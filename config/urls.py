from django.contrib import admin
from django.urls import path
# Import views bawaan Django buat login/logout
from django.contrib.auth import views as auth_views
# Import 2 fungsi baru: asset_update & asset_delete
from assets.views import asset_list, asset_create, asset_update, asset_delete
# Import fungsi baru 'export_excel'
from assets.views import asset_list, asset_create, asset_update, asset_delete, export_excel

urlpatterns = [
    path('admin/', admin.site.urls),

    # --- JALUR LOGIN & LOGOUT BARU ---
    # Kita arahkan ke template 'assets/login.html' (nanti kita buat)
    path('login/', auth_views.LoginView.as_view(template_name='assets/login.html'), name='login'),
    # Kalau logout, lempar ke next_page='login'
    path('logout/', auth_views.LogoutView.as_view(next_page='login'), name='logout'),
    # ---------------------------------
    
    # Alamat halaman utama (kosong artinya root domain)
    path('', asset_list, name='asset_list'), 
    path('tambah/', asset_create, name='asset_create'), # <--- Jalur baru

    # --- TAMBAHAN BARU ---
    # <int:pk> itu artinya: "Tolong tangkap nomor ID asetnya"
    path('edit/<int:pk>/', asset_update, name='asset_update'),
    path('hapus/<int:pk>/', asset_delete, name='asset_delete'),
    path('export/', export_excel, name='export_excel'), # <--- Jalur baru
]