<div class="container-fluid px-0">
    <a href="<?php echo base_url('inventory'); ?>" class="btn btn-sm btn-outline-secondary mb-4 rounded-pill px-3 py-1.5 shadow-sm d-inline-flex align-items-center">
        <i class="bi bi-arrow-left me-1.5"></i>Kembali ke Dashboard
    </a>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-laptop fs-1 text-primary bg-primary bg-opacity-10 p-4 rounded-circle d-inline-block mb-2"></i>

                        <h4 class="mt-3 fw-bold mb-1"><?php echo htmlspecialchars($asset->name); ?></h4>
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-monospace">
                            <i class="bi bi-upc-scan me-1"></i><?php echo $asset->barcode_id ?: 'No Barcode'; ?>
                        </span>
                    </div>

                    <hr class="border-secondary opacity-10">

                    <div class="d-flex justify-content-between mb-3">
                        <small class="text-muted">Serial Number</small>
                        <span class="fw-bold text-dark font-monospace"><?php echo htmlspecialchars($asset->serial_number); ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <small class="text-muted">Tanggal Pembelian</small>
                        <span class="fw-medium text-dark">
                            <i class="bi bi-calendar-event me-1 text-muted"></i> 
                            <?php echo $asset->purchase_date ? date('d F Y', strtotime($asset->purchase_date)) : '-'; ?>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <small class="text-muted">Harga Beli</small>
                        <span class="fw-bold text-success">
                            Rp <?php echo number_format($asset->price ?: 0, 0, ',', '.'); ?>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Status</small>
                        <span class="badge bg-primary rounded-pill px-3"><?php echo $status_display; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white p-4 border-bottom-0 d-flex flex-column flex-sm-row gap-2 justify-content-between align-items-start align-items-sm-center">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Aset
                    </h5>
                    <a href="<?php echo base_url('edit/' . $asset->id); ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                        <i class="bi bi-pencil-square me-1"></i> Edit Aset
                    </a>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="timeline mt-2">
                        <?php if (!empty($histories)): ?>
                            <?php foreach ($histories as $history): ?>
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div class="d-flex flex-column flex-sm-row justify-content-between mb-1 align-items-start align-items-sm-center gap-1.5">
                                    <span class="fw-bold text-dark d-flex align-items-center">
                                        <i class="bi bi-person-circle me-2 text-secondary"></i>
                                        <?php echo ucfirst($history->user_name ?: ($history->user_email ?: 'System')); ?>
                                    </span>
                                    <small class="text-muted bg-light px-2 py-0.5 rounded border" style="font-size: 0.72rem;">
                                        <?php echo $history->event_date ? date('d M Y, H:i', strtotime($history->event_date)) : '-'; ?>
                                    </small>
                                </div>
                                <div class="bg-light p-3 rounded-3 border border-light-subtle mt-2">
                                    <p class="text-secondary mb-0 small"><?php echo htmlspecialchars($history->description); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-hourglass text-muted fs-1 opacity-25"></i>
                                <p class="text-muted mt-2">Belum ada riwayat tercatat.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline { border-left: 2px solid #e9ecef; margin-left: 10px; padding-left: 30px; position: relative; }
    .timeline-item { position: relative; margin-bottom: 2rem; }
    .timeline-item:last-child { margin-bottom: 0; }
    
    .timeline-dot {
        width: 14px; height: 14px; 
        background: #fff; 
        border: 3px solid #0d6efd; 
        border-radius: 50%;
        position: absolute; 
        left: -38px; 
        top: 4px; 
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
    }
    .card:hover { transform: translateY(-2px); transition: all 0.3s ease; }
</style>
