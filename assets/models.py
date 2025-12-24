from django.db import models

class Asset(models.Model):
    # Pilihan status aset
    STATUS_CHOICES = [
        ('TERSEDIA', 'Tersedia'),
        ('DIPAKAI', 'Sedang Dipakai'),
        ('RUSAK', 'Rusak'),
        ('HILANG', 'Hilang'),
    ]

    name = models.CharField(max_length=200, verbose_name="Nama Aset") 
    barcode_id = models.CharField(max_length=100, verbose_name="ID Barcode", blank=True, null=True)
    serial_number = models.CharField(max_length=100, unique=True, verbose_name="Nomor Seri")
    purchase_date = models.DateField(verbose_name="Tanggal Beli")
    price = models.DecimalField(max_digits=15, decimal_places=0, verbose_name="Harga")
    status = models.CharField(max_length=20, choices=STATUS_CHOICES, default='TERSEDIA')
    note = models.TextField(blank=True, null=True, verbose_name="Keterangan")
    
    created_at = models.DateTimeField(auto_now_add=True) # Mencatat kapan data diinput

    def __str__(self):
        return f"{self.name} ({self.serial_number})"