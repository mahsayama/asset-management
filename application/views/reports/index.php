<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-medium d-block mb-1">Total aset Terdaftar</span>
                    <h3 class="fw-bold text-dark mb-0"><?php echo number_format($total_assets); ?> Unit</h3>
                    <small class="text-muted" style="font-size: 0.75rem;">Seluruh kategori & lokasi</small>
                </div>
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-boxes fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-medium d-block mb-1">Total Estimasi Nilai</span>
                    <h3 class="fw-bold text-success mb-0">Rp <?php echo number_format($total_valuation, 0, ',', '.'); ?></h3>
                    <small class="text-muted" style="font-size: 0.75rem;">Valuasi Aset IT</small>
                </div>
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-wallet2 fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary bg-gradient text-white">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-white-50 small fw-medium d-block mb-1">Ekspor Data Rekap</span>
                    <h5 class="fw-bold mb-2">Download File CSV</h5>
                    <a href="<?php echo base_url('reports/export/csv'); ?>" hx-boost="false" class="btn btn-light text-primary btn-sm rounded-pill fw-bold px-3">
                        <i class="bi bi-download me-1"></i> Download CSV
                    </a>
                </div>
                <i class="bi bi-file-earmark-spreadsheet fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white p-4 border-bottom-0">
                <h5 class="fw-bold text-dark m-0">Sebaran Aset Per Kategori</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div style="height: 320px; position: relative;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white p-4 border-bottom-0">
                <h5 class="fw-bold text-dark m-0">Sebaran Aset Per Lokasi Penempatan</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div style="height: 320px; position: relative;">
                    <canvas id="locationChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function renderReportCharts() {
    if (typeof Chart === 'undefined') {
        setTimeout(renderReportCharts, 100);
        return;
    }

    const catCanvas = document.getElementById('categoryChart');
    const locCanvas = document.getElementById('locationChart');

    if (!catCanvas || !locCanvas) return;

    if (window.reportCategoryChartInstance) {
        window.reportCategoryChartInstance.destroy();
    }
    if (window.reportLocationChartInstance) {
        window.reportLocationChartInstance.destroy();
    }

    const catLabels = <?php echo json_encode(array_column($category_stats, 'nama')); ?>;
    const catData = <?php echo json_encode(array_column($category_stats, 'count')); ?>;

    const locLabels = <?php echo json_encode(array_column($location_stats, 'nama')); ?>;
    const locData = <?php echo json_encode(array_column($location_stats, 'count')); ?>;

    const palette = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#6f42c1', '#fd7e14', '#20c997', '#d63384'];

    window.reportCategoryChartInstance = new Chart(catCanvas, {
        type: 'doughnut',
        data: {
            labels: catLabels,
            datasets: [{
                data: catData,
                backgroundColor: palette.slice(0, catLabels.length)
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } }
            }
        }
    });

    window.reportLocationChartInstance = new Chart(locCanvas, {
        type: 'bar',
        data: {
            labels: locLabels,
            datasets: [{
                label: 'Jumlah Unit',
                data: locData,
                backgroundColor: '#4e73df',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } },
                x: { ticks: { autoSkip: false, maxRotation: 45, minRotation: 0 } }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', renderReportCharts);
} else {
    renderReportCharts();
}
</script>
