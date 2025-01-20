<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Daftar Produk - FastPrint</title>
    
    <!-- Security Headers -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self' https: 'unsafe-inline' 'unsafe-eval'; img-src 'self' https: data:;">
    <meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">
    
    <!-- External CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4361ee;
            --hover-color: #3046eb;
            --shadow-color: rgba(0, 0, 0, 0.1);
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            padding: 1rem 1.5rem;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .card {
            border: none;
            box-shadow: 0 0 15px var(--shadow-color);
            border-radius: 10px;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 2px solid #f8f9fa;
            padding: 1.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.5rem 1.5rem;
        }

        .btn-primary:hover {
            background-color: var(--hover-color);
            border-color: var(--hover-color);
        }

        .table th {
            font-weight: 600;
            color: #495057;
        }

        .badge {
            padding: 0.5em 1em;
            font-weight: 500;
        }

        .action-buttons .btn {
            padding: 0.4rem 0.8rem;
            margin: 0 0.2rem;
        }

        .alert {
            border-radius: 10px;
            padding: 1rem 1.5rem;
        }
    </style>
</head>

<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?= html_escape(base_url()) ?>">
                <i class="fas fa-print me-2"></i>FastPrint
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?= ($this->uri->segment(2) == '' || $this->uri->segment(2) == 'index') ? 'active' : '' ?>" 
                           href="<?= html_escape(base_url('products')) ?>">
                            Semua Produk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($this->uri->segment(2) == 'sellable') ? 'active' : '' ?>" 
                           href="<?= html_escape(base_url('products/sellable')) ?>">
                            Produk Bisa Dijual
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Daftar Produk</h1>
                <p class="text-muted mb-0">Kelola produk FastPrint</p>
            </div>
            <div>
                <a href="<?= html_escape(base_url('products/add')) ?>" class="btn btn-primary me-2">
                    <i class="fas fa-plus me-2"></i>Tambah Produk
                </a>
                <a href="<?= html_escape(base_url('products/sync_api')) ?>" 
                   class="btn btn-success"
                   data-bs-toggle="tooltip" 
                   title="Sinkronisasi data dengan API">
                    <i class="fas fa-sync me-2"></i>Sync Data API
                </a>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php $this->load->view('partials/alerts'); ?>

        <!-- Main Content Card -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover" id="productsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">ID</th>
                                <th>Nama Produk</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Kategori</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($products as $product): ?>
                            <tr>
                                <td class="text-center"><?= html_escape($product->id_produk) ?></td>
                                <td>
                                    <div class="fw-semibold"><?= html_escape($product->nama_produk) ?></div>
                                </td>
                                <td class="text-center">
                                    <div class="text-primary fw-semibold">
                                        Rp <?= number_format($product->harga, 0, ',', '.') ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info bg-opacity-10 text-info">
                                        <?= html_escape($product->nama_kategori) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if($product->nama_status == 'bisa dijual'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success">
                                            <i class="fas fa-check-circle me-1"></i>Bisa Dijual
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger">
                                            <i class="fas fa-times-circle me-1"></i>Tidak Bisa Dijual
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <a href="<?= html_escape(base_url('products/edit/' . $product->id_produk)) ?>" 
                                           class="btn btn-warning btn-sm"
                                           data-bs-toggle="tooltip" 
                                           title="Edit Produk">
                                            <i class="fas fa-edit"></i>
                                            <span class="visually-hidden">Edit</span>
                                        </a>
                                        <button type="button"
                                                class="btn btn-danger btn-sm delete-product"
                                                data-id="<?= html_escape($product->id_produk) ?>"
                                                data-name="<?= html_escape($product->nama_produk) ?>"
                                                data-bs-toggle="tooltip" 
                                                title="Hapus Produk">
                                            <i class="fas fa-trash"></i>
                                            <span class="visually-hidden">Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus produk <span id="deleteProductName" class="fw-bold"></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="#" id="deleteProductBtn" class="btn btn-danger">Hapus</a>
                </div>
            </div>
        </div>
    </div>

    <!-- External Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <!-- Custom Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTables
            const productsTable = new DataTable('#productsTable', {
                columnDefs: [
                    { width: '80px', targets: 0 },  // ID
                    { width: '300px', targets: 1 }, // Nama Produk
                    { width: '150px', targets: 2 }, // Harga
                    { width: '120px', targets: 3 }, // Kategori
                    { width: '120px', targets: 4 }, // Status
                    { width: '120px', targets: 5 }  // Aksi
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
                },
                pageLength: 10,
                ordering: true,
                responsive: true,
                autoWidth: false
            });

            // Initialize tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));

            // Delete product handling
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            const deleteButtons = document.querySelectorAll('.delete-product');
            const deleteProductName = document.getElementById('deleteProductName');
            const deleteProductBtn = document.getElementById('deleteProductBtn');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.dataset.id;
                    const productName = this.dataset.name;
                    
                    deleteProductName.textContent = productName;
                    deleteProductBtn.href = `<?= base_url('products/delete/') ?>${productId}`;
                    deleteModal.show();
                });
            });

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
</body>
</html>