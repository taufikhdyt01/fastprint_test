<!DOCTYPE html>
<html>

<head>
    <title>Daftar Produk - FastPrint</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        /* DataTables Custom Styling */
        .dataTables_length {
            padding: 1rem 1.5rem;
        }

        .dataTables_filter {
            padding: 1rem 1.5rem;
        }

        .dataTables_info {
            padding: 1rem 1.5rem;
        }

        .dataTables_paginate {
            padding: 1rem 1.5rem;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 2px solid #f8f9fa;
            padding: 1.5rem;
        }

        .btn-primary {
            background-color: #4361ee;
            border-color: #4361ee;
            padding: 0.5rem 1.5rem;
        }

        .btn-primary:hover {
            background-color: #3046eb;
            border-color: #3046eb;
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

        .filter-status {
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-print me-2"></i>FastPrint
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $this->uri->segment(2) == '' || $this->uri->segment(2) == 'index' ? 'active' : ''; ?>" href="<?php echo base_url('products'); ?>">Semua Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $this->uri->segment(2) == 'sellable' ? 'active' : ''; ?>" href="<?php echo base_url('products/sellable'); ?>">Produk Bisa Dijual</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Daftar Produk</h2>
                <p class="text-muted mb-0">Kelola produk FastPrint</p>
            </div>
            <div>
                <a href="<?php echo base_url('products/add'); ?>" class="btn btn-primary me-2">
                    <i class="fas fa-plus me-2"></i>Tambah Produk
                </a>
                <a href="<?php echo base_url('products/sync_api'); ?>" class="btn btn-success">
                    <i class="fas fa-sync me-2"></i>Sync Data API
                </a>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo $this->session->flashdata('success'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php echo $this->session->flashdata('error'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

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
                                <td class="text-center"><?php echo $product->id_produk; ?></td>
                                <td>
                                    <div class="fw-semibold"><?php echo $product->nama_produk; ?></div>
                                </td>
                                <td class="text-center">
                                    <div class="text-primary fw-semibold">
                                        Rp <?php echo number_format($product->harga, 0, ',', '.'); ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info bg-opacity-10 text-info">
                                        <?php echo $product->nama_kategori; ?>
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
                                        <a href="<?php echo base_url('products/edit/' . $product->id_produk); ?>" class="btn btn-warning btn-sm"
                                            data-bs-toggle="tooltip" title="Edit Produk">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" onclick="confirmDelete('<?php echo base_url('products/delete/' . $product->id_produk); ?>')"
                                            class="btn btn-danger btn-sm" data-bs-toggle="tooltip" title="Hapus Produk">
                                            <i class="fas fa-trash"></i>
                                        </a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#productsTable').DataTable({
                columnDefs: [{
                        width: '80px',
                        targets: 0
                    }, // ID
                    {
                        width: '300px',
                        targets: 1
                    }, // Nama Produk
                    {
                        width: '150px',
                        targets: 2
                    }, // Harga
                    {
                        width: '120px',
                        targets: 3
                    }, // Kategori
                    {
                        width: '120px',
                        targets: 4
                    }, // Status
                    {
                        width: '120px',
                        targets: 5
                    } // Aksi
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
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });

        function confirmDelete(deleteUrl) {
            if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
                window.location.href = deleteUrl;
            }
        }
    </script>
</body>

</html>
