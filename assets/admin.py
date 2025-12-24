from django.contrib import admin
from .models import Asset
# --- TAMBAHAN BARU ---
from django.utils.html import format_html
from django.urls import reverse
# ---------------------

@admin.register(Asset)
class AssetAdmin(admin.ModelAdmin):
    # Kolom yang mau ditampilkan di tabel list
    list_display = ('name', 'barcode_id', 'action_buttons', 'serial_number', 'status', 'purchase_date', 'price')
    
    # Menu filter di sidebar kanan
    list_filter = ('status', 'purchase_date')
    
    # Kotak pencarian (bisa cari nama atau serial number)
    search_fields = ('name', 'serial_number', 'barcode_id')
    
# --- FUNGSI BARU UNTUK TOMBOL ---
    @admin.display(description='Aksi')
    def action_buttons(self, obj):
        # 1. Bikin URL dinamis untuk Edit dan Delete
        # Polanya: 'admin:namaapp_namamodel_change' atau '..._delete'
        edit_url = reverse('admin:assets_asset_change', args=[obj.id])
        delete_url = reverse('admin:assets_asset_delete', args=[obj.id])

        # 2. Bikin HTML tombolnya pakai CSS inline sederhana biar berwarna
        # Tombol Edit (Hijau)
        edit_btn = format_html(
            '<a href="{}" style="background-color: #28a745; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; margin-right: 5px;">Edit</a>',
            edit_url
        )
        # Tombol Delete (Merah)
        delete_btn = format_html(
            '<a href="{}" style="background-color: #dc3545; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none;">Delete</a>',
            delete_url
        )
        
        # Gabungkan kedua tombol
        return format_html('{} {}', edit_btn, delete_btn)
    # --------------------------------
    