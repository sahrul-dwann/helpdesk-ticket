<?php

require_once __DIR__ . '/config/database.php';

// Ambil ID dari URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: index.php');
    exit;
}

// Cari tiket berdasarkan ID
$stmt = $pdo->prepare(
    "SELECT * FROM tickets WHERE id = :id"
);

$stmt->execute(['id' => $id]);

$ticket = $stmt->fetch();

// Jika tiket tidak ditemukan
if (!$ticket) {
    header('Location: index.php');
    exit;
}

function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Tiket - Helpdesk</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                Helpdesk Ticket
            </a>
        </div>
    </nav>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">

                <h2 class="fw-bold mb-1">Edit Tiket</h2>

                <p class="text-secondary mb-4">
                    Ubah informasi tiket #<?= e($ticket['id']) ?>.
                </p>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">

                        <form action="update.php" method="POST">

                            <input
                                type="hidden"
                                name="id"
                                value="<?= e($ticket['id']) ?>"
                            >

                            <div class="mb-3">
                                <label for="subject" class="form-label">
                                    Subject
                                </label>

                                <input
                                    type="text"
                                    id="subject"
                                    name="subject"
                                    class="form-control"
                                    maxlength="255"
                                    value="<?= e($ticket['subject']) ?>"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label">
                                    Category
                                </label>

                                <select
                                    id="category"
                                    name="category"
                                    class="form-select"
                                    required
                                >
                                    <?php
                                    $categories = [
                                        'Hardware',
                                        'Software',
                                        'Network',
                                        'Others'
                                    ];
                                    ?>

                                    <option value="">
                                        Pilih kategori
                                    </option>

                                    <?php foreach ($categories as $category): ?>
                                        <option
                                            value="<?= e($category) ?>"
                                            <?= $ticket['category'] === $category
                                                ? 'selected'
                                                : '' ?>
                                        >
                                            <?= e($category) ?>
                                        </option>
                                    <?php endforeach; ?>

                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    Description
                                </label>

                                <textarea
                                    id="description"
                                    name="description"
                                    class="form-control"
                                    rows="5"
                                    required
                                ><?= e($ticket['description']) ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label for="status" class="form-label">
                                    Status
                                </label>

                                <select
                                    id="status"
                                    name="status"
                                    class="form-select"
                                    required
                                >
                                    <?php
                                    $statuses = [
                                        'Open',
                                        'In Progress',
                                        'Closed'
                                    ];
                                    ?>

                                    <?php foreach ($statuses as $status): ?>
                                        <option
                                            value="<?= e($status) ?>"
                                            <?= $ticket['status'] === $status
                                                ? 'selected'
                                                : '' ?>
                                        >
                                            <?= e($status) ?>
                                        </option>
                                    <?php endforeach; ?>

                                </select>
                            </div>

                            <div class="d-flex gap-2">
                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                >
                                    Simpan Perubahan
                                </button>

                                <a
                                    href="index.php"
                                    class="btn btn-outline-secondary"
                                >
                                    Batal
                                </a>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </main>

</body>
</html>
