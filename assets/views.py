import openpyxl # <--- Import alat barunya
from django.http import HttpResponse # <--- Buat ngirim file ke browser
from django.shortcuts import render, redirect, get_object_or_404
from .models import Asset
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
    # Cari aset berdasarkan ID (pk), kalau gak ada kasih error 404
    asset = get_object_or_404(Asset, pk=pk)
    
    if request.method == 'POST':
        # Masukin data baru ke form, TAPI load juga data lamanya (instance=asset)
        form = AssetForm(request.POST, instance=asset)
        if form.is_valid():
            form.save()
            return redirect('asset_list')
    else:
        # Kalau baru buka, isi form dengan data lama
        form = AssetForm(instance=asset)
        
    # Kita pakai file HTML yang sama kayak waktu 'tambah' (reusable!)
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