# 1. Gunakan base image Python yang ringan
FROM python:3.12-slim

# 2. Set environment variables agar Python output-nya langsung ke terminal (bagus buat log)
ENV PYTHONDONTWRITEBYTECODE=1
ENV PYTHONUNBUFFERED=1

# 3. Set folder kerja di dalam container
WORKDIR /app

# 4. Copy file requirements.txt dulu (biar cache-nya optimal)
COPY requirements.txt /app/

# 5. Install dependencies
RUN pip install --upgrade pip
RUN pip install -r requirements.txt

# 6. Copy seluruh kodingan project ke dalam container
COPY . /app/

# 7. Buka port 8000 (port default Django)
EXPOSE 8000

# 8. Perintah yang dijalankan saat container nyala
# Ganti 'nama_project_lu' dengan nama folder project django lu
CMD ["python", "manage.py", "runserver", "0.0.0.0:8000"]