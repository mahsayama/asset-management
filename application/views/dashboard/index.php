<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-medium d-block mb-1">Total Unit Aset</span>
                    <h3 class="fw-bold text-dark mb-0"><?php echo number_format($total_assets); ?></h3>
                </div>
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-box-seam fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-medium d-block mb-1">Sedang Dipakai</span>
                    <h3 class="fw-bold text-success mb-0"><?php echo number_format($count_dipakai); ?></h3>
                </div>
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-person-check fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-medium d-block mb-1">Status Tersedia</span>
                    <h3 class="fw-bold text-warning mb-0"><?php echo number_format($count_tersedia); ?></h3>
                </div>
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-archive fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-medium d-block mb-1">Kondisi Rusak</span>
                    <h3 class="fw-bold text-danger mb-0"><?php echo number_format($count_rusak); ?></h3>
                </div>
                <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-exclamation-triangle fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-medium d-block mb-1">Tidak Layak Pakai</span>
                    <h3 class="fw-bold text-dark mb-0"><?php echo number_format($count_tidak_layak); ?></h3>
                </div>
                <div class="rounded-circle bg-dark bg-opacity-10 text-dark d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-x-circle fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white p-4 border-bottom-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark m-0">Aset Terbaru Didaftarkan</h5>
                <a href="<?php echo base_url('inventory'); ?>" class="btn btn-sm btn-light border rounded-pill px-3">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-start mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Nama Aset</th>
                                <th>Barcode / SN</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>User / Dept</th>
                                <th>Status</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_assets)): ?>
                                <?php foreach ($recent_assets as $asset): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark"><?php echo character_limiter($asset->name, 30); ?></div>
                                        </td>
                                        <td>
                                            <?php if ($asset->barcode_id): ?>
                                                <span class="badge bg-light text-dark border font-monospace"><?php echo $asset->barcode_id; ?></span>
                                            <?php endif; ?>
                                            <small class="text-muted font-monospace d-block"><?php echo $asset->serial_number; ?></small>
                                        </td>
                                        <td class="text-muted small"><?php echo $asset->kategori_nama ?: '-'; ?></td>
                                        <td class="text-muted small"><?php echo $asset->lokasi_nama ?: '-'; ?></td>
                                        <td>
                                            <?php if ($asset->current_user): ?>
                                                <div class="fw-bold text-dark small"><?php echo $asset->current_user; ?></div>
                                                <small class="text-muted" style="font-size: 0.72rem;"><?php echo $asset->current_dept ?: '-'; ?></small>
                                            <?php else: ?>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-2.5 py-1 fw-normal small">Storage</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($asset->status == 'DIPAKAI'): ?>
                                                <span class="badge bg-success-subtle text-success border rounded-pill px-3 py-1">Sedang Dipakai</span>
                                            <?php elseif ($asset->status == 'TERSEDIA'): ?>
                                                <span class="badge bg-warning-subtle text-warning border rounded-pill px-3 py-1">Tersedia</span>
                                            <?php elseif ($asset->status == 'RUSAK'): ?>
                                                <span class="badge bg-danger-subtle text-danger border rounded-pill px-3 py-1">Rusak</span>
                                            <?php elseif ($asset->status == 'TIDAK_LAYAK'): ?>
                                                <span class="badge bg-dark bg-opacity-10 text-dark border rounded-pill px-3 py-1">Tidak Layak Pakai</span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark border rounded-pill px-3 py-1"><?php echo $asset->status; ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center pe-4">
                                            <a href="<?php echo base_url('asset/' . $asset->id . '/detail'); ?>" hx-boost="false" class="text-primary" title="Detail">
                                                <i class="bi bi-eye fs-5"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Belum ada data aset.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
