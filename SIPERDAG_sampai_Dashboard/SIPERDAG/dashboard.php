<?php
session_start();
require_once "config/koneksi.php";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION["user"];

function jumlahData($conn, $table) {
    $allowed = ["tb_produk", "tb_pelanggan", "tb_penjualan"];
    if (!in_array($table, $allowed, true)) return 0;
    $result = $conn->query("SELECT COUNT(*) AS total FROM $table");
    return (int)($result->fetch_assoc()["total"] ?? 0);
}

$totalProduk = jumlahData($conn, "tb_produk");
$totalPelanggan = jumlahData($conn, "tb_pelanggan");
$totalTransaksi = jumlahData($conn, "tb_penjualan");

$totalPenjualan = 0;
$res = $conn->query("SELECT COALESCE(SUM(total),0) AS total FROM tb_penjualan");
if ($res) $totalPenjualan = (float)$res->fetch_assoc()["total"];

$transaksiTerbaru = $conn->query("
    SELECT p.id_penjualan, p.tanggal, p.total, pl.nama_pelanggan, u.nama AS nama_user
    FROM tb_penjualan p
    LEFT JOIN tb_pelanggan pl ON pl.id_pelanggan = p.id_pelanggan
    LEFT JOIN tb_user u ON u.id_user = p.id_user
    ORDER BY p.id_penjualan DESC LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - SIPERDAG</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="side-brand">
            <div class="brand-icon small">S</div>
            <div><b>SIPERDAG</b><span>Sistem Perdagangan</span></div>
        </div>

        <nav>
            <a class="active" href="dashboard.php">Dashboard</a>
            <a href="#">Data Produk</a>
            <a href="#">Data Pelanggan</a>
            <a href="#">Data Stok</a>
            <a href="#">Transaksi Penjualan</a>
            <a href="#">Laporan</a>
        </nav>

        <a class="logout" href="logout.php">Logout</a>
    </aside>

    <main class="content">
        <header class="topbar">
            <div>
                <h1>Dashboard</h1>
                <p>Ringkasan informasi SIPERDAG</p>
            </div>
            <div class="profile">
                <div class="avatar"><?= strtoupper(substr($user["nama"],0,1)) ?></div>
                <div>
                    <b><?= htmlspecialchars($user["nama"]) ?></b>
                    <span><?= htmlspecialchars(ucfirst($user["role"])) ?></span>
                </div>
            </div>
        </header>

        <section class="welcome">
            <div>
                <h2>Halo, <?= htmlspecialchars($user["nama"]) ?> 👋</h2>
                <p>Selamat datang di sistem informasi perdagangan.</p>
            </div>
        </section>

        <section class="cards">
            <div class="card">
                <span class="card-icon">📦</span>
                <div><small>Total Produk</small><strong><?= $totalProduk ?></strong></div>
            </div>
            <div class="card">
                <span class="card-icon">👥</span>
                <div><small>Total Pelanggan</small><strong><?= $totalPelanggan ?></strong></div>
            </div>
            <div class="card">
                <span class="card-icon">🧾</span>
                <div><small>Total Transaksi</small><strong><?= $totalTransaksi ?></strong></div>
            </div>
            <div class="card">
                <span class="card-icon">💰</span>
                <div><small>Total Penjualan</small><strong>Rp <?= number_format($totalPenjualan,0,",",".") ?></strong></div>
            </div>
        </section>

        <section class="panel">
            <div class="panel-head">
                <div>
                    <h2>Transaksi Terbaru</h2>
                    <p>5 transaksi terakhir yang tersimpan.</p>
                </div>
                <a class="btn primary" href="#">Lihat Semua</a>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th><th>Tanggal</th><th>Pelanggan</th><th>Kasir</th><th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($transaksiTerbaru && $transaksiTerbaru->num_rows): ?>
                        <?php while ($row = $transaksiTerbaru->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= (int)$row["id_penjualan"] ?></td>
                            <td><?= htmlspecialchars($row["tanggal"]) ?></td>
                            <td><?= htmlspecialchars($row["nama_pelanggan"] ?: "-") ?></td>
                            <td><?= htmlspecialchars($row["nama_user"] ?: "-") ?></td>
                            <td><b>Rp <?= number_format((float)$row["total"],0,",",".") ?></b></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="empty">Belum ada transaksi.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
</body>
</html>