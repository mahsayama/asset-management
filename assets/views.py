import csv  # <--- WAJIB ADA (Buat fitur Export CSV)
import openpyxl # (Opsional, kalau gak dipake hapus aja biar bersih)
from django.http import HttpResponse 
from django.shortcuts import render, redirect, get_object_or_404
from django.db.models import Count, Q, Sum 
from django.contrib.auth.decorators import login_required
from django.db import transaction 
from django.core.paginator import Paginator
from .models import Asset, AssetHistory, Kategori, Lokasi
from .forms import AssetForm

# --- 1. HALAMAN LIST (DASHBOARD) ---
@login_required
def asset_list(request):
    # 1. Ambil Parameter Filter dari URL
    query = request.GET.get('q')
    category_id = request.GET.get('category')
    location_id = request.GET.get('location')
    status_filter = request.GET.get('status')
    
    # Pagination setup
    per_page = request.GET.get('per_page', 10)

    # 2. Query Dasar
    assets = Asset.objects.all().order_by('-created_at')

    # 3. Logika Filter
    if query:
        assets = assets.filter(
            Q(name__icontains=query) | 
            Q(serial_number__icontains=query) |
            Q(barcode_id__icontains=query) |
            Q(current_user__icontains=query)
        )

    # Filter Kategori (Pake ID karena ForeignKey)
    if category_id:
        assets = assets.filter(kategori_id=category_id)

    # Filter Lokasi (Pake ID karena ForeignKey)
    if location_id:
        assets = assets.filter(lokasi_id=location_id)

    # Filter Status
    if status_filter:
        assets = assets.filter(status=status_filter)

    # 4. Pagination
    paginator = Paginator(assets, per_page)
    page_number = request.GET.get('page')
    page_obj = paginator.get_page(page_number)

    # 5. Context
    context = {
        'assets': page_obj,
        'query': query,
        'per_page': per_page,
        'category_filter': int(category_id) if category_id else '',
        'location_filter': int(location_id) if location_id else '',
        'status_filter': status_filter,
        'kategori_list': Kategori.objects.all(),
        'lokasi_list': Lokasi.objects.all(),
        'status_choices': Asset.STATUS_CHOICES,
    }

    # --- FIX ERROR HTMX DISINI ---
    # Kita cek header 'HX-Request' secara manual
    if request.headers.get('HX-Request'):
        return render(request, 'assets/asset_table_partial.html', context)
        
    return render(request, 'assets/asset_list.html', context)


# --- 2. DETAIL ASET ---
@login_required
def asset_detail(request, pk):
    asset = get_object_or_404(Asset, pk=pk)
    history_list = asset.history.all().order_by('-event_date') 
    
    return render(request, 'assets/asset_detail.html', {
        'asset': asset,
        'history_list': history_list
    })


# --- 3. TAMBAH ASET ---
@login_required
def asset_create(request):
    if request.method == 'POST':
        form = AssetForm(request.POST)
        if form.is_valid():
            asset = form.save()
            # Catat history pembuatan awal
            AssetHistory.objects.create(
                asset=asset,
                changed_by=request.user,
                description="Aset baru ditambahkan ke sistem."
            )
            return redirect('asset_list')
    else:
        form = AssetForm()

    return render(request, 'assets/asset_form.html', {'form': form})    


# --- 4. EDIT ASET (AUTO HANDOVER) ---
@login_required
def asset_update(request, pk):
    asset_obj = get_object_or_404(Asset, pk=pk)
    
    # Snapshot data lama
    old_current_user = asset_obj.current_user 
    old_current_dept = asset_obj.current_dept

    if request.method == 'POST':
        form = AssetForm(request.POST, request.FILES, instance=asset_obj)
        
        if form.is_valid():
            if form.has_changed():
                changes = []
                
                # Cek Handover User
                if 'current_user' in form.changed_data:
                    temp_asset = form.save(commit=False)
                    temp_asset.prev_user = old_current_user
                    temp_asset.prev_dept = old_current_dept
                    changes.append(f"Handover: '{old_current_user}' -> User Sebelumnya.")
                
                # Cek Perubahan Lain
                for field in form.changed_data:
                    field_label = form.fields[field].label or field
                    new_value = form.cleaned_data.get(field)
                    
                    if field == 'status':
                         new_value = dict(Asset.STATUS_CHOICES).get(new_value, new_value)
                    
                    if new_value is None: new_value = "-"
                    
                    if field != 'current_user' and field != 'prev_user': 
                        changes.append(f"{field_label} diubah jadi '{new_value}'")
                
                with transaction.atomic():
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


# --- 5. HAPUS ASET ---
@login_required
def asset_delete(request, pk):
    asset = get_object_or_404(Asset, pk=pk)
    
    if request.method == 'POST':
        asset.delete()
        return redirect('asset_list')
    
    return render(request, 'assets/asset_confirm_delete.html', {'asset': asset})


# --- 6. DASHBOARD ---
@login_required
def dashboard(request):
    # 1. Hitung Total Aset
    total_asset = Asset.objects.count()

    # 2. Data Grafik (Status & Kategori) - INI KODINGAN LAMA (TETAP DIPAKE)
    status_data = Asset.objects.values('status').annotate(total=Count('status'))
    status_labels = [item['status'] for item in status_data]
    status_counts = [item['total'] for item in status_data]

    category_data = Asset.objects.values('kategori__nama').annotate(total=Count('id')).order_by('-total')
    cat_labels = [item['kategori__nama'] if item['kategori__nama'] else 'Tanpa Kategori' for item in category_data]
    cat_counts = [item['total'] for item in category_data]

    # --- 3. TAMBAHAN BARU: AMBIL 5 ASET TERBARU ---
    # order_by('-created_at') artinya urutkan dari yang paling baru
    # [:5] artinya cuma ambil 5 biji
    recent_assets = Asset.objects.select_related('kategori', 'lokasi').order_by('-created_at')[:5]

    context = {
        'total_asset': total_asset,
        'status_labels': status_labels,
        'status_counts': status_counts,
        'cat_labels': cat_labels,
        'cat_counts': cat_counts,
        'recent_assets': recent_assets, # <-- Jangan lupa masukin ke context
    }

    return render(request, 'dashboard.html', context)

# --- 8. SETTINGS ---
@login_required
def settings_view(request):
    # Logic nambah data pas tombol 'Tambah' diklik
    if request.method == "POST":
        tipe = request.POST.get('type') # Nangkep ini kategori atau lokasi
        nama = request.POST.get('nama') # Nangkep text yang diketik
        
        if tipe == 'kategori' and nama:
            # Cek biar ga duplikat
            if not Kategori.objects.filter(nama__iexact=nama).exists():
                Kategori.objects.create(nama=nama)
        
        elif tipe == 'lokasi' and nama:
            # Cek biar ga duplikat
            if not Lokasi.objects.filter(nama__iexact=nama).exists():
                Lokasi.objects.create(nama=nama)
            
        # Refresh halaman biar inputan kosong lagi
        return redirect('settings')

    # Kirim data list yang udah ada ke HTML
    return render(request, 'assets/settings.html', {
        'user': request.user,
        'kategoris': Kategori.objects.all().order_by('nama'),
        'lokasis': Lokasi.objects.all().order_by('nama'),
    })

@login_required
def delete_master_data(request, tipe, pk):
    if request.method == "POST":
        if tipe == 'kategori':
            data = get_object_or_404(Kategori, pk=pk)
            data.delete()
        elif tipe == 'lokasi':
            data = get_object_or_404(Lokasi, pk=pk)
            data.delete()
            
    return redirect('settings')

# --- 9. EXPORT EXCEL ---
@login_required
def export_assets_csv(request):
    # 1. Setup Response Browser (Biar browser tau ini file download)
    response = HttpResponse(content_type='text/csv')
    response['Content-Disposition'] = 'attachment; filename="data_aset_it.csv"'

    # 2. Bikin CSV Writer
    writer = csv.writer(response)
    
    # 3. Tulis Header (Judul Kolom)
    writer.writerow(['Nama Aset', 'Serial Number', 'Barcode', 'Kategori', 'Lokasi', 'Pengguna', 'Status', 'Harga', 'Tanggal Beli'])

    # 4. Ambil Data dari Database
    assets = Asset.objects.all().order_by('-created_at')

    # 5. Loop dan Tulis Baris Data
    for asset in assets:
        writer.writerow([
            asset.name,
            asset.serial_number,
            asset.barcode_id,
            asset.kategori.nama if asset.kategori else '-',
            asset.lokasi.nama if asset.lokasi else '-',
            asset.current_user if asset.current_user else '-',
            asset.get_status_display(), # Ambil teks status (Tersedia/Rusak), bukan kodenya
            asset.price,
            asset.purchase_date,
        ])

    return response

@login_required
def reports_view(request):
    # 1. Total Valuasi Aset (Jumlahin semua field price)
    # Hasilnya: {'price__sum': 50000000}
    total_value_data = Asset.objects.aggregate(Sum('price'))
    total_value = total_value_data['price__sum'] or 0 # Kalau kosong kasih 0

    # 2. Laporan per Kategori (Nama, Jumlah Unit, Total Harga per Kategori)
    category_report = Asset.objects.values('kategori__nama').annotate(
        total_unit=Count('id'),
        total_price=Sum('price')
    ).order_by('-total_price') # Urutkan dari yang paling mahal totalnya

    # 3. Laporan per Lokasi (Nama, Jumlah Unit)
    location_report = Asset.objects.values('lokasi__nama').annotate(
        total_unit=Count('id')
    ).order_by('-total_unit')

    context = {
        'total_value': total_value,
        'category_report': category_report,
        'location_report': location_report,
    }

    return render(request, 'reports.html', context)