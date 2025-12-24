from django import forms
from .models import Asset

class AssetForm(forms.ModelForm):
    class Meta:
        model = Asset
        fields = ['name', 'barcode_id', 'serial_number', 'status', 'purchase_date', 'price', 'note']
        
        # Ini biar form-nya keliatan ganteng pake class Bootstrap
        widgets = {
            'name': forms.TextInput(attrs={'class': 'form-control'}),
            'barcode_id': forms.TextInput(attrs={'class': 'form-control'}),
            'serial_number': forms.TextInput(attrs={'class': 'form-control'}),
            'status': forms.Select(attrs={'class': 'form-select'}),
            'purchase_date': forms.DateInput(attrs={'class': 'form-control', 'type': 'date'}),
            'price': forms.NumberInput(attrs={'class': 'form-control'}),
            'note': forms.Textarea(attrs={'class': 'form-control', 'rows': 3}),
        }