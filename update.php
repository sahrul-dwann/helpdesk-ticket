<?php

require_once __DIR__ . '/config/database.php';

// Pastikan request menggunakan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Ambil data dari form
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

$subject = trim($_POST['subject'] ?? '');
$category = $_POST['category'] ?? '';
$description = trim($_POST['description'] ?? '');
$status = $_POST['status'] ?? '';

// Validasi ID
if (!$id || $id < 1) {
    die('ID tiket tidak valid.');
}

// Validasi field wajib
if (
    $subject === '' ||
    $category === '' ||
    $description === '' ||
    $status === ''
) {
    die('Semua field wajib diisi.');
}

// Validasi kategori
$allowedCategories = [
    'Hardware',
    'Software',
    'Network',
    'Others'
];

if (!in_array($category, $allowedCategories, true)) {
    die('Kategori tidak valid.');
}

// Validasi status
$allowedStatuses = [
    'Open',
    'In Progress',
    'Closed'
];

if (!in_array($status, $allowedStatuses, true)) {
    die('Status tidak valid.');
}

// Validasi panjang subject
if (strlen($subject) > 255) {
    die('Subject maksimal 255 karakter.');
}

// Pastikan tiket memang ada
$check = $pdo->prepare(
    "SELECT id FROM tickets WHERE id = :id"
);

$check->execute(['id' => $id]);

if (!$check->fetch()) {
    die('Tiket tidak ditemukan.');
}

// Perbarui data tiket
$sql = "UPDATE tickets
        SET subject = :subject,
            category = :category,
            description = :description,
            status = :status
        WHERE id = :id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    'subject' => $subject,
    'category' => $category,
    'description' => $description,
    'status' => $status,
    'id' => $id
]);

// Kembali ke halaman daftar tiket
header('Location: index.php');
exit;
