<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Tiket - Helpdesk</title>

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

                <div class="mb-4">
                    <h2 class="fw-bold">Tambah Tiket</h2>
                    <p class="text-secondary">
                        Isi formulir untuk melaporkan masalah IT.
                    </p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">   
                        <form action="store.php" method="POST">

                            <!-- Subject -->
                            <div class="mb-3">
                                <label for="subject" class="form-label">
                                    Subject <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="subject"
                                    id="subject"
                                    class="form-control"
                                    maxlength="255"
                                    placeholder="Masukkan subject tiket"
                                    required
                                >
                            </div>

                            <!-- Category -->
                            <div class="mb-3">
                                <label for="category" class="form-label">
                                    Category <span class="text-danger">*</span>
                                </label>

                                <select
                                    name="category"
                                    id="category"
                                    class="form-select"
                                    required
                                >
                                    <option value="" selected disabled>
                                        Pilih kategori
                                    </option>
                                    <option value="Hardware">Hardware</option>
                                    <option value="Software">Software</option>
                                    <option value="Network">Network</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    Description <span class="text-danger">*</span>
                                </label>

                                <textarea
                                    name="description"
                                    id="description"
                                    class="form-control"
                                    rows="4"
                                    placeholder="Jelaskan masalah yang terjadi"
                                    required
                                ></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                Submit Ticket
                            </button>

                            <a href="index.php" class="btn btn-secondary">
                                Cancel
                            </a>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </main>

</body>
</html>
