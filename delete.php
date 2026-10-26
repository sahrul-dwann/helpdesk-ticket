<?php

require_once __DIR__ . '/config/database.php';

// Hapus hanya melalui request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Validasi ID tiket
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: index.php');
    exit;
}

// Periksa apakah tiket tersedia
$check = $pdo->prepare(
    "SELECT id FROM tickets WHERE id = :id"
);

$check->execute(['id' => $id]);

if (!$check->fetch()) {
    header('Location: index.php');
    exit;
}

// Hapus tiket
$stmt = $pdo->prepare(
    "DELETE FROM tickets WHERE id = :id"
);

$stmt->execute(['id' => $id]);

// Kembali ke daftar tiket
header('Location: index.php');
exit;
