<?php if($this->session->flashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <div class="d-flex">
        <div class="me-2">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <?= html_escape($this->session->flashdata('success')) ?>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if($this->session->flashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <div class="d-flex">
        <div class="me-2">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div>
            <?= html_escape($this->session->flashdata('error')) ?>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if($this->session->flashdata('warning')): ?>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <div class="d-flex">
        <div class="me-2">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div>
            <?= html_escape($this->session->flashdata('warning')) ?>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>