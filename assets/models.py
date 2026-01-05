from django.db import models
from django.contrib.auth.models import User
from django.utils import timezone

class Asset(models.Model):
    # --- Opsi Pilihan Kategori ---
    KATEGORI_CHOICES = [
        ('Laptop', 'Laptop'),
        ('PC', 'PC'),
        ('Printer', 'Printer'),
        ('Scanner', 'Scanner'),
        ('Server', 'Server'),
        ('Network', 'Network'),
        ('UPS', 'UPS'),
        ('Handphone', 'Handphone'),
        ('Tablet', 'Tablet'),
        ('Webcam', 'Webcam'),
        ('Speaker', 'Speaker'),
    ]

    # --- Opsi Pilihan Lokasi ---
    LOKASI_CHOICES = [
        ('Gedung 32', 'Gedung 32'),
        ('Gedung 34', 'Gedung 34'),
        ('Gedung 38', 'Gedung 38'),
        ('Gedung 39', 'Gedung 39'),
    ]

    # --- Opsi Pilihan Status ---
    STATUS_CHOICES = [
        ('TERSEDIA', 'Tersedia'),
        ('DIPAKAI', 'Sedang Dipakai'),
        ('RUSAK', 'Rusak'),
        ('HILANG', 'Hilang'),
    ]

    # --- FIELD DATA UTAMA ---
    name = models.CharField(max_length=200, verbose_name="Nama Aset") 
    barcode_id = models.CharField(max_length=100, verbose_name="ID Barcode", blank=True, null=True)
    serial_number = models.CharField(max_length=100, unique=True, verbose_name="Nomor Seri")
    
    kategori = models.CharField(max_length=50, choices=KATEGORI_CHOICES)
    lokasi = models.CharField(max_length=50, choices=LOKASI_CHOICES)
    
    purchase_date = models.DateField(verbose_name="Tanggal Beli")
    price = models.DecimalField(max_digits=15, decimal_places=0, verbose_name="Harga")
    status = models.CharField(max_length=20, choices=STATUS_CHOICES, default='TERSEDIA')
    note = models.TextField(blank=True, null=True, verbose_name="Keterangan")

    # --- FIELD USER & HISTORY ---
    # Pengguna Saat Ini
    current_user = models.CharField(max_length=100, null=True, blank=True, verbose_name="Pengguna Saat Ini")
    current_dept = models.CharField(max_length=100, null=True, blank=True, verbose_name="Dept/Divisi Saat Ini")
    
    # Pengguna Sebelumnya
    prev_user = models.CharField(max_length=100, null=True, blank=True, verbose_name="Pengguna Sebelumnya")
    prev_dept = models.CharField(max_length=100, null=True, blank=True, verbose_name="Dept/Divisi Sebelumnya")

    # --- TIMESTAMP ---
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    def __str__(self):
        return f"{self.name} ({self.serial_number})"

# --- MODEL HISTORY (AUDIT LOG) ---
class AssetHistory(models.Model):
    # PERHATIKAN: Ini semua menjorok ke dalam (Indented)
    asset = models.ForeignKey(Asset, on_delete=models.CASCADE, related_name='history')
    changed_by = models.ForeignKey(User, on_delete=models.SET_NULL, null=True) 
    event_date = models.DateTimeField(default=timezone.now)
    description = models.TextField() 

    class Meta:
        ordering = ['-event_date'] 

    def __str__(self):
        return f"{self.asset.name} - {self.description}"