from django.contrib import admin
from django.urls import path
from django.contrib.auth import views as auth_views

# Import semua views secara eksplisit biar rapi
from assets.views import (
    dashboard,
    asset_list,
    asset_create,
    asset_update,
    asset_delete,
    asset_detail,
    reports_view,
    settings_view,
    delete_master_data, # Tambahin ini biar gak perlu nulis 'views.delete_master_data'
    export_assets_csv,   # Pake yang ini, JANGAN export_excel
    import_assets_excel,
    download_excel_template
)

urlpatterns = [
    path('admin/', admin.site.urls),
    
    # --- JALUR LOGIN & LOGOUT ---
    path('login/', auth_views.LoginView.as_view(template_name='assets/login.html'), name='login'),
    path('logout/', auth_views.LogoutView.as_view(next_page='login'), name='logout'),
    
    # --- MENU UTAMA ---
    path('', dashboard, name='dashboard'), 
    path('inventory/', asset_list, name='asset_list'), 
    path('reports/', reports_view, name='reports'),
    path('settings/', settings_view, name='settings'),

    # --- CRUD ACTIONS (ASET) ---
    path('tambah/', asset_create, name='asset_create'),
    path('edit/<int:pk>/', asset_update, name='asset_update'),
    path('hapus/<int:pk>/', asset_delete, name='asset_delete'),
    path('asset/<int:pk>/detail/', asset_detail, name='asset_detail'),
    
    # --- FITUR TAMBAHAN ---
    # Delete Kategori/Lokasi (Settings)
    path('settings/delete/<str:tipe>/<int:pk>/', delete_master_data, name='delete_master_data'),

    # Export CSV (Ini yang bener)
    path('reports/export/csv/', export_assets_csv, name='export_assets_csv'),

    # Untuk import data
    path('inventory/import/', import_assets_excel, name='import_assets_excel'),
    path('inventory/import/template/', download_excel_template, name='download_excel_template'),
]