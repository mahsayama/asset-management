import openpyxl 
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
    total_assets = Asset.objects.count()
    total_value = Asset.objects.aggregate(Sum('price'))['price__sum'] or 0
    
    context = {
        'total_assets': total_assets,
        'total_value': total_value,
        'recent_assets': Asset.objects.all().order_by('-created_at')[:5],
        'tersedia_count': Asset.objects.filter(status='TERSEDIA').count(),
        'dipakai_count': Asset.objects.filter(status='DIPAKAI').count(),
        'rusak_count': Asset.objects.filter(status='RUSAK').count(),
    }
    return render(request, 'assets/dashboard.html', context)


# --- 7. REPORTS (FIXED LOGIC) ---
@login_required
def reports_view(request):
    # FIX: Pake __nama untuk ambil nama kategori, bukan ID-nya
    # values('kategori__nama') artinya group by kolom nama di tabel Kategori
    category_data = Asset.objects.values('kategori__nama').annotate(total=Count('id'))
    
    # Status data tetep sama
    status_data = Asset.objects.values('status').annotate(total=Count('id'))
    
    # Kita perlu mapping biar template reports.html ga bingung
    # Ubah format biar sesuai template: [{'kategori': 'Laptop', 'total': 5}, ...]
    cleaned_category_data = []
    for item in category_data:
        nama = item['kategori__nama'] if item['kategori__nama'] else 'Uncategorized'
        cleaned_category_data.append({'kategori': nama, 'total': item['total']})

    return render(request, 'assets/reports.html', {
        'category_data': cleaned_category_data,
        'status_data': status_data,
    })


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
def export_excel(request):
    response = HttpResponse(content_type='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
    response['Content-Disposition'] = 'attachment; filename="Laporan_Aset.xlsx"'

    workbook = openpyxl.Workbook()
    worksheet = workbook.active
    worksheet.title = 'Data Aset'

    headers = ['Nama Aset', 'Barcode ID', 'Serial Number', 'Kategori', 'Lokasi', 'Status', 'Tanggal Beli', 'Harga', 'User', 'Keterangan']
    worksheet.append(headers)

    for cell in worksheet[1]:
        cell.font = openpyxl.styles.Font(bold=True)

    assets = Asset.objects.all().order_by('-created_at')

    for asset in assets:
        # Handle ForeignKey biar yang muncul Namanya, bukan ID
        kategori_nama = asset.kategori.nama if asset.kategori else "-"
        lokasi_nama = asset.lokasi.nama if asset.lokasi else "-"

        worksheet.append([
            asset.name,
            asset.barcode_id,
            asset.serial_number,
            kategori_nama, # Fix export
            lokasi_nama,   # Fix export
            asset.get_status_display(),
            asset.purchase_date,
            asset.price,
            asset.current_user,
            asset.note
        ])

    workbook.save(response)
    return response