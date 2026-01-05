import openpyxl # <--- Import alat barunya
from django.http import HttpResponse # <--- Buat ngirim file ke browser
from django.shortcuts import render, redirect, get_object_or_404
from django.db.models import Count # <--- Pastikan import ini ada
from django.db import transaction  # <--- INI BIASANYA KETINGGALAN
from .models import Asset, AssetHistory # <--- PASTIIN AssetHistory UDAH DITULIS
from .forms import AssetForm
# --- 1. Import Gembok ---
from django.contrib.auth.decorators import login_required 
from django.db.models import Q  # <--- 1. WAJIB IMPORT INI (Huruf Q Besar)


# --- 2. Pasang Gembok di Atas Setiap Fungsi ---

@login_required
def asset_list(request):
    # Ambil kata kunci dari kotak pencarian (kalau ada)
    query = request.GET.get('q')

    if query:
        # Kalau user lagi nyari sesuatu:
        # Cari di Nama ATAU Serial Number ATAU Barcode ID
        # icontains = case insensitive (huruf besar/kecil dianggap sama)
        assets = Asset.objects.filter(
            Q(name__icontains=query) | 
            Q(serial_number__icontains=query) |
            Q(barcode_id__icontains=query) |
            Q(current_user__icontains=query) |
            Q(prev_user__icontains=query)
        ).order_by('-created_at')
    else:
        # Kalau gak nyari apa-apa, ambil semua kayak biasa
        assets = Asset.objects.all().order_by('-created_at')

# --- LOGIKA HITUNG STATUS (TAMBAHKAN INI) ---
    # Kita hitung berdasarkan filter status
    # Sesuaikan teks 'DIPAKAI', 'RUSAK', 'HILANG' dengan isi database lo
    context = {
        'assets': assets,
        'query': query,
        'total_count': assets.count(),
        'dipakai_count': assets.filter(status='DIPAKAI').count(),
        'rusak_count': assets.filter(status='RUSAK').count(),
        'hilang_count': assets.filter(status='HILANG').count(),
    }
    
    return render(request, 'assets/asset_list.html', context)

    context = {
        'assets': assets,
        'query': query # Kirim balik kata kuncinya biar tetep nongol di kotak search
    }
    return render(request, 'assets/asset_list.html', context)

    # --- FUNGSI BARU ---
@login_required
def asset_create(request):
    if request.method == 'POST':
        # Kalau user klik tombol Save
        form = AssetForm(request.POST)
        if form.is_valid():
            form.save() # Simpan ke database
            return redirect('asset_list') # Balik ke halaman list
    else:
        # Kalau user baru buka halaman (form kosong)
        form = AssetForm()

    return render(request, 'assets/asset_form.html', {'form': form})    

        # --- FUNGSI EDIT ---
@login_required
def asset_update(request, pk):
    # 1. Ambil data asli dari database (Sebelum diedit)
    asset_obj = get_object_or_404(Asset, pk=pk)
    
    # Simpan user lama di variabel sementara
    old_current_user = asset_obj.current_user 
    old_current_dept = asset_obj.current_dept

    if request.method == 'POST':
        form = AssetForm(request.POST, request.FILES, instance=asset_obj)
        
        if form.is_valid():
            if form.has_changed():
                changes = []
                
                # --- LOGIKA OTOMATIS PINDAH USER (AUTO HANDOVER) ---
                # Cek apakah field 'current_user' ada dalam daftar perubahan?
                if 'current_user' in form.changed_data:
                    # Ambil instance tapi jangan save ke DB dulu
                    temp_asset = form.save(commit=False)
                    
                    # Pindahkan user lama ke kolom 'prev_user'
                    temp_asset.prev_user = old_current_user
                    # Pindahkan dept lama ke kolom 'prev_dept' (opsional)
                    temp_asset.prev_dept = old_current_dept
                    
                    # Tambahin catatan ke changes biar masuk history
                    changes.append(f"Handover aset: '{old_current_user}' pindah jadi User Sebelumnya.")
                
                # --- LOGIKA DETEKSI PERUBAHAN LAIN ---
                for field in form.changed_data:
                    field_label = form.fields[field].label or field
                    new_value = form.cleaned_data.get(field)
                    
                    if field == 'status':
                         new_value = dict(Asset.STATUS_CHOICES).get(new_value, new_value)
                    
                    if new_value is None:
                        new_value = "-"
                        
                    changes.append(f"{field_label} diubah menjadi '{new_value}'")
                
                with transaction.atomic():
                    # Save aset yang sudah diupdate (termasuk prev_user otomatis tadi)
                    asset = form.save()
                    
                    AssetHistory.objects.create(
                        asset=asset,
                        changed_by=request.user,
                        description=". ".join(changes)
                    )
            else:
                form.save()
                
            return redirect('asset_list')
    else:
        form = AssetForm(instance=asset_obj)
        
    return render(request, 'assets/asset_form.html', {'form': form})

# --- FUNGSI HAPUS ---
@login_required
def asset_delete(request, pk):
    asset = get_object_or_404(Asset, pk=pk)
    
    if request.method == 'POST':
        # Hapus asetnya
        asset.delete()
        return redirect('asset_list')
    
    # Tampilkan halaman konfirmasi sebelum hapus
    return render(request, 'assets/asset_confirm_delete.html', {'asset': asset})
    
    return render(request, 'assets/asset_form.html', {'form': form})

# --- FUNGSI EXPORT TO EXCEL---
@login_required
def export_excel(request):
    # 1. Setup Workbook (Buku Kerja Excel)
    response = HttpResponse(content_type='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
    response['Content-Disposition'] = 'attachment; filename="Laporan_Aset.xlsx"'

    workbook = openpyxl.Workbook()
    worksheet = workbook.active
    worksheet.title = 'Data Aset'

    # 2. Bikin Header (Judul Kolom)
    headers = ['Nama Aset', 'Barcode ID', 'Serial Number', 'Status', 'Tanggal Beli', 'Harga', 'Keterangan']
    worksheet.append(headers)

    # (Opsional) Tebalin huruf Header biar ganteng
    for cell in worksheet[1]:
        cell.font = openpyxl.styles.Font(bold=True)

    # 3. Ambil Data dari Database
    # (Kita ambil semua data, kalau mau filter bisa disesuaikan nanti)
    assets = Asset.objects.all().order_by('-created_at')

    # 4. Masukin Data baris per baris
    for asset in assets:
        worksheet.append([
            asset.name,
            asset.barcode_id,
            asset.serial_number,
            asset.get_status_display(), # Biar muncul "Tersedia" bukan "TERSEDIA"
            asset.purchase_date,
            asset.price,
            asset.note
        ])

    # 5. Simpan dan kirim ke browser
    workbook.save(response)
    return response

@login_required
def asset_detail(request, pk):
    asset = get_object_or_404(Asset, pk=pk)
    history_list = asset.history.all() # Ambil semua history aset ini
    
    return render(request, 'assets/asset_detail.html', {
        'asset': asset,
        'history_list': history_list
    })