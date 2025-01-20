<!DOCTYPE html>
<html>
<head>
    <title><?php echo isset($product) ? 'Edit' : 'Tambah'; ?> Produk - FastPrint</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
        }
        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        .form-label {
            font-weight: 500;
            color: #495057;
        }
        .form-control, .form-select {
            padding: 0.75rem 1rem;
            border-radius: 8px;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
        .btn-primary {
            background-color: #4361ee;
            border-color: #4361ee;
            padding: 0.75rem 1.5rem;
        }
        .btn-primary:hover {
            background-color: #3046eb;
            border-color: #3046eb;
        }
        .btn-secondary {
            padding: 0.75rem 1.5rem;
        }
        .input-group-text {
            background-color: #f8f9fa;
        }
        .alert {
            border-radius: 10px;
            padding: 1rem 1.5rem;
        }
        .select2-container--bootstrap-5 .select2-selection {
            padding: 0.75rem 1rem;
            height: auto;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?php echo base_url('products'); ?>">
                <i class="fas fa-print me-2"></i>FastPrint
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $this->uri->segment(2) == '' || $this->uri->segment(2) == 'index' ? 'active' : ''; ?>" 
                           href="<?php echo base_url('products'); ?>">Semua Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $this->uri->segment(2) == 'sellable' ? 'active' : ''; ?>" 
                           href="<?php echo base_url('products/sellable'); ?>">Produk Bisa Dijual</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Header Section -->
        <div class="mb-4">
            <h2 class="mb-1"><?php echo isset($product) ? 'Edit' : 'Tambah'; ?> Produk</h2>
            <p class="text-muted mb-0">Silakan isi form di bawah ini dengan lengkap</p>
        </div>

        <!-- Validation Errors -->
        <?php if(validation_errors()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5 class="alert-heading">
                    <i class="fas fa-exclamation-circle me-2"></i>Terjadi Kesalahan
                </h5>
                <?php echo validation_errors(); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="<?php echo isset($product) ? base_url('products/update/'.$product->id_produk) : base_url('products/store'); ?>" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama_produk" class="form-label">Nama Produk</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-box"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       id="nama_produk" 
                                       name="nama_produk" 
                                       value="<?php echo isset($product) ? $product->nama_produk : set_value('nama_produk'); ?>" 
                                       required>
                            </div>
                            <div class="form-text">Masukkan nama produk dengan lengkap</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="harga" class="form-label">Harga</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="harga" 
                                       name="harga" 
                                       value="<?php echo isset($product) ? $product->harga : set_value('harga'); ?>" 
                                       required>
                            </div>
                            <div class="form-text">Masukkan harga dalam format angka</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="kategori_id" class="form-label">Kategori</label>
                            <select class="form-select select2" id="kategori_id" name="kategori_id" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach($categories as $category): ?>
                                    <option value="<?php echo $category->id_kategori; ?>"
                                        <?php echo (isset($product) && $product->kategori_id == $category->id_kategori) ? 'selected' : ''; ?>>
                                        <?php echo $category->nama_kategori; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status_id" class="form-label">Status</label>
                            <select class="form-select select2" id="status_id" name="status_id" required>
                                <option value="">Pilih Status</option>
                                <?php foreach($statuses as $status): ?>
                                    <option value="<?php echo $status->id_status; ?>"
                                        <?php echo (isset($product) && $product->status_id == $status->id_status) ? 'selected' : ''; ?>>
                                        <?php echo $status->nama_status; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="<?php echo base_url('products'); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?php echo isset($product) ? 'Update' : 'Simpan'; ?> Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap-5'
        });

        // Bootstrap 5 form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    });
    </script>
</body>
</html>