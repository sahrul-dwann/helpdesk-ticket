<?php

require_once __DIR__ . '/config/database.php';

// Pastikan request berasal dari form POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Ambil data dari form
$subject = trim($_POST['subject'] ?? '');
$category = $_POST['category'] ?? '';
$description = trim($_POST['description'] ?? '');

// Validasi field wajib
if (
    $subject === '' ||
    $category === '' ||
    $description === ''
) {
    die('Semua field wajib diisi.');
}

// Validasi kategori yang diizinkan
$allowedCategories = [
    'Hardware',
    'Software',
    'Network',
    'Others'
];

if (!in_array($category, $allowedCategories, true)) {
    die('Kategori tidak valid.');
}

// Validasi panjang subject
if (strlen($subject) > 255) {
    die('Subject maksimal 255 karakter.');
}

// Simpan tiket ke database
$sql = "INSERT INTO tickets
        (subject, category, description)
        VALUES
        (:subject, :category, :description)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    'subject' => $subject,
    'category' => $category,
    'description' => $description
]);

// Kembali ke halaman daftar tiket
header('Location: index.php');
exit;
