<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="<?= html_escape(base_url()) ?>">
            <i class="fas fa-print me-2"></i>FastPrint
        </a>
        <button class="navbar-toggler" 
                type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#navbarNav" 
                aria-controls="navbarNav" 
                aria-expanded="false" 
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?= ($this->uri->segment(2) == '' || $this->uri->segment(2) == 'index') ? 'active' : '' ?>" 
                       href="<?= html_escape(base_url('products')) ?>"
                       aria-current="<?= ($this->uri->segment(2) == '' || $this->uri->segment(2) == 'index') ? 'page' : 'false' ?>">
                        Semua Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($this->uri->segment(2) == 'sellable') ? 'active' : '' ?>" 
                       href="<?= html_escape(base_url('products/sellable')) ?>"
                       aria-current="<?= ($this->uri->segment(2) == 'sellable') ? 'page' : 'false' ?>">
                        Produk Bisa Dijual
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>