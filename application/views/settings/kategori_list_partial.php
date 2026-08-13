<div id="kategori-list-wrapper" class="d-flex flex-wrap gap-2">
    <?php if (!empty($kategoris)): ?>
        <?php foreach ($kategoris as $cat): ?>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-start-pill ps-3 pe-2" disabled>
                    <?php echo htmlspecialchars($cat->nama); ?>
                </button>
                
                <form action="<?php echo base_url('settings/delete/kategori/' . $cat->id); ?>" method="POST" class="delete-form" data-name="<?php echo htmlspecialchars($cat->nama); ?>" style="margin:0;">
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-end-pill ps-2 pe-3 border-start-0" title="Hapus">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <span class="text-muted small fst-italic">Belum ada data kategori.</span>
    <?php endif; ?>
</div>
