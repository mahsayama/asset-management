from django import forms
from .models import Asset

class AssetForm(forms.ModelForm):
    class Meta:
        model = Asset
        fields = '__all__' # <--- Pastiin ini __all__ biar otomatis
        
        # Ini biar form-nya keliatan ganteng pake class Bootstrap
        widgets = {
            'name': forms.TextInput(attrs={'class': 'form-control'}),
            'barcode_id': forms.TextInput(attrs={'class': 'form-control'}),
            'serial_number': forms.TextInput(attrs={'class': 'form-control'}),
            'kategori': forms.Select(attrs={'class': 'form-control'}),
            'lokasi': forms.Select(attrs={'class': 'form-control'}),
            'current_user': forms.TextInput(attrs={'class': 'form-control'}),
            'current_dept': forms.TextInput(attrs={'class': 'form-control'}),
            'prev_user': forms.TextInput(attrs={'class': 'form-control'}),
            'prev_dept': forms.TextInput(attrs={'class': 'form-control'}),
            'status': forms.Select(attrs={'class': 'form-select'}),
            'purchase_date': forms.DateInput(attrs={'class': 'form-control', 'type': 'date'}),
            'price': forms.NumberInput(attrs={'class': 'form-control'}),
            'note': forms.Textarea(attrs={'class': 'form-control', 'rows': 3}),
        }