# 1. Gunakan base image Python yang ringan
FROM python:3.12-slim

# 2. Set environment variables
ENV PYTHONDONTWRITEBYTECODE=1
ENV PYTHONUNBUFFERED=1

# 3. Set folder kerja
WORKDIR /app

# --- PERBAIKAN DI SINI ---
# Install dependencies sistem buat Postgres (wajib buat psycopg2)
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       gcc \
       libpq-dev \
       python3-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*
# -------------------------

# 4. Copy file requirements
COPY requirements.txt /app/

# 5. Install dependencies
RUN pip install --upgrade pip
RUN pip install -r requirements.txt

# 6. Copy seluruh kodingan
COPY . /app/

# 7. Expose port
EXPOSE 8000

# 8. Jalanin server
CMD ["python", "manage.py", "runserver", "0.0.0.0:8000"]