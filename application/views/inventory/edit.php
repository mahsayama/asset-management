<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white p-4 border-bottom-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Edit Data Aset</h5>
                    <p class="text-muted small mb-0">Perbarui rincian informasi aset IT perusahaan.</p>
                </div>
                <a href="<?php echo base_url('inventory'); ?>" class="btn btn-light btn-sm border rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <div class="card-body p-4 pt-0">
                <form action="<?php echo base_url('edit/' . $asset->id); ?>" method="POST" hx-boost="false">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex align-items-center gap-2 border-bottom pb-2 mb-2">
                                <i class="bi bi-info-circle text-primary"></i>
                                <h6 class="text-primary fw-bold text-uppercase small mb-0">Informasi Utama</h6>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="fw-medium text-secondary small mb-2">Nama Aset / Perangkat <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control bg-white shadow-sm" style="padding: 0.7rem 1rem;" value="<?php echo htmlspecialchars($asset->name); ?>" required autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-medium text-secondary small mb-2">ID Barcode</label>
                            <input type="text" name="barcode_id" class="form-control bg-white shadow-sm" style="padding: 0.7rem 1rem;" value="<?php echo htmlspecialchars($asset->barcode_id ?? ''); ?>" autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-medium text-secondary small mb-2">Serial Number <span class="text-danger">*</span></label>
                            <input type="text" name="serial_number" class="form-control bg-white shadow-sm" style="padding: 0.7rem 1rem;" value="<?php echo htmlspecialchars($asset->serial_number); ?>" required autocomplete="off">
                        </div>

                        <div class="col-12 mt-4">
                            <div class="d-flex align-items-center gap-2 border-bottom pb-2 mb-2">
                                <i class="bi bi-geo-alt text-primary"></i>
                                <h6 class="text-primary fw-bold text-uppercase small mb-0">Lokasi & Status</h6>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-medium text-secondary small mb-2">Kategori</label>
                            <select name="kategori_id" id="id_kategori" placeholder="Pilih Kategori...">
                                <option value="" disabled <?php echo empty($asset->kategori_id) ? 'selected' : ''; ?>>Pilih Kategori...</option>
                                <?php foreach ($kategoriList as $cat): ?>
                                    <option value="<?php echo $cat->id; ?>" <?php echo ($asset->kategori_id == $cat->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat->nama); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-medium text-secondary small mb-2">Lokasi Penempatan</label>
                            <select name="lokasi_id" id="id_lokasi" placeholder="Pilih Lokasi...">
                                <option value="" disabled <?php echo empty($asset->lokasi_id) ? 'selected' : ''; ?>>Pilih Lokasi...</option>
                                <?php foreach ($lokasiList as $loc): ?>
                                    <option value="<?php echo $loc->id; ?>" <?php echo ($asset->lokasi_id == $loc->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($loc->nama); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="fw-medium text-secondary small mb-2">Status Kondisi</label>
                            <select name="status" class="form-select bg-white shadow-sm" style="padding: 0.7rem 1rem;">
                                <?php foreach ($statusChoices as $code => $label): ?>
                                    <option value="<?php echo $code; ?>" <?php echo ($asset->status == $code) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="d-flex align-items-center gap-2 border-bottom pb-2 mb-2">
                                <i class="bi bi-cash-coin text-primary"></i>
                                <h6 class="text-primary fw-bold text-uppercase small mb-0">Data Pembelian (Opsional)</h6>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-medium text-secondary small mb-2">Tanggal Pembelian</label>
                            <input type="date" name="purchase_date" class="form-control bg-white shadow-sm" style="padding: 0.7rem 1rem;" value="<?php echo $asset->purchase_date ? date('Y-m-d', strtotime($asset->purchase_date)) : ''; ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="fw-medium text-secondary small mb-2">Harga Aset</label>
                            <div class="input-group shadow-sm" style="border-radius: 0.5rem; overflow: hidden;">
                                <span class="input-group-text bg-light text-muted fw-bold border-end-0 ps-3 pe-2">Rp</span>
                                <input type="text" id="price_display" class="form-control bg-white border-start-0" style="padding: 0.7rem 1rem;" value="<?php echo $asset->price ? number_format($asset->price, 0, ',', '.') : ''; ?>" autocomplete="off">
                                <input type="hidden" name="price" id="price_real" value="<?php echo $asset->price ?: ''; ?>">
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="d-flex align-items-center gap-2 border-bottom pb-2 mb-2">
                                <i class="bi bi-people text-primary"></i>
                                <h6 class="text-primary fw-bold text-uppercase small mb-0">Pengguna (User)</h6>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <label class="fw-bold text-dark small mb-2 d-block">User Saat Ini (PIC Active)</label>
                                <div class="mb-3">
                                    <label class="text-muted small mb-1">Nama User / PIC</label>
                                    <input type="text" name="current_user" class="form-control bg-white shadow-sm" style="padding: 0.7rem 1rem;" value="<?php echo htmlspecialchars($asset->current_user ?? ''); ?>" placeholder="User penanggung jawab aktif">
                                </div>
                                <div>
                                    <label class="text-muted small mb-1">Departemen</label>
                                    <input type="text" name="current_dept" class="form-control bg-white shadow-sm" style="padding: 0.7rem 1rem;" value="<?php echo htmlspecialchars($asset->current_dept ?? ''); ?>" placeholder="Divisi user saat ini">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 border">
                                <label class="fw-bold text-dark small mb-2 d-block">User Sebelumnya (History)</label>
                                <div class="mb-3">
                                    <label class="text-muted small mb-1">Nama User Lama</label>
                                    <input type="text" name="prev_user" class="form-control bg-white shadow-sm" style="padding: 0.7rem 1rem;" value="<?php echo htmlspecialchars($asset->prev_user ?? ''); ?>" placeholder="User sebelum penugasan saat ini">
                                </div>
                                <div>
                                    <label class="text-muted small mb-1">Departemen Lama</label>
                                    <input type="text" name="prev_dept" class="form-control bg-white shadow-sm" style="padding: 0.7rem 1rem;" value="<?php echo htmlspecialchars($asset->prev_dept ?? ''); ?>" placeholder="Divisi user lama">
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <label class="fw-medium text-secondary small mb-2">Keterangan Tambahan</label>
                            <textarea name="note" class="form-control bg-white shadow-sm" rows="3" style="padding: 0.7rem 1rem;"><?php echo htmlspecialchars($asset->note ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2.5 mt-5 pt-3 border-top">
                        <a href="<?php echo base_url('inventory'); ?>" class="btn btn-light border py-2.5 px-4 fw-medium rounded-pill order-2 order-sm-1">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary py-2.5 px-4 shadow-sm fw-medium rounded-pill order-1 order-sm-2">
                            <i class="bi bi-save me-1"></i> Perbarui Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    (function initTomSelects() {
        const catEl = document.getElementById('id_kategori');
        if (catEl && !catEl.tomselect) {
            new TomSelect('#id_kategori', {
                create: false
            });
        }

        const locEl = document.getElementById('id_lokasi');
        if (locEl && !locEl.tomselect) {
            new TomSelect('#id_lokasi', {
                create: false
            });
        }

        const priceDisplay = document.getElementById('price_display');
        const priceReal = document.getElementById('price_real');

        if (priceDisplay && priceReal) {
            priceDisplay.addEventListener('input', function(e) {
                let raw = this.value.replace(/\D/g, '');
                priceReal.value = raw;
                this.value = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
            });
        }
    })();
</script>
