#!/bin/bash
# =================================================================
# 🚀 1-CLICK DEPLOYMENT SCRIPT UNTUK UBUNTU 24.04 (DEV SERVER)
# Aplikasi: IT Asset Management System (CodeIgniter 3 + PostgreSQL)
# =================================================================

set -e

echo "======================================================="
echo "  🚀 MEMULAI PROSES INSTALASI IT ASSET MANAGEMENT APP"
echo "======================================================="

# 1. Update package list
echo "📦 Updating Ubuntu system packages..."
sudo apt-get update -y

# 2. Check and Install Docker if missing
if ! command -v docker &> /dev/null; then
    echo "🐳 Docker belum terinstall. Menginstall Docker..."
    curl -fsSL https://get.docker.com -o get-docker.sh
    sudo sh get-docker.sh
    sudo usermod -aG docker $USER || true
    rm -f get-docker.sh
    echo "✅ Docker berhasil diinstall."
else
    echo "✅ Docker sudah terinstall."
fi

# 3. Check and Install Docker Compose if missing
if ! command -v docker-compose &> /dev/null && ! docker compose version &> /dev/null; then
    echo "🐳 Menginstall Docker Compose plugin..."
    sudo apt-get install -y docker-compose-plugin
    echo "✅ Docker Compose berhasil diinstall."
fi

# 4. Build & Run Containers via Docker Compose
echo "🛠️ Building & Starting Application Containers..."
if command -v docker-compose &> /dev/null; then
    sudo docker-compose up -d --build
else
    sudo docker compose up -d --build
fi

# 5. Wait for PostgreSQL container to be ready and populate database
echo "⏳ Menunggu database PostgreSQL siap..."
sleep 6

if [ -f "backup_asset.sql" ]; then
    echo "🗄️ Mengisi database awal dari backup_asset.sql..."
    sudo docker exec -i asset_management_db psql -U postgres -d asset_db < backup_asset.sql 2>/dev/null || true
fi

# 6. Show Success Banner
SERVER_IP=$(hostname -I | awk '{print $1}')

echo ""
echo "======================================================="
echo "🎉 INSTALASI DAN DEPLOYMENT BERHASIL 100%!"
echo "======================================================="
echo "🌐 Akses Aplikasi melalui Browser:"
echo "   http://${SERVER_IP}"
echo "   atau http://localhost"
echo ""
echo "🔑 Credential Login Default Admin:"
echo "   Username / Email : admin"
echo "   Password         : password"
echo "======================================================="
