import csv
import json  # <--- JANGAN LUPA INI
from django.shortcuts import render, redirect, get_object_or_404
from django.http import HttpResponse
from django.db.models import Q, Sum, Count
from django.contrib.auth.decorators import login_required
from django.db import transaction
from django.core.paginator import Paginator
from django.contrib import messages

# Import model & form
from .models import Asset, AssetHistory, Kategori, Lokasi
from .forms import AssetForm

# 1. DASHBOARD
@login_required
def dashboard(request):
    total_asset = Asset.objects.count()
    status_data = Asset.objects.values('status').annotate(total=Count('status'))
    status_labels = [item['status'] for item in status_data]
    status_counts = [item['total'] for item in status_data]

    category_data = Asset.objects.values('kategori__nama').annotate(total=Count('id')).order_by('-total')
    cat_labels = [item['kategori__nama'] if item['kategori__nama'] else 'Tanpa Kategori' for item in category_data]
    cat_counts = [item['total'] for item in category_data]

    recent_assets = Asset.objects.select_related('kategori', 'lokasi').order_by('-created_at')[:5]

    context = {
        'total_asset': total_asset,
        'status_labels': status_labels,
        'status_counts': status_counts,
        'cat_labels': cat_labels,
        'cat_counts': cat_counts,
        'recent_assets': recent_assets,
    }
    return render(request, 'dashboard.html', context)

# 2. INVENTORY LIST (FIXED: SEARCH BARCODE)
@login_required
def asset_list(request):
    # Ambil parameter
    query = request.GET.get('q', '').strip()
    category_id = request.GET.get('category', '').strip()
    location_id = request.GET.get('location', '').strip()
    status_filter = request.GET.get('status', '').strip()
    sort_by = request.GET.get('sort', '-created_at')
    
    try:
        page_number = int(request.GET.get('page', 1))
    except (ValueError, TypeError):
        page_number = 1

    # Base Queryset
    assets_queryset = Asset.objects.select_related('kategori', 'lokasi').all()

    # --- JURUS FILTERING ---
    if query:
        assets_queryset = assets_queryset.filter(
            Q(name__icontains=query) | 
            Q(serial_number__icontains=query) |
            Q(barcode_id__icontains=query) | # <--- INI YANG TADI KETINGGALAN
            Q(current_user__icontains=query) |
            Q(current_dept__icontains=query) # Bonus: Biar bisa cari nama divisi juga
        )
    
    if category_id:
        assets_queryset = assets_queryset.filter(kategori_id=category_id)
    
    if location_id:
        assets_queryset = assets_queryset.filter(lokasi_id=location_id)
        
    if status_filter:
        assets_queryset = assets_queryset.filter(status=status_filter)

    # --- JURUS SORTING ---
    assets_queryset = assets_queryset.order_by(sort_by)

    paginator = Paginator(assets_queryset, 10)
    page_obj = paginator.get_page(page_number)

    context = {
        'assets': page_obj,
        'query': query,
        'category_filter': category_id,
        'location_filter': location_id,
        'status_filter': status_filter,
        'sort_current': sort_by,
        'kategori_list': Kategori.objects.all(),
        'lokasi_list': Lokasi.objects.all(),
        'status_choices': Asset.STATUS_CHOICES,
    }

    if request.headers.get('HX-Request') and request.headers.get('HX-Target') == 'asset-table-body':
        return render(request, 'assets/asset_table_partial.html', context)
    
    return render(request, 'assets/asset_list.html', context)

# 3. CREATE
@login_required
def asset_create(request):
    if request.method == 'POST':
        form = AssetForm(request.POST, request.FILES)
        if form.is_valid():
            with transaction.atomic():
                asset = form.save()
                AssetHistory.objects.create(
                    asset=asset,
                    changed_by=request.user,
                    description="Aset baru didaftarkan ke sistem."
                )
            # FIX: Pindah ke SINI (sebelum return redirect)
            messages.success(request, "Mantap! Aset berhasil ditambahkan.") 
            return redirect('asset_list')
    else:
        form = AssetForm()
    return render(request, 'assets/asset_form.html', {'form': form, 'title': 'Tambah Aset'})

# 4. UPDATE
@login_required
def asset_update(request, pk):
    asset = get_object_or_404(Asset, pk=pk)
    
    if request.method == 'POST':
        form = AssetForm(request.POST, request.FILES, instance=asset) 
        if form.is_valid():
            with transaction.atomic():
                if form.has_changed():
                    instance = form.save(commit=False) 
                    changes = [] 
                    
                    # --- 1. LOGIC SPESIAL: PERGESERAN USER (Current -> Prev) ---
                    if 'current_user' in form.changed_data:
                        old_curr_user = form.initial.get('current_user')
                        old_curr_dept = form.initial.get('current_dept')
                        
                        old_prev_user = asset.prev_user
                        old_prev_dept = asset.prev_dept

                        # A. UPDATE DATABASE
                        instance.prev_user = old_curr_user
                        instance.prev_dept = old_curr_dept

                        # B. UPDATE HISTORY
                        changes.append(f"Pengguna Sebelumnya berubah dari '{old_prev_user or '-'}' menjadi '{old_curr_user or '-'}'")
                        
                        if old_curr_dept != old_prev_dept:
                             changes.append(f"Divisi Sebelumnya berubah dari '{old_prev_dept or '-'}' menjadi '{old_curr_dept or '-'}'")

                    # --- 2. LOGIC STANDARD ---
                    for field_name in form.changed_data:
                        if field_name in ['prev_user', 'prev_dept']:
                            continue
                            
                        field = form.fields[field_name]
                        label = field.label or field_name.replace('_', ' ').title()
                        
                        old_raw = form.initial.get(field_name)
                        new_raw = form.cleaned_data.get(field_name)

                        if hasattr(field, 'choices') and field.choices:
                            choices_dict = dict(field.choices)
                            old_val = choices_dict.get(old_raw, old_raw)
                            new_val = choices_dict.get(new_raw, new_raw)
                        else:
                            old_val = old_raw
                            new_val = new_raw

                        if old_val is None or old_val == '': old_val = '-'
                        if new_val is None or new_val == '': new_val = '-'
                        if hasattr(new_val, '__str__'): new_val = str(new_val)
                        if hasattr(old_val, '__str__'): old_val = str(old_val)

                        changes.append(f"{label} berubah dari '{old_val}' menjadi '{new_val}'")
                    
                    desc = "; ".join(changes) + "."
                    
                    instance.save()
                    form.save_m2m()
                    
                    AssetHistory.objects.create(
                        asset=instance,
                        changed_by=request.user,
                        description=desc
                    )
                else:
                    form.save()
            
            # Tambahan: Kasih Toast Success juga pas update
            messages.success(request, "Data aset berhasil diperbarui.")        
            return redirect('asset_list')
            
    else:
        form = AssetForm(instance=asset)
    
    return render(request, 'assets/asset_form.html', {
        'form': form, 
        'title': 'Edit Aset',
        'asset': asset 
    })

# 5. DETAIL
@login_required
def asset_detail(request, pk):
    asset = get_object_or_404(Asset.objects.select_related('kategori', 'lokasi'), pk=pk)
    histories = asset.history.all().order_by('-event_date') 
    
    return render(request, 'assets/asset_detail.html', {
        'asset': asset,
        'histories': histories,
    })

# 6. DELETE (FIXED: Gak Ada Double Message)
@login_required
def asset_delete(request, pk):
    asset = get_object_or_404(Asset, pk=pk)
    if request.method == 'POST':
        asset.delete()
        
        # --- JURUS 1: Kalau HTMX, Pakai Trigger Aja (Jangan pake messages.error) ---
        if request.headers.get('HX-Request'):
            response = HttpResponse(status=204)
            trigger_data = {
                'closeModal': True,
                'refreshTable': True,
                'showToast': {
                    'message': 'Aset telah berhasil dihapus.',
                    'tags': 'danger'
                }
            }
            response['HX-Trigger'] = json.dumps(trigger_data)
            return response
            
        # --- JURUS 2: Kalau BUKAN HTMX (Fallback), Baru pake messages ---
        messages.error(request, "Aset telah dihapus.")
        return redirect('asset_list')
        
    return render(request, 'assets/asset_confirm_delete.html', {'asset': asset})
# 7. REPORTS
@login_required
def reports_view(request):
    total_value_data = Asset.objects.aggregate(Sum('price'))
    total_value = total_value_data['price__sum'] or 0 

    category_report = Asset.objects.values('kategori__nama').annotate(
        total_unit=Count('id'),
        total_price=Sum('price')
    ).order_by('-total_price')

    location_report = Asset.objects.values('lokasi__nama').annotate(
        total_unit=Count('id')
    ).order_by('-total_unit')

    context = {
        'total_value': total_value,
        'category_report': category_report,
        'location_report': location_report,
    }
    return render(request, 'reports.html', context)

# 8. EXPORT CSV
@login_required
def export_assets_csv(request):
    if request.headers.get('HX-Request'):
        response = HttpResponse()
        response['HX-Redirect'] = request.get_full_path()
        return response

    response = HttpResponse(content_type='text/csv')
    response['Content-Disposition'] = 'attachment; filename="data_aset_it.csv"'

    writer = csv.writer(response)
    writer.writerow(['Nama Aset', 'Serial Number', 'Barcode', 'Kategori', 'Lokasi', 'User', 'Status', 'Harga'])

    assets = Asset.objects.select_related('kategori', 'lokasi').all().order_by('-created_at')

    for a in assets:
        writer.writerow([
            a.name, a.serial_number, a.barcode_id,
            a.kategori.nama if a.kategori else '-',
            a.lokasi.nama if a.lokasi else '-',
            a.current_user if a.current_user else '-',
            a.get_status_display(), a.price
        ])
    return response

# 9. SETTINGS & MASTER DATA
@login_required
def settings_view(request):
    if request.method == "POST":
        tipe = request.POST.get('type')
        nama = request.POST.get('nama')
        if tipe == 'kategori' and nama:
            Kategori.objects.get_or_create(nama=nama)
        elif tipe == 'lokasi' and nama:
            Lokasi.objects.get_or_create(nama=nama)
        return redirect('settings')

    return render(request, 'assets/settings.html', {
        'kategoris': Kategori.objects.all().order_by('nama'),
        'lokasis': Lokasi.objects.all().order_by('nama'),
    })

@login_required
def delete_master_data(request, tipe, pk):
    if request.method == "POST":
        if tipe == 'kategori':
            get_object_or_404(Kategori, pk=pk).delete()
        elif tipe == 'lokasi':
            get_object_or_404(Lokasi, pk=pk).delete()
    return redirect('settings')