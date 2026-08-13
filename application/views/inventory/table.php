<div class="table-responsive">
    <table class="table table-hover align-middle text-start mb-0">
        <thead class="bg-light">
            <tr>
                <th class="ps-3 pe-2 text-center" style="width: 40px;">
                    <input type="checkbox" id="select-all-assets" class="form-check-input" style="cursor: pointer;" title="Pilih Semua Aset di Halaman Ini">
                </th>
                <th class="ps-3 pointer" style="cursor:pointer"
                    hx-get="<?php echo base_url('inventory'); ?>?sort=<?php echo ($sort_current == 'name') ? '-name' : 'name'; ?>"
                    hx-target="#asset-table-body" hx-push-url="true" hx-include="#filter-form">
                    Nama Aset 
                    <?php if ($sort_current == 'name'): ?><i class="bi bi-sort-alpha-down"></i>
                    <?php elseif ($sort_current == '-name'): ?><i class="bi bi-sort-alpha-up-alt"></i>
                    <?php else: ?><i class="bi bi-arrow-down-up opacity-50"></i><?php endif; ?>
                </th>
                
                <th>Barcode / SN</th>            
                <th class="d-none d-md-table-cell">Kategori</th>
                <th class="d-none d-md-table-cell">Lokasi</th>
                <th>User / Dept</th>
                
                <th class="d-none d-xl-table-cell">Prev. User & Dept</th> 
                
                <th class="pointer" style="cursor:pointer"
                    hx-get="<?php echo base_url('inventory'); ?>?sort=<?php echo ($sort_current == 'status') ? '-status' : 'status'; ?>"
                    hx-target="#asset-table-body" hx-push-url="true" hx-include="#filter-form">
                    Status
                </th>

                <th class="d-none d-lg-table-cell text-nowrap">Harga</th>

                <th class="text-center pointer" style="min-width: 120px; cursor:pointer"
                    hx-get="<?php echo base_url('inventory'); ?>?sort=<?php echo ($sort_current == '-created_at') ? 'created_at' : '-created_at'; ?>"
                    hx-target="#asset-table-body" hx-push-url="true" hx-include="#filter-form">
                    Aksi
                </th> 
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($assets)): ?>
                <?php foreach ($assets as $asset): ?>
                <tr>
                    <td class="ps-3 pe-2 text-center" onclick="event.stopPropagation();">
                        <input type="checkbox" class="form-check-input asset-checkbox" value="<?php echo $asset->id; ?>" style="cursor: pointer;">
                    </td>
                    <td class="ps-3">
                        <div class="fw-bold text-dark"><?php echo character_limiter($asset->name, 30); ?></div>
                        <small class="text-muted d-block d-md-none font-monospace mt-1"><?php echo $asset->serial_number; ?></small>
                    </td>
                    <td>
                        <?php if ($asset->barcode_id): ?>
                            <div class="badge bg-light text-dark border mb-1 font-monospace" style="font-size: 0.75rem;"><?php echo $asset->barcode_id; ?></div>
                        <?php endif; ?>
                        <div class="small text-muted font-monospace d-none d-md-block"><?php echo $asset->serial_number; ?></div>
                    </td>
                    <td class="d-none d-md-table-cell text-muted small"><?php echo $asset->kategori_nama ?: '-'; ?></td>
                    <td class="d-none d-md-table-cell text-muted small"><?php echo $asset->lokasi_nama ?: '-'; ?></td>
                    <td>
                        <?php if ($asset->current_user): ?>
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary fw-bold me-2 border border-primary-subtle" 
                                     style="width: 38px; height: 38px; min-width: 38px; font-size: 0.9rem;">
                                    <?php echo strtoupper(substr($asset->current_user, 0, 1)); ?>
                                </div>
                                
                                <div>
                                    <div class="fw-bold text-dark small" style="line-height: 1.2;"><?php echo character_limiter($asset->current_user, 20); ?></div>
                                    <div class="text-muted" style="font-size: 0.75rem; line-height: 1.2;"><?php echo character_limiter($asset->current_dept ?: '-', 15); ?></div>
                                </div>
                            </div>
                        <?php else: ?>
                            <span class="badge bg-light text-secondary border font-monospace fw-normal" style="font-size: 0.75rem;">
                                <i class="bi bi-box-seam me-1"></i> Storage
                            </span>
                        <?php endif; ?>
                    </td>
                    
                    <td class="d-none d-xl-table-cell text-muted small">
                        <?php if ($asset->prev_user): ?>
                            <div class="d-flex align-items-center text-secondary">
                                <i class="bi bi-person-exclamation me-1 opacity-75"></i>
                                <span><?php echo character_limiter($asset->prev_user, 18); ?></span>
                            </div>
                            <?php if ($asset->prev_dept): ?>
                                <div class="text-muted ps-3" style="font-size: 0.75rem;">
                                    <i class="bi bi-building me-1 opacity-50"></i><?php echo character_limiter($asset->prev_dept, 15); ?>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted opacity-50">&mdash;</span>
                        <?php endif; ?>
                    </td>
                    
                    <td>
                        <?php 
                            $badge_class = 'bg-secondary';
                            if ($asset->status == 'TERSEDIA') $badge_class = 'bg-success bg-opacity-10 text-success border-success-subtle';
                            elseif ($asset->status == 'DIPAKAI') $badge_class = 'bg-success bg-opacity-10 text-success border-success-subtle';
                            elseif ($asset->status == 'RUSAK') $badge_class = 'bg-danger bg-opacity-10 text-danger border-danger-subtle';
                            elseif ($asset->status == 'HILANG') $badge_class = 'bg-dark bg-opacity-10 text-dark border-secondary-subtle';
                            elseif ($asset->status == 'TIDAK_LAYAK') $badge_class = 'bg-warning bg-opacity-10 text-warning border-warning-subtle';
                            
                            $status_label = $status_choices[$asset->status] ?? $asset->status;
                        ?>
                        <span class="badge <?php echo $badge_class; ?> border rounded-pill px-2.5 py-1.5 fw-medium" style="font-size: 0.75rem;">
                            <?php echo $status_label; ?>
                        </span>
                    </td>
                    
                    <td class="d-none d-lg-table-cell font-monospace fw-medium text-dark text-nowrap">
                        Rp <?php echo number_format($asset->price ?: 0, 0, ',', '.'); ?>
                    </td>
                    
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2 align-items-center">
                            <a href="<?php echo base_url('asset/' . $asset->id . '/detail'); ?>" hx-boost="false" class="text-primary" title="Detail" data-bs-toggle="tooltip">
                                <i class="bi bi-eye fs-5"></i>
                            </a>
                            <a href="<?php echo base_url('edit/' . $asset->id); ?>" hx-boost="false" class="text-warning" title="Edit" data-bs-toggle="tooltip">
                                <i class="bi bi-pencil-square fs-5"></i>
                            </a>
                            
                            <form action="<?php echo base_url('hapus/' . $asset->id); ?>" method="POST" class="delete-asset-form" data-name="<?php echo htmlspecialchars($asset->name); ?>" style="margin: 0; display: inline;">
                                <button type="submit" class="btn btn-link text-danger p-0 border-0" style="text-decoration: none;" title="Hapus" data-bs-toggle="tooltip">
                                    <i class="bi bi-trash fs-5"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="text-center py-5">
                        <div class="mb-3 text-muted opacity-25"><i class="bi bi-inbox" style="font-size: 3rem;"></i></div>
                        <h6 class="fw-bold text-dark">Data Tidak Ditemukan</h6>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($total_pages > 1): ?>
<div class="d-flex justify-content-between align-items-center p-3 border-top bg-white">
    <small class="text-muted">Hal <?php echo $current_page; ?> dari <?php echo $total_pages; ?></small>
    <div class="btn-group">
        <?php if ($current_page > 1): ?>
        <button class="btn btn-sm btn-outline-secondary" hx-get="<?php echo base_url('inventory'); ?>?page=<?php echo ($current_page - 1); ?>" hx-include="#filter-form" hx-target="#asset-table-body" hx-push-url="true" hx-swap="innerHTML">
            <i class="bi bi-chevron-left"></i> Prev
        </button>
        <?php endif; ?>
        <?php if ($current_page < $total_pages): ?>
        <button class="btn btn-sm btn-outline-secondary" hx-get="<?php echo base_url('inventory'); ?>?page=<?php echo ($current_page + 1); ?>" hx-include="#filter-form" hx-target="#asset-table-body" hx-push-url="true" hx-swap="innerHTML">
            Next <i class="bi bi-chevron-right"></i>
        </button>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
<span id="total-asset-count" hx-swap-oob="true" class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill border border-primary-subtle">
    <i class="bi bi-box-seam me-1"></i> Total Data: <?php echo $total_rows; ?> Unit
</span>
