CREATE DATABASE IF NOT EXISTS siperdag CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE siperdag;

CREATE TABLE IF NOT EXISTS tb_user (
 id_user INT AUTO_INCREMENT PRIMARY KEY,
 nama VARCHAR(100) NOT NULL,
 username VARCHAR(50) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 role VARCHAR(20) NOT NULL DEFAULT 'admin',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tb_produk (
 id_produk INT AUTO_INCREMENT PRIMARY KEY,
 nama_produk VARCHAR(100) NOT NULL,
 harga DECIMAL(12,2) NOT NULL DEFAULT 0,
 stok INT NOT NULL DEFAULT 0,
 kategori VARCHAR(50) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tb_pelanggan (
 id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
 nama_pelanggan VARCHAR(100) NOT NULL,
 alamat TEXT,
 no_hp VARCHAR(20),
 email VARCHAR(100),
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tb_penjualan (
 id_penjualan INT AUTO_INCREMENT PRIMARY KEY,
 id_user INT NOT NULL,
 id_pelanggan INT NULL,
 tanggal DATE NOT NULL,
 total DECIMAL(12,2) NOT NULL DEFAULT 0,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_penjualan_user FOREIGN KEY (id_user) REFERENCES tb_user(id_user) ON UPDATE CASCADE ON DELETE RESTRICT,
 CONSTRAINT fk_penjualan_pelanggan FOREIGN KEY (id_pelanggan) REFERENCES tb_pelanggan(id_pelanggan) ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS tb_detail_penjualan (
 id_detail INT AUTO_INCREMENT PRIMARY KEY,
 id_penjualan INT NOT NULL,
 id_produk INT NOT NULL,
 jumlah INT NOT NULL,
 harga DECIMAL(12,2) NOT NULL DEFAULT 0,
 subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
 CONSTRAINT fk_detail_penjualan FOREIGN KEY (id_penjualan) REFERENCES tb_penjualan(id_penjualan) ON UPDATE CASCADE ON DELETE CASCADE,
 CONSTRAINT fk_detail_produk FOREIGN KEY (id_produk) REFERENCES tb_produk(id_produk) ON UPDATE CASCADE ON DELETE RESTRICT
);

INSERT INTO tb_user (nama, username, password, role)
SELECT 'Administrator', 'admin', 'admin123', 'admin'
WHERE NOT EXISTS (SELECT 1 FROM tb_user WHERE username='admin');

INSERT INTO tb_produk (nama_produk,harga,stok,kategori)
SELECT 'Contoh Produk',50000,10,'Umum'
WHERE NOT EXISTS (SELECT 1 FROM tb_produk LIMIT 1);

INSERT INTO tb_pelanggan (nama_pelanggan,alamat,no_hp,email)
SELECT 'Pelanggan Umum','-','-','-'
WHERE NOT EXISTS (SELECT 1 FROM tb_pelanggan LIMIT 1);
