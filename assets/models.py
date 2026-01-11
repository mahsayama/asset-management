from django.db import models
from django.contrib.auth.models import User
from django.utils import timezone

# 1. MODEL MASTER DATA (Biar bisa ditambah via Dashboard)
class Kategori(models.Model):
    nama = models.CharField(max_length=100, unique=True)
    
    class Meta:
        verbose_name_plural = "Kategori"

    def __str__(self):
        return self.nama

class Lokasi(models.Model):
    nama = models.CharField(max_length=100, unique=True)

    class Meta:
        verbose_name_plural = "Lokasi"

    def __str__(self):
        return self.nama

# 2. MODEL UTAMA
class Asset(models.Model):
    # --- Opsi Pilihan Status (Status mah tetep hardcode aja krn logic system) ---
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
    
    # --- PERUBAHAN DISINI: Pake ForeignKey ke Model Baru ---
    kategori = models.ForeignKey(Kategori, on_delete=models.SET_NULL, null=True, blank=True)
    lokasi = models.ForeignKey(Lokasi, on_delete=models.SET_NULL, null=True, blank=True)
    
    purchase_date = models.DateField(verbose_name="Tanggal Beli", null=True, blank=True) 
    price = models.DecimalField(max_digits=15, decimal_places=0, verbose_name="Harga", null=True, blank=True)
    
    status = models.CharField(max_length=20, choices=STATUS_CHOICES, default='TERSEDIA')
    note = models.TextField(blank=True, null=True, verbose_name="Keterangan")

    # --- FIELD USER & HISTORY ---
    current_user = models.CharField(max_length=100, null=True, blank=True, verbose_name="Pengguna Saat Ini")
    current_dept = models.CharField(max_length=100, null=True, blank=True, verbose_name="Dept/Divisi Saat Ini")
    
    prev_user = models.CharField(max_length=100, null=True, blank=True, verbose_name="Pengguna Sebelumnya")
    prev_dept = models.CharField(max_length=100, null=True, blank=True, verbose_name="Dept/Divisi Sebelumnya")

    # --- TIMESTAMP ---
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    def __str__(self):
        return f"{self.name} ({self.serial_number})"

# --- MODEL HISTORY (AUDIT LOG) ---
class AssetHistory(models.Model):
    asset = models.ForeignKey(Asset, on_delete=models.CASCADE, related_name='history')
    changed_by = models.ForeignKey(User, on_delete=models.SET_NULL, null=True) 
    event_date = models.DateTimeField(default=timezone.now)
    description = models.TextField() 

    class Meta:
        ordering = ['-event_date'] 

    def __str__(self):
        return f"{self.asset.name} - {self.description}"