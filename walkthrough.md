# 📦 IT Asset Management System (CodeIgniter 3 + HTMX)

Dokumentasi lengkap panduan penggunaan (*How to Use*), arsitektur sistem, *tech stack*, skema basis data, serta panduan operasional & **1-Click Deployment** pada Ubuntu 24.04 Server.

---

## 🛠️ Tech Stack & Architecture

### **Backend Framework & Core:**
- **PHP**: ^7.4 / ^8.2 (Compatible dengan PHP 7.4 production server & XAMPP PHP 8.2 local)
- **Framework**: CodeIgniter 3.1.13 (`application/` MVC pattern)
- **Database Support**: PostgreSQL (Production) / SQLite3 `db.sqlite3` & MySQL / MariaDB (Local XAMPP via PDO/SQLite/MySQLi drivers)

### **Frontend & UI/UX:**
- **HTML5 & Vanilla CSS3**: Custom responsive design system dengan Inter typography & smooth micro-animations.
- **Bootstrap 5.3**: Layout Grid, Utility classes, Modals, Toasts, & Form styling.
- **Bootstrap Icons 1.11.3**: Modern UI icons.
- **HTMX 1.9.10**: Zero-reload SPA-like interactivity (AJAX filtering, pagination, sorting, & dynamic partial swaps).
- **TomSelect 2.3.1**: Searchable dropdowns untuk Kategori & Lokasi Aset.
- **Chart.js**: Doughnut & Bar charts untuk statistik sebaran kategori & lokasi di halaman Reports & Dashboard.
- **SweetAlert2 11**: Pop-up modal konfirmasi hapus single/bulk aset yang interaktif.

### **Libraries & Tools:**
- **PhpOffice / PhpSpreadsheet 1.29**: Import spreadsheet data aset masal (`.xlsx` / `.csv`) & export template Excel.
- **Composer**: PHP dependency manager.

---

## 🚀 How to Use / Cara Penggunaan Aplikasi

### **1. Halaman Login (Authentication)**
- Akses URL: `http://127.0.0.1:8000/login` atau `http://<SERVER_IP>/login`
- **Default Credential Admin:**
  - **Username / Email:** `admin` atau `admin@example.com`
  - **Password:** `password`
- **Fitur:**
  - Desain 2-kolom split-screen modern dengan background gradient.
  - **Show/Hide Password:** Klik ikon mata 👁️ untuk melihat atau menyembunyikan kata sandi.

### **2. Menu Dashboard (`/dashboard`)**
- **5 Status Counters:**
  1. **Total Unit Aset:** Seluruh perangkat yang terdaftar di sistem.
  2. **Sedang Dipakai:** Aset aktif yang dipegang pengguna/karyawan.
  3. **Status Tersedia:** Aset dalam kondisi baik yang siap ditugaskan (Storage).
  4. **Kondisi Rusak:** Perangkat yang memerlukan perbaikan/maintenance.
  5. **Tidak Layak Pakai:** Aset yang sudah *decommissioned* / tidak layak guna.
- **Ringkasan Tabel:** Menampilkan 5 aset terbaru yang didaftarkan ke dalam sistem.

### **3. Menu Inventory (`/inventory`)**
- **Navigasi & Pencarian Instan (HTMX):**
  - **Filter Bar:** Filter data berdasarkan Kategori, Lokasi, dan Status Kondisi.
  - **Pencarian Real-Time:** Ketik nama aset, Serial Number, Barcode, atau nama User PIC pada kotak pencarian.
  - **Header Sorting:** Klik nama kolom tabel (*Nama Aset*, *Status*, *Aksi*) untuk mengurutkan data dari A s/d Z atau sebaliknya.
- **Tambah Aset Baru (`/tambah`):**
  - Mengisi Informasi Utama (Nama Aset, Serial Number, Barcode ID).
  - Mengisi Lokasi Penempatan & Kategori menggunakan **Searchable Dropdown (TomSelect)**.
  - Mengisi harga beli (format mata uang otomatis `Rp X.XXX.XXX`), tanggal pembelian, dan penugasan User PIC (Nama & Departemen).
- **Edit Aset (`/edit/{id}`):**
  - Mengubah rincian aset.
  - **Automatic Handover Shift:** Jika User PIC saat ini diubah ke nama baru, sistem otomatis memindahkan User PIC lama ke kolom *User Sebelumnya* dan mencatat riwayat serah terima aset (*Handover Audit Log*).
- **Detail & Riwayat Aset (`/asset/{id}/detail`):**
  - Menampilkan informasi lengkap teknis & finansial aset.
  - **Timeline Log History:** Menampilkan jejak audit aktivitas aset.
- **Hapus & Hapus Masal (Bulk Delete):**
  - **Hapus Single:** Klik ikon tempat sampah 🗑️ di baris aset untuk konfirmasi SweetAlert2.
  - **Hapus Masal (Bulk Delete):** Centang *Select All* di header tabel pojok kiri atau centang beberapa baris aset ➔ Klik tombol merah **`Hapus X Aset`** ➔ Konfirmasi hapus sekaligus.
- **Import Data Excel (`.xlsx`):**
  - Klik tombol **`Import`** di bagian atas tabel ➔ Unggah file Excel.

### **4. Menu Reports (`/reports`)**
- **Visualisasi Grafik (Chart.js):** Doughnut Chart Kategori & Bar Chart Lokasi.
- **Ekspor Data Rekap CSV:** Download rekapitulasi seluruh data aset.

### **5. Menu Settings (`/settings`)**
- **Manajemen Administrator:** Tambah Admin baru & Hapus Admin.
- **Keamanan Akun:** Formulir Ubah Kata Sandi (*Change Password*).
- **Master Data Gedung & Kategori:** Tambah/Hapus Gedung & Kategori via Zero-Reload AJAX.

---

## 💻 1-Click Installation Guide di Server Ubuntu 24.04 (Dev Server)

Proyek ini telah dilengkapi dengan skrip otomatisasi **[setup.sh](file:///e:/MAHSA/PROJECT/asset-manajemen/setup.sh)** dan **[docker-compose.yml](file:///e:/MAHSA/PROJECT/asset-manajemen/docker-compose.yml)**.

### **Langkah Deploy (Cukup 1 Perintah CLI):**

1. **Upload / Clone folder proyek ke server Ubuntu 24.04:**
   ```bash
   git clone <REPOSITORY_URL> /var/www/asset-manajemen
   cd /var/www/asset-manajemen
   ```

2. **Jalankan Skrip Instalasi 1-Klik (`setup.sh`):**
   ```bash
   chmod +x setup.sh
   ./setup.sh
   ```

### **Apa yang Dilakukan Skrip `setup.sh` secara Otomatis?**
- 1️⃣ Meng-update *system packages* Ubuntu 24.04.
- 2️⃣ Menginstall **Docker** & **Docker Compose** (jika belum ada di server).
- 3️⃣ Meng-compile image web (CodeIgniter 3 + PHP 8.2/7.4 Apache) & menjalankan kontainer **PostgreSQL 15**.
- 4️⃣ Otomatis meng-import skema basis data awal dari **`backup_asset.sql`** ke PostgreSQL.
- 5️⃣ Menampilkan IP Address dan URL aplikasi yang bisa langsung diakses di browser (`http://<IP_SERVER_KANTOR>`).

---

## 📂 Struktur Direktori Proyek

```
asset-manajemen/
├── application/            # Core CodeIgniter 3 MVC Application
│   ├── config/             # Routes, Database, Autoload, & Config
│   ├── controllers/        # Auth, Dashboard, Inventory, Reports, Settings
│   ├── models/             # User_model, Asset_model, Kategori_model, Lokasi_model, History_model
│   └── views/              # Header, Sidebar, Footer, & Page Views (Auth, Inventory, Reports, Settings)
├── composer.json           # PHP Dependency Configuration
├── docker-compose.yml      # Multi-container Docker Orchestration (Web + PostgreSQL 15)
├── dockerfile              # Docker Container Build Specification
├── setup.sh                # 1-Click Deployment Script untuk Ubuntu 24.04
├── db.sqlite3              # SQLite Database File (Development)
├── backup_asset.sql        # PostgreSQL Database Backup & Seed Dump
├── index.php               # Front Controller / Entry Point
└── walkthrough.md          # Complete User Guide & Documentation
```
