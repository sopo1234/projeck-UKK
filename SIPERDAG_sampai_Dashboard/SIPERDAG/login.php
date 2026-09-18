<?php
session_start();
require_once "config/koneksi.php";

$error = "";

if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Username dan password wajib diisi.";
    } else {
        $stmt = $conn->prepare("SELECT id_user, nama, username, password, role FROM tb_user WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && ($password === $user["password"] || password_verify($password, $user["password"]))) {
            $_SESSION["user"] = [
                "id_user" => $user["id_user"],
                "nama" => $user["nama"],
                "username" => $user["username"],
                "role" => $user["role"]
            ];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Username atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - SIPERDAG</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
<div class="login-card">
    <div class="brand">
        <div class="brand-icon">S</div>
        <div>
            <h1>SIPERDAG</h1>
            <p>Sistem Informasi Perdagangan</p>
        </div>
    </div>

    <h2>Selamat Datang</h2>
    <p class="muted">Silakan masuk untuk melanjutkan.</p>

    <?php if ($error): ?>
        <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <label>Username</label>
        <input type="text" name="username" placeholder="Masukkan username" required autofocus>

        <label>Password</label>
        <input type="password" name="password" placeholder="Masukkan password" required>

        <button class="btn primary full" type="submit">Masuk ke Sistem</button>
    </form>

    <div class="demo-account">
        <b>Akun awal</b><br>
        Username: <code>admin</code><br>
        Password: <code>admin123</code>
    </div>
</div>
</body>
</html>