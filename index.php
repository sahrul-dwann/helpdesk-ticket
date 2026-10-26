
<?php

require_once __DIR__ . '/config/database.php';

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);

$page = ($page && $page > 0) ? $page : 1;
$perPage = 5;

$allowedStatuses = ['Open', 'In Progress', 'Closed'];

if (!in_array($status, $allowedStatuses, true)) {
    $status = '';
}

// Pencarian dan filter
$conditions = [];
$params = [];

if ($search !== '') {
    $conditions[] = 'subject LIKE :search';
    $params['search'] = '%' . $search . '%';
}

if ($status !== '') {
    $conditions[] = 'status = :status';
    $params['status'] = $status;
}

$whereSql = '';

if (!empty($conditions)) {
    $whereSql = 'WHERE ' . implode(' AND ', $conditions);
}

// Total seluruh tiket
$totalAllStmt = $pdo->query(
    'SELECT COUNT(*) FROM tickets'
);

$totalAllTickets = (int) $totalAllStmt->fetchColumn();

// Total hasil pencarian/filter
$countStmt = $pdo->prepare(
    "SELECT COUNT(*) FROM tickets $whereSql"
);

$countStmt->execute($params);
$totalTickets = (int) $countStmt->fetchColumn();

// Pagination
$totalPages = max(1, (int) ceil($totalTickets / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

// Mengambil data tiket
$sql = "SELECT * FROM tickets
        $whereSql
        ORDER BY id DESC
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
}

$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$tickets = $stmt->fetchAll();

$displayStart = $totalTickets > 0 ? $offset + 1 : 0;
$displayEnd = min($offset + count($tickets), $totalTickets);

// Escape output HTML
function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

// Warna status
function statusBadge($status)
{
    switch ($status) {
        case 'Open':
            return 'success';

        case 'In Progress':
            return 'warning';

        case 'Closed':
            return 'danger';

        default:
            return 'secondary';
    }
}

// URL pagination
function pageUrl($pageNumber, $search, $status)
{
    $query = ['page' => $pageNumber];

    if ($search !== '') {
        $query['search'] = $search;
    }

    if ($status !== '') {
        $query['status'] = $status;
    }

    return 'index.php?' . http_build_query($query);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Helpdesk Ticket</title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- CSS milik project -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                Helpdesk Ticket
            </a>
        </div>
    </nav>

    <main class="container py-4 py-md-5">

        <!-- Header -->
        <div class="d-flex justify-content-between
                    align-items-center mb-4 flex-wrap gap-3">

            <div>
                <h2 class="fw-bold mb-1">Daftar Tiket</h2>
                <p class="text-secondary mb-0">
                    Kelola laporan masalah IT.
                </p>
            </div>

            <a href="create.php" class="btn btn-primary">
                + Tambah Tiket
            </a>

        </div>

        <!-- Ringkasan tiket -->
        <div class="row g-3 mb-4">

            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-secondary mb-1">
                            Total Seluruh Tiket
                        </p>

                        <h3 class="fw-bold mb-0">
                            <?= $totalAllTickets ?>
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-secondary mb-1">
                            Hasil Pencarian / Filter
                        </p>

                        <h3 class="fw-bold mb-0">
                            <?= $totalTickets ?>
                        </h3>
                    </div>
                </div>
            </div>

        </div>

        <!-- Pencarian dan filter -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">

                <form method="GET" action="index.php">

                    <div class="row g-3">

                        <div class="col-12 col-md-6">
                            <label for="search" class="form-label">
                                Cari Subject
                            </label>

                            <input
                                type="text"
                                name="search"
                                id="search"
                                class="form-control"
                                placeholder="Masukkan subject tiket..."
                                value="<?= e($search) ?>"
                            >
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="status" class="form-label">
                                Filter Status
                            </label>

                            <select
                                name="status"
                                id="status"
                                class="form-select"
                            >
                                <option value="">Semua Status</option>

                                <option
                                    value="Open"
                                    <?= $status === 'Open' ? 'selected' : '' ?>
                                >
                                    Open
                                </option>

                                <option
                                    value="In Progress"
                                    <?= $status === 'In Progress' ? 'selected' : '' ?>
                                >
                                    In Progress
                                </option>

                                <option
                                    value="Closed"
                                    <?= $status === 'Closed' ? 'selected' : '' ?>
                                >
                                    Closed
                                </option>
                            </select>
                        </div>

                        <div class="col-12 col-md-2 d-flex align-items-end">
                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                            >
                                Cari
                            </button>
                        </div>

                    </div>

                    <?php if ($search !== '' || $status !== ''): ?>
                        <div class="mt-3">
                            <a
                                href="index.php"
                                class="btn btn-outline-secondary btn-sm"
                            >
                                Reset Pencarian
                            </a>
                        </div>
                    <?php endif; ?>

                </form>

            </div>
        </div>

        <!-- Data tiket -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="d-flex justify-content-between
                            align-items-center flex-wrap gap-2 mb-3">

                    <h5 class="fw-bold mb-0">
                        Data Tiket
                    </h5>

                    <span class="text-secondary small">
                        Menampilkan <?= $displayStart ?>–<?= $displayEnd ?>
                        dari <?= $totalTickets ?> tiket
                    </span>

                </div>

                <!-- Tabel yang bisa digeser -->
                <div
                    class="ticket-table-wrapper"
                    id="ticketTableWrapper"
                >
                    <table
                        class="table table-hover align-middle"
                        id="ticketTable"
                    >

                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Subject</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (count($tickets) > 0): ?>

                                <?php foreach ($tickets as $ticket): ?>

                                    <tr>
                                        <td>
                                            <?= e($ticket['id']) ?>
                                        </td>

                                        <td class="fw-semibold">
                                            <?= e($ticket['subject']) ?>
                                        </td>

                                        <td>
                                            <?= e($ticket['category']) ?>
                                        </td>

                                        <td>
                                            <span class="badge text-bg-<?= statusBadge($ticket['status']) ?>">
                                                <?= e($ticket['status']) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?= e(date(
                                                'd-m-Y H:i',
                                                strtotime($ticket['created_at'])
                                            )) ?>
                                        </td>

                                        <td>
                                            <div class="d-flex gap-2">

                                                <a
                                                    href="edit.php?id=<?= e($ticket['id']) ?>"
                                                    class="btn btn-sm btn-warning"
                                                >
                                                    Edit
                                                </a>

                                                <form
                                                    action="delete.php"
                                                    method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus tiket ini?');"
                                                >
                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= e($ticket['id']) ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-danger"
                                                    >
                                                        Delete
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>
                                    <td
                                        colspan="6"
                                        class="text-center py-4 text-secondary"
                                    >
                                        Tidak ada tiket ditemukan.
                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>
                </div>

                <!-- Scrollbar biru tambahan -->
                <div
                    class="ticket-scrollbar"
                    id="ticketScrollbar"
                    aria-label="Geser tabel ke kanan atau kiri"
                >
                    <div
                        class="ticket-scrollbar-thumb"
                        id="ticketScrollbarThumb"
                    ></div>
                </div>

                <!-- Pagination -->
                <?php if ($totalTickets > 0 && $totalPages > 1): ?>

                    <nav aria-label="Navigasi halaman tiket">
                        <ul class="pagination justify-content-center
                                   flex-wrap mt-4 mb-0">

                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <?php if ($page > 1): ?>
                                    <a
                                        class="page-link"
                                        href="<?= e(pageUrl($page - 1, $search, $status)) ?>"
                                    >
                                        Previous
                                    </a>
                                <?php else: ?>
                                    <span class="page-link">Previous</span>
                                <?php endif; ?>
                            </li>

                            <?php for (
                                $i = max(1, $page - 2);
                                $i <= min($totalPages, $page + 2);
                                $i++
                            ): ?>

                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a
                                        class="page-link"
                                        href="<?= e(pageUrl($i, $search, $status)) ?>"
                                    >
                                        <?= $i ?>
                                    </a>
                                </li>

                            <?php endfor; ?>

                            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                <?php if ($page < $totalPages): ?>
                                    <a
                                        class="page-link"
                                        href="<?= e(pageUrl($page + 1, $search, $status)) ?>"
                                    >
                                        Next
                                    </a>
                                <?php else: ?>
                                    <span class="page-link">Next</span>
                                <?php endif; ?>
                            </li>

                        </ul>
                    </nav>

                <?php endif; ?>

                <?php if ($totalTickets > 0): ?>
                    <p class="text-secondary small text-center mt-3 mb-0">
                        Halaman <?= $page ?> dari <?= $totalPages ?>
                    </p>
                <?php endif; ?>

            </div>
        </div>

    </main>

    <!-- JavaScript scrollbar -->
    <script src="assets/js/script.js"></script>

</body>
</html>
