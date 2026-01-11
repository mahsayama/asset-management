from django.contrib import admin
from django.urls import path
from django.contrib.auth import views as auth_views
from assets import views

# Import views dari assets (JANGAN LUPA 'dashboard' dimasukin sini)
from assets.views import (
    dashboard,      # <--- INI WAJIB ADA (Fungsi baru kita)
    asset_list, 
    asset_create, 
    asset_update, 
    asset_delete, 
    export_excel,
    asset_detail,
    reports_view,  # <--- Tambah ini
    settings_view  # <--- Tambah ini
)

urlpatterns = [
    path('admin/', admin.site.urls),
    
    # --- JALUR LOGIN & LOGOUT ---
    path('login/', auth_views.LoginView.as_view(template_name='assets/login.html'), name='login'),
    path('logout/', auth_views.LogoutView.as_view(next_page='login'), name='logout'),
    
    # --- STRUKTUR MENU BARU ---
    
    # 1. Dashboard (Halaman Utama saat buka web)
    path('', dashboard, name='dashboard'), 
    
    # 2. Inventory (List Aset dipindah ke /inventory/)
    path('inventory/', asset_list, name='asset_list'), 

    path('reports/', reports_view, name='reports'),
    path('settings/', settings_view, name='settings'),

    # --- CRUD ACTIONS ---
    path('tambah/', asset_create, name='asset_create'),
    path('edit/<int:pk>/', asset_update, name='asset_update'),
    path('hapus/<int:pk>/', asset_delete, name='asset_delete'),
    path('asset/<int:pk>/detail/', asset_detail, name='asset_detail'),
    
    # --- FITUR TAMBAHAN ---
    path('export/', export_excel, name='export_excel'),

    path('settings/delete/<str:tipe>/<int:pk>/', views.delete_master_data, name='delete_master_data'),

]