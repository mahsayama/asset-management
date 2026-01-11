from django.contrib import admin
from .models import Asset, AssetHistory, Kategori, Lokasi  # <--- Tambah Kategori & Lokasi
from django.contrib.admin.models import LogEntry
from django.utils.html import format_html
from django.urls import reverse

# 1. Registrasi Master Data (Biar muncul di Sidebar Admin)
@admin.register(Kategori)
class KategoriAdmin(admin.ModelAdmin):
    list_display = ('nama',)
    search_fields = ('nama',)

@admin.register(Lokasi)
class LokasiAdmin(admin.ModelAdmin):
    list_display = ('nama',)
    search_fields = ('nama',)

# 2. Registrasi Audit Log (Opsional, biar bisa liat log di admin)
@admin.register(AssetHistory)
class AssetHistoryAdmin(admin.ModelAdmin):
    list_display = ('asset', 'changed_by', 'event_date', 'description')
    list_filter = ('event_date', 'changed_by')

# 3. Registrasi Asset (Kodingan existing lu yang udah dirapihin)
@admin.register(Asset)
class AssetAdmin(admin.ModelAdmin):
    # Tambahin 'kategori' dan 'lokasi' di list_display biar keliatan di tabel
    list_display = ('name', 'barcode_id', 'kategori', 'lokasi', 'action_buttons', 'status', 'price')
    
    # Tambahin filter kategori dan lokasi di sidebar kanan
    list_filter = ('status', 'kategori', 'lokasi', 'purchase_date')
    
    search_fields = ('name', 'serial_number', 'barcode_id')
    
    @admin.display(description='Aksi')
    def action_buttons(self, obj):
        edit_url = reverse('admin:assets_asset_change', args=[obj.id])
        delete_url = reverse('admin:assets_asset_delete', args=[obj.id])

        edit_btn = format_html(
            '<a href="{}" style="background-color: #28a745; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; margin-right: 5px;">Edit</a>',
            edit_url
        )
        delete_btn = format_html(
            '<a href="{}" style="background-color: #dc3545; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none;">Delete</a>',
            delete_url
        )
        
        return format_html('{} {}', edit_btn, delete_btn)