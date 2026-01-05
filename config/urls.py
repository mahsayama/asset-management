from django.contrib import admin
from django.urls import path
from django.contrib.auth import views as auth_views

# Import SEMUA fungsi dari views di sini (termasuk asset_detail)
from assets.views import (
    asset_list, 
    asset_create, 
    asset_update, 
    asset_delete, 
    export_excel,
    asset_detail  # <--- Jangan lupa koma di baris sebelumnya
)

urlpatterns = [
    path('admin/', admin.site.urls),

    # --- JALUR LOGIN & LOGOUT ---
    path('login/', auth_views.LoginView.as_view(template_name='assets/login.html'), name='login'),
    path('logout/', auth_views.LogoutView.as_view(next_page='login'), name='logout'),
    
    # --- DASHBOARD & CRUD ---
    path('', asset_list, name='asset_list'), 
    path('tambah/', asset_create, name='asset_create'),

    # <int:pk> artinya menangkap ID aset
    path('edit/<int:pk>/', asset_update, name='asset_update'),
    path('hapus/<int:pk>/', asset_delete, name='asset_delete'),
    
    # --- FITUR TAMBAHAN ---
    path('export/', export_excel, name='export_excel'),
    
    # Jalur Detail History (Perhatikan: sudah tidak pakai 'views.' lagi)
    path('asset/<int:pk>/detail/', asset_detail, name='asset_detail'),
]