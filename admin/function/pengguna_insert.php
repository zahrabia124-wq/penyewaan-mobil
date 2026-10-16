<?php
// Memproses form "Tambah Pengguna".
// Alur: cek login admin -> terima data -> cek data -> cek email -> simpan.
require_once __DIR__ . '/../../login/cek_login.php';
cek_login('admin');
require_once __DIR__ . '/../database/connection.php';
$db   = new Database();
$conn = $db->conn;

// Kalau file ini dibuka langsung (bukan dari form), kembalikan ke daftar
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../index.php?page=pengguna');
    exit;
}

// 1. Ambil data dari form
$nama     = trim($_POST['nama'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? '';

// 2. Cek isian
if ($nama == '' || $email == '' || strlen($password) < 6 || !in_array($role, ['pelanggan', 'pemilik'])) {
    header('Location: ../index.php?page=pengguna-tambah&status=gagal&pesan=' . urlencode('Data tidak valid, periksa isian form.'));
    exit;
}

try {
    // 3. Email tidak boleh sama dengan pengguna lain (email dipakai untuk login)
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc()) {
        header('Location: ../index.php?page=pengguna-tambah&status=gagal&pesan=' . urlencode('Email sudah terdaftar.'));
        exit;
    }

    // 4. Password dienkripsi dulu (hash), baru disimpan
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $nama, $email, $hash, $role);
    $stmt->execute();

    header('Location: ../index.php?page=pengguna&status=sukses&pesan=' . urlencode('Pengguna berhasil ditambahkan.'));
} catch (Exception $e) {
    header('Location: ../index.php?page=pengguna-tambah&status=gagal&pesan=' . urlencode('Gagal menyimpan: ' . $e->getMessage()));
}
exit;