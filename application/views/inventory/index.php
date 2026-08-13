<div class="inventory-sticky-header mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div class="d-flex align-items-center order-1">
            <button class="btn btn-toggle-sidebar shadow-sm rounded-3 flex-shrink-0" id="sidebarToggle" onclick="toggleSidebar(event)">
                <i class="bi bi-layout-sidebar fs-5 icon-expanded"></i>
                <i class="bi bi-layout-sidebar-inset fs-5 icon-collapsed"></i>
            </button>
        </div>

        <div class="d-flex align-items-center gap-3 order-2 order-md-3 ms-auto flex-shrink-0">
            <div class="profile-avatar">
                <i class="bi bi-person-fill fs-5"></i>
            </div>
            <div>
                <div class="fw-bold small text-dark"><?php echo ucfirst($this->session->userdata('user_name') ?: 'Admin'); ?></div>
                <div class="text-muted" style="font-size: 0.75rem; font-weight: 500;">Administrator</div>
            </div>
        </div>

        <div class="header-title order-3 order-md-2 mt-1 mt-md-0 d-flex align-items-center ps-md-3">
            <div>
                <h4 class="fw-bold text-dark m-0"><?php echo isset($title) ? $title : 'Inventory Aset'; ?></h4>
                <p class="text-muted small m-0"><?php echo isset($subtitle) ? $subtitle : 'Kelola database seluruh aset IT.'; ?></p>
            </div>
        </div>
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
        <div>
            <span id="total-asset-count" class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill border border-primary-subtle d-inline-block">
                <i class="bi bi-box-seam me-1"></i> Total Data: <?php echo $total_rows; ?> Unit
            </span>
        </div>
        
        <div class="d-flex gap-2 w-100 justify-content-md-end">
            <button type="button" id="btn-bulk-delete" class="btn btn-danger rounded-pill px-3 px-md-4 shadow-sm" style="display: none;">
                <i class="bi bi-trash me-1"></i> Hapus <span id="bulk-selected-count">0</span> Aset
            </button>

            <button type="button" class="btn btn-outline-success rounded-pill px-3 px-md-4 shadow-sm flex-grow-1 flex-md-grow-0" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-file-earmark-excel me-1"></i> Import
            </button>
            
            <a href="<?php echo base_url('tambah'); ?>" class="btn btn-primary rounded-pill px-3 px-md-4 shadow-sm flex-grow-1 flex-md-grow-0">
                <i class="bi bi-plus-lg me-1"></i> Tambah
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
            <form id="filter-form" method="get" action="<?php echo base_url('inventory'); ?>" class="row g-2" 
                  hx-get="<?php echo base_url('inventory'); ?>?page=1" 
                  hx-target="#asset-table-body" 
                  hx-swap="innerHTML" 
                  hx-push-url="true"
                  hx-indicator="#asset-table-body"
                  hx-trigger="change, submit, keyup delay:500ms from:input[name='q'], refreshTable from:body">
                
                <input type="hidden" name="sort" value="<?php echo $sort_current; ?>">
                
                <div class="col-md-2">
                    <select name="category" class="form-select border-0 bg-light fw-medium text-secondary">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($kategori_list as $cat): ?>
                            <option value="<?php echo $cat->id; ?>" <?php echo ($category_filter == $cat->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat->nama); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <select name="location" class="form-select border-0 bg-light fw-medium text-secondary">
                        <option value="">Semua Lokasi</option>
                        <?php foreach ($lokasi_list as $loc): ?>
                            <option value="<?php echo $loc->id; ?>" <?php echo ($location_filter == $loc->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($loc->nama); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select border-0 bg-light fw-medium text-secondary">
                        <option value="">Semua Status</option>
                        <?php foreach ($status_choices as $code => $label): ?>
                            <option value="<?php echo $code; ?>" <?php echo ($status_filter == $code) ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-light text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" class="form-control border-0 bg-light shadow-none" placeholder="Cari Barcode, SN, user..." value="<?php echo htmlspecialchars($query); ?>">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0" id="asset-table-body">
        <?php $this->load->view('inventory/table'); ?>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-header-title fw-bold text-dark" id="importModalLabel">Import Data Aset Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo base_url('inventory/import'); ?>" method="POST" enctype="multipart/form-data" hx-boost="false">
                <div class="modal-body py-3">
                    <p class="text-muted small mb-3">
                        Upload file spreadsheet (.xlsx / .csv) sesuai template untuk menambahkan aset secara masal.
                    </p>
                    <div class="mb-3">
                        <label for="excel_file" class="form-label small fw-semibold">Pilih File Excel (.xlsx)</label>
                        <input class="form-control" type="file" id="excel_file" name="excel_file" accept=".xlsx, .xls, .csv" required>
                    </div>
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="fw-bold d-block text-dark">Belum Punya Format Template?</small>
                                <small class="text-muted">Unduh file contoh format kolom import.</small>
                            </div>
                            <a href="<?php echo base_url('inventory/import/template'); ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="bi bi-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <i class="bi bi-upload me-1"></i> Unggah & Proses
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Bulk Delete & Select All Checkbox Handler
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'select-all-assets') {
            const isChecked = e.target.checked;
            document.querySelectorAll('.asset-checkbox').forEach(cb => {
                cb.checked = isChecked;
            });
            updateBulkDeleteBtn();
        }
        
        if (e.target && e.target.classList.contains('asset-checkbox')) {
            const allBoxes = document.querySelectorAll('.asset-checkbox');
            const checkedBoxes = document.querySelectorAll('.asset-checkbox:checked');
            const selectAll = document.getElementById('select-all-assets');
            if (selectAll) {
                selectAll.checked = (allBoxes.length > 0 && checkedBoxes.length === allBoxes.length);
            }
            updateBulkDeleteBtn();
        }
    });

    function updateBulkDeleteBtn() {
        const checkedBoxes = document.querySelectorAll('.asset-checkbox:checked');
        const bulkBtn = document.getElementById('btn-bulk-delete');
        const countSpan = document.getElementById('bulk-selected-count');
        
        if (bulkBtn) {
            if (checkedBoxes.length > 0) {
                bulkBtn.style.display = 'inline-block';
                if (countSpan) countSpan.innerText = checkedBoxes.length;
            } else {
                bulkBtn.style.display = 'none';
            }
        }
    }

    // Reset bulk delete button state after HTMX table swaps
    document.body.addEventListener('htmx:afterSwap', function(evt) {
        if (evt.detail.target.id === 'asset-table-body') {
            updateBulkDeleteBtn();
        }
    });

    // Bulk Delete Click Handler with SweetAlert2
    document.addEventListener('click', function(e) {
        const bulkBtn = e.target.closest('#btn-bulk-delete');
        if (bulkBtn) {
            e.preventDefault();
            const checkedBoxes = document.querySelectorAll('.asset-checkbox:checked');
            const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);
            if (selectedIds.length === 0) return;

            Swal.fire({
                title: 'Hapus ' + selectedIds.length + ' Aset Terpilih?',
                text: "Seluruh data aset terpilih dan riwayat aktivitasnya akan dihapus permanen dari sistem.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus ' + selectedIds.length + ' Aset!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    selectedIds.forEach(id => formData.append('ids[]', id));

                    fetch('<?php echo base_url("inventory/bulk_delete"); ?>', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (response.ok && data.success) {
                            htmx.trigger('#filter-form', 'submit');
                            bulkBtn.style.display = 'none';
                            document.body.dispatchEvent(new CustomEvent('showToast', {
                                detail: { message: data.message || 'Aset terpilih berhasil dihapus.', tags: 'danger' }
                            }));
                        } else {
                            document.body.dispatchEvent(new CustomEvent('showToast', {
                                detail: { message: data.message || 'Gagal menghapus aset.', tags: 'danger' }
                            }));
                        }
                    })
                    .catch(err => {
                        document.body.dispatchEvent(new CustomEvent('showToast', {
                            detail: { message: 'Gagal menghapus aset.', tags: 'danger' }
                        }));
                    });
                }
            });
        }
    });

    // Confirmation dialog before deleting an asset in inventory table
    document.body.addEventListener('click', function(e) {
        const form = e.target.closest('.delete-asset-form');
        if (form && e.target.closest('button[type="submit"]')) {
            e.preventDefault();
            e.stopPropagation();

            const assetName = form.getAttribute('data-name');

            Swal.fire({
                title: 'Hapus Aset ' + assetName + '?',
                text: "Data aset dan riwayat aktivitasnya akan dihapus permanen dari sistem.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus Aset!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (response.ok && data.success) {
                            htmx.trigger('#filter-form', 'submit');
                            document.body.dispatchEvent(new CustomEvent('showToast', {
                                detail: { message: data.message || ('Aset ' + assetName + ' telah berhasil dihapus.'), tags: 'danger' }
                            }));
                        } else {
                            document.body.dispatchEvent(new CustomEvent('showToast', {
                                detail: { message: data.message || 'Gagal menghapus aset.', tags: 'danger' }
                            }));
                        }
                    })
                    .catch(err => {
                        document.body.dispatchEvent(new CustomEvent('showToast', {
                            detail: { message: 'Gagal menghapus aset.', tags: 'danger' }
                        }));
                    });
                }
            });
        }
    });
</script>
