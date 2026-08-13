-- =============================================
-- Asset Management CI3 - Database Schema
-- =============================================

-- Users table (for CI3 auth)
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Assets table
CREATE TABLE IF NOT EXISTS assets (
    id SERIAL PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    barcode_id VARCHAR(100),
    serial_number VARCHAR(100) NOT NULL,
    purchase_date DATE,
    price NUMERIC(15,0),
    status VARCHAR(20) NOT NULL DEFAULT 'available',
    note TEXT,
    "current_user" VARCHAR(100),
    "current_dept" VARCHAR(100),
    "prev_user" VARCHAR(100),
    "prev_dept" VARCHAR(100),
    kategori_id BIGINT,
    lokasi_id BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS kategori (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Locations table
CREATE TABLE IF NOT EXISTS lokasi (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Asset History table
CREATE TABLE IF NOT EXISTS asset_history (
    id SERIAL PRIMARY KEY,
    event_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description TEXT NOT NULL,
    asset_id BIGINT NOT NULL,
    changed_by_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('admin', 'admin@mahsa.com', '$2y$10$mDugWpQaJcknZm7LulCjh.MTe6.YVYUX0PZT3vsPTtG5.3ZSAJ6d6', 'admin')
ON CONFLICT (email) DO NOTHING;
