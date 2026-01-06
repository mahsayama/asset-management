import openpyxl 
from django.http import HttpResponse 
from django.shortcuts import render, redirect, get_object_or_404
from django.db.models import Count, Q 
from django.db import transaction 
from django.core.paginator import Paginator
from django.contrib.auth.decorators import login_required
from .models import Asset, AssetHistory
from .forms import AssetForm

# --- 1. HALAMAN LIST (DASHBOARD) ---
@login_required
def asset_list(request):
    # --- A. AMBIL PARAMETER DARI URL ---
    query = request.GET.get('q')
    per_page = request.GET.get('per_page', '10')
    category_filter = request.GET.get('category')
    location_filter = request.GET.get('location')
    status_filter = request.GET.get('status')

    # --- B. QUERY DATABASE ---
    assets_list = Asset.objects.all().order_by('-created_at')

    # Filter Search Teks
    if query:
        assets_list = assets_list.filter(
            Q(name__icontains=query) |
            Q(serial_number__icontains=query) |
            Q(barcode_id__icontains=query) |
            Q(current_user__icontains=query)
        )

    # Filter Dropdown
    if category_filter:
        assets_list = assets_list.filter(kategori=category_filter)
    
    if location_filter:
        assets_list = assets_list.filter(lokasi=location_filter)

    if status_filter:
        assets_list = assets_list.filter(status=status_filter)

    # --- C. PAGINATION ---
    paginator = Paginator(assets_list, per_page)
    page_number = request.GET.get('page')
    page_obj = paginator.get_page(page_number)

    # --- D. CONTEXT DATA ---
    context = {
        'assets': page_obj,
        'query': query,
        'per_page': per_page,
        'category_filter': category_filter,
        'location_filter': location_filter,
        'status_filter': status_filter,
        'kategori_choices': Asset.KATEGORI_CHOICES,
        'lokasi_choices': Asset.LOKASI_CHOICES,
        'status_choices': Asset.STATUS_CHOICES,
        
        # Statistik Card
        'total_count': Asset.objects.count(),
        'tersedia_count': Asset.objects.filter(status='TERSEDIA').count(),
        'dipakai_count': Asset.objects.filter(status='DIPAKAI').count(),
        'rusak_count': Asset.objects.filter(status='RUSAK').count(),
        'hilang_count': Asset.objects.filter(status='HILANG').count(),
    }

    # --- E. LOGIC HTMX ---
    # Kalau request dari HTMX, render tabelnya doang (Partial)
    if request.headers.get('HX-Request'):
        return render(request, 'assets/asset_table_partial.html', context)

    # Kalau refresh biasa, render halaman full
    return render(request, 'assets/asset_list.html', context)


# --- 2. DETAIL ASET ---
@login_required
def asset_detail(request, pk):
    asset = get_object_or_404(Asset, pk=pk)
    
    # PERBAIKAN DI SINI:
    # Ganti '-changed_at' jadi '-event_date'
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
                    
                    # Jangan catat user lagi disini biar ga double log
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


# --- 6. EXPORT EXCEL ---
@login_required
def export_excel(request):
    response = HttpResponse(content_type='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
    response['Content-Disposition'] = 'attachment; filename="Laporan_Aset.xlsx"'

    workbook = openpyxl.Workbook()
    worksheet = workbook.active
    worksheet.title = 'Data Aset'

    # Header
    headers = ['Nama Aset', 'Barcode ID', 'Serial Number', 'Status', 'Tanggal Beli', 'Harga', 'User', 'Keterangan']
    worksheet.append(headers)

    # Styling Header Bold
    for cell in worksheet[1]:
        cell.font = openpyxl.styles.Font(bold=True)

    # Data
    assets = Asset.objects.all().order_by('-created_at')

    for asset in assets:
        worksheet.append([
            asset.name,
            asset.barcode_id,
            asset.serial_number,
            asset.get_status_display(),
            asset.purchase_date,
            asset.price,
            asset.current_user,
            asset.note
        ])

    workbook.save(response)
    return response