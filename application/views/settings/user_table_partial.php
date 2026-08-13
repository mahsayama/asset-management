<tbody id="user-table-body">
    <?php if (!empty($users)): ?>
        <?php foreach ($users as $u): ?>
        <tr>
            <td class="ps-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem;">
                        <?php echo strtoupper(substr($u->name, 0, 1)); ?>
                    </div>
                    <div>
                        <span class="fw-medium text-dark"><?php echo htmlspecialchars($u->name); ?></span>
                        <?php if ((int)$u->id === (int)$this->session->userdata('user_id')): ?>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1" style="font-size: 0.7rem;">Anda</span>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
            <td class="text-secondary"><?php echo htmlspecialchars($u->email); ?></td>
            <td class="text-secondary small">
                <?php echo $u->created_at ? date('d M Y, H:i', strtotime($u->created_at)) : '-'; ?>
            </td>
            <td class="text-center pe-4">
                <?php if ((int)$u->id !== (int)$this->session->userdata('user_id')): ?>
                    <form action="<?php echo base_url('settings/user/' . $u->id); ?>" method="POST" class="delete-user-form" data-name="<?php echo htmlspecialchars($u->name); ?>" style="margin: 0;">
                        <button type="submit" class="btn btn-outline-danger btn-sm border-0 rounded-circle" title="Hapus User">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                <?php else: ?>
                    <span class="text-muted small">—</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4" class="text-center py-4 text-muted small">Belum ada akun admin lain.</td>
        </tr>
    <?php endif; ?>
</tbody>
