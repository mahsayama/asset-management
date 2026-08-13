<div class="row g-4">
    <!-- 1. HAK AKSES ADMIN SAYA -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="profile-avatar me-3">
                        <i class="bi bi-shield-lock-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0"><?php echo ucfirst($this->session->userdata('user_name') ?: 'Admin'); ?></h6>
                        <small class="text-muted"><?php echo $this->session->userdata('user_email'); ?></small>
                    </div>
                </div>
                <hr class="my-3 opacity-10">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-medium">
                        Administrator System
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        <i class="bi bi-key me-1"></i> Ganti Password
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. SUMMARY COUNTER MASTER DATA -->
    <div class="col-md-6 col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="row g-3 w-100">
                    <div class="col-sm-4">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <div class="text-primary fs-3 fw-bold" id="counter-total-admin"><?php echo count($users); ?></div>
                            <small class="text-muted fw-medium">Total Admin</small>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <div class="text-success fs-3 fw-bold" id="counter-total-lokasi"><?php echo count($lokasis); ?></div>
                            <small class="text-muted fw-medium">Gedung / Lokasi</small>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <div class="text-purple fs-3 fw-bold" id="counter-total-kategori" style="color: #6f42c1;"><?php echo count($kategoris); ?></div>
                            <small class="text-muted fw-medium">Kategori Aset</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. MANAJEMEN AKUN ADMINISTRATOR -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold m-0 text-dark">
                    <i class="bi bi-people me-2 text-primary"></i>Manajemen Akun Administrator
                </h6>
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-person-plus me-1"></i> Tambah Admin
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Nama User</th>
                                <th>Email</th>
                                <th>Tanggal Dibuat</th>
                                <th class="text-center pe-4" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <?php $this->load->view('settings/user_table_partial'); ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. MASTER DATA MANAGEMENT (KATEGORI & LOKASI) -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold m-0 text-dark">
                    <i class="bi bi-database-gear me-2 text-primary"></i>Master Data Management
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-5">
                    <div class="col-md-6 border-end-md">
                        <label class="fw-bold text-dark mb-2">📍 Daftar Gedung / Lokasi</label>
                        <p class="small text-muted mb-3">Tambahkan lokasi baru untuk penempatan aset.</p>
                        
                        <form id="addLokasiForm" action="<?php echo base_url('settings/master'); ?>" method="POST" class="mb-4" hx-boost="false">
                            <input type="hidden" name="type" value="lokasi">
                            <div class="input-group">
                                <input type="text" name="nama" class="form-control" placeholder="Nama Gedung (contoh: Gudang B)" required autocomplete="off">
                                <button class="btn btn-primary" type="submit" id="btnSubmitLokasi">
                                    <i class="bi bi-plus-lg"></i> Tambah
                                </button>
                            </div>
                        </form>

                        <?php $this->load->view('settings/lokasi_list_partial'); ?>
                    </div>

                    <div class="col-md-6">
                        <label class="fw-bold text-dark mb-2">🏷️ Kategori Aset</label>
                        <p class="small text-muted mb-3">Tambahkan kategori untuk pengelompokan aset.</p>
                        
                        <form id="addKategoriForm" action="<?php echo base_url('settings/master'); ?>" method="POST" class="mb-4" hx-boost="false">
                            <input type="hidden" name="type" value="kategori">
                            <div class="input-group">
                                <input type="text" name="nama" class="form-control" placeholder="Nama Kategori (contoh: Drone)" required autocomplete="off">
                                <button class="btn btn-primary" type="submit" id="btnSubmitKategori">
                                    <i class="bi bi-plus-lg"></i> Tambah
                                </button>
                            </div>
                        </form>

                        <?php $this->load->view('settings/kategori_list_partial'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH ADMIN -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="addUserModalLabel">Tambah Akun Administrator</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <form id="addUserForm" action="<?php echo base_url('settings/user'); ?>" method="POST" novalidate>
        <div class="modal-body py-4">
          <div class="mb-3">
            <label class="form-label fw-medium small">Username / Nama</label>
            <input class="form-control" type="text" name="name" id="new_user_name" placeholder="misal: admin2" required autocomplete="off">
          </div>
          <div class="mb-3">
            <label class="form-label fw-medium small">Email</label>
            <input class="form-control" type="email" name="email" id="new_user_email" placeholder="admin2@asset.com" required autocomplete="off">
          </div>
          <div class="mb-3">
            <label class="form-label fw-medium small">Password (Min 6 karakter)</label>
            <div class="input-group">
                <input class="form-control" type="password" name="password" id="new_user_password" minlength="6" placeholder="Password akun baru" required>
                <button type="button" class="btn btn-outline-secondary toggle-password-btn" data-target="#new_user_password" title="Lihat Password">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-medium small">Konfirmasi Password</label>
            <div class="input-group">
                <input class="form-control" type="password" name="password_confirmation" id="new_user_password_confirmation" minlength="6" placeholder="Ulangi password akun baru" required>
                <button type="button" class="btn btn-outline-secondary toggle-password-btn" data-target="#new_user_password_confirmation" title="Lihat Password">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
            <div id="add_user_password_msg" class="text-danger small mt-1" style="display: none;"></div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
          <button type="submit" id="btnSubmitAddUser" class="btn btn-primary rounded-pill px-4">
              <i class="bi bi-save me-1"></i> Simpan User
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL GANTI PASSWORD -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="changePasswordModalLabel">Ganti Password Akun</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <form id="changePasswordForm" action="<?php echo base_url('settings/user/password'); ?>" method="POST" novalidate>
        <div class="modal-body py-4">
          <div class="mb-3">
            <label class="form-label fw-medium small">Password Lama</label>
            <div class="input-group">
                <input class="form-control" type="password" name="old_password" id="user_old_password" required>
                <button type="button" class="btn btn-outline-secondary toggle-password-btn" data-target="#user_old_password" title="Lihat Password">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-medium small">Password Baru (Min 6 Karakter)</label>
            <div class="input-group">
                <input class="form-control" type="password" name="new_password" id="user_new_password" minlength="6" required>
                <button type="button" class="btn btn-outline-secondary toggle-password-btn" data-target="#user_new_password" title="Lihat Password">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-medium small">Konfirmasi Password Baru</label>
            <div class="input-group">
                <input class="form-control" type="password" name="new_password_confirmation" id="user_new_password_confirmation" minlength="6" required>
                <button type="button" class="btn btn-outline-secondary toggle-password-btn" data-target="#user_new_password_confirmation" title="Lihat Password">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
            <div id="change_user_password_msg" class="text-danger small mt-1" style="display: none;"></div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
          <button type="submit" id="btnSubmitChangePassword" class="btn btn-primary rounded-pill px-4">
              <i class="bi bi-check-circle me-1"></i> Update Password
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addUserModalEl = document.getElementById('addUserModal');
        if (addUserModalEl) {
            addUserModalEl.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('addUserForm');
                if (form) form.reset();
                const msg = document.getElementById('add_user_password_msg');
                if (msg) msg.style.display = 'none';
                document.querySelectorAll('#addUserForm .is-invalid').forEach(el => el.classList.remove('is-invalid'));
            });
        }

        const changePasswordModalEl = document.getElementById('changePasswordModal');
        if (changePasswordModalEl) {
            changePasswordModalEl.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('changePasswordForm');
                if (form) form.reset();
                const msg = document.getElementById('change_user_password_msg');
                if (msg) msg.style.display = 'none';
                document.querySelectorAll('#changePasswordForm .is-invalid').forEach(el => el.classList.remove('is-invalid'));
            });
        }
    });

    function triggerToast(message, tags) {
        document.body.dispatchEvent(new CustomEvent('showToast', {
            detail: { message: message, tags: tags }
        }));
    }

    // 1. Submit Form Tambah Admin via Fetch
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        addUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const name = document.getElementById('new_user_name').value.trim();
            const email = document.getElementById('new_user_email').value.trim();
            const pass = document.getElementById('new_user_password').value;
            const confirmPass = document.getElementById('new_user_password_confirmation').value;
            const errorMsgEl = document.getElementById('add_user_password_msg');

            if (!name || !email) {
                errorMsgEl.innerText = 'Username dan Email wajib diisi!';
                errorMsgEl.style.display = 'block';
                return false;
            }

            if (pass !== confirmPass) {
                errorMsgEl.innerText = 'Konfirmasi password tidak cocok dengan password awal!';
                errorMsgEl.style.display = 'block';
                document.getElementById('new_user_password_confirmation').classList.add('is-invalid');
                document.getElementById('new_user_password').classList.add('is-invalid');
                triggerToast('Konfirmasi password tidak cocok!', 'danger');
                return false;
            }

            if (pass.length < 6) {
                errorMsgEl.innerText = 'Password minimal 6 karakter!';
                errorMsgEl.style.display = 'block';
                document.getElementById('new_user_password').classList.add('is-invalid');
                triggerToast('Password minimal 6 karakter!', 'danger');
                return false;
            }

            errorMsgEl.style.display = 'none';
            document.querySelectorAll('#addUserForm .is-invalid').forEach(el => el.classList.remove('is-invalid'));

            const btnSubmit = document.getElementById('btnSubmitAddUser');
            btnSubmit.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.json();
                btnSubmit.disabled = false;

                if (response.ok && data.success) {
                    const modalInstance = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
                    if (modalInstance) modalInstance.hide();

                    addUserForm.reset();

                    if (data.html) {
                        const tableBody = document.getElementById('user-table-body');
                        if (tableBody) tableBody.outerHTML = data.html;
                    }

                    triggerToast(data.message || 'Akun Administrator baru berhasil ditambahkan.', 'success');
                } else {
                    errorMsgEl.innerText = data.message || 'Terjadi kesalahan saat membuat user.';
                    errorMsgEl.style.display = 'block';
                    triggerToast(data.message || 'Gagal membuat user.', 'danger');
                }
            })
            .catch(err => {
                btnSubmit.disabled = false;
                errorMsgEl.innerText = 'Terjadi kesalahan sistem. Coba lagi.';
                errorMsgEl.style.display = 'block';
                triggerToast('Gagal membuat user.', 'danger');
            });
        });
    }

    // 2. Submit Form Tambah Gedung / Lokasi via Fetch
    const addLokasiForm = document.getElementById('addLokasiForm');
    if (addLokasiForm) {
        addLokasiForm.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const input = this.querySelector('input[name="nama"]');
            if (!input || !input.value.trim()) return;

            const btn = document.getElementById('btnSubmitLokasi');
            if (btn) btn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async res => {
                if (btn) btn.disabled = false;
                const data = await res.json();
                if (res.ok && data.success) {
                    addLokasiForm.reset();
                    if (data.html) {
                        const wrapper = document.getElementById('lokasi-list-wrapper');
                        if (wrapper) wrapper.outerHTML = data.html;
                    }
                    triggerToast(data.message, 'success');
                } else {
                    triggerToast(data.message || 'Gagal menambahkan lokasi.', 'danger');
                }
            })
            .catch(err => {
                if (btn) btn.disabled = false;
                triggerToast('Gagal menambahkan lokasi.', 'danger');
            });
        });
    }

    // 3. Submit Form Tambah Kategori Aset via Fetch
    const addKategoriForm = document.getElementById('addKategoriForm');
    if (addKategoriForm) {
        addKategoriForm.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const input = this.querySelector('input[name="nama"]');
            if (!input || !input.value.trim()) return;

            const btn = document.getElementById('btnSubmitKategori');
            if (btn) btn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async res => {
                if (btn) btn.disabled = false;
                const data = await res.json();
                if (res.ok && data.success) {
                    addKategoriForm.reset();
                    if (data.html) {
                        const wrapper = document.getElementById('kategori-list-wrapper');
                        if (wrapper) wrapper.outerHTML = data.html;
                    }
                    triggerToast(data.message, 'success');
                } else {
                    triggerToast(data.message || 'Gagal menambahkan kategori.', 'danger');
                }
            })
            .catch(err => {
                if (btn) btn.disabled = false;
                triggerToast('Gagal menambahkan kategori.', 'danger');
            });
        });
    }

    // 4. Submit Form Ganti Password via Fetch
    const changePasswordForm = document.getElementById('changePasswordForm');
    if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const oldPass = document.getElementById('user_old_password').value;
            const newPass = document.getElementById('user_new_password').value;
            const confirmNewPass = document.getElementById('user_new_password_confirmation').value;
            const errorMsgEl = document.getElementById('change_user_password_msg');

            if (!oldPass) {
                errorMsgEl.innerText = 'Password lama wajib diisi!';
                errorMsgEl.style.display = 'block';
                return false;
            }

            if (newPass !== confirmNewPass) {
                errorMsgEl.innerText = 'Konfirmasi password baru tidak cocok!';
                errorMsgEl.style.display = 'block';
                document.getElementById('user_new_password_confirmation').classList.add('is-invalid');
                document.getElementById('user_new_password').classList.add('is-invalid');
                triggerToast('Konfirmasi password baru tidak cocok!', 'danger');
                return false;
            }

            if (newPass.length < 6) {
                errorMsgEl.innerText = 'Password baru minimal 6 karakter!';
                errorMsgEl.style.display = 'block';
                document.getElementById('user_new_password').classList.add('is-invalid');
                triggerToast('Password baru minimal 6 karakter!', 'danger');
                return false;
            }

            errorMsgEl.style.display = 'none';
            document.querySelectorAll('#changePasswordForm .is-invalid').forEach(el => el.classList.remove('is-invalid'));

            const btnSubmit = document.getElementById('btnSubmitChangePassword');
            btnSubmit.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.json();
                btnSubmit.disabled = false;

                if (response.ok && data.success) {
                    const modalInstance = bootstrap.Modal.getInstance(document.getElementById('changePasswordModal'));
                    if (modalInstance) modalInstance.hide();

                    changePasswordForm.reset();
                    triggerToast(data.message || 'Password Anda berhasil diperbarui.', 'success');
                } else {
                    errorMsgEl.innerText = data.message || 'Gagal merubah password.';
                    errorMsgEl.style.display = 'block';
                    triggerToast(data.message || 'Gagal merubah password.', 'danger');
                }
            })
            .catch(err => {
                btnSubmit.disabled = false;
                errorMsgEl.innerText = 'Terjadi kesalahan sistem. Coba lagi.';
                errorMsgEl.style.display = 'block';
                triggerToast('Gagal merubah password.', 'danger');
            });
        });
    }

    // Toggle Password Visibility (Mata)
    document.querySelectorAll('.toggle-password-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetSelector = this.getAttribute('data-target');
            const targetInput = document.querySelector(targetSelector);
            const icon = this.querySelector('i');
            
            if (targetInput) {
                const type = targetInput.getAttribute('type') === 'password' ? 'text' : 'password';
                targetInput.setAttribute('type', type);
                
                if (type === 'text') {
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                } else {
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            }
        });
    });

    // Delete confirmation listeners via Fetch AJAX
    document.body.addEventListener('click', function(e) {
        const form = e.target.closest('.delete-form, .delete-user-form');
        if (form && e.target.closest('button[type="submit"]')) {
            e.preventDefault();
            e.stopPropagation();
            const itemName = form.getAttribute('data-name');
            const isUser = form.classList.contains('delete-user-form');
            
            Swal.fire({
                title: 'Hapus ' + (isUser ? 'Akun ' : '') + itemName + '?',
                text: isUser ? "Akun ini tidak akan bisa login lagi ke sistem." : "Data aset yang menggunakan ini akan menjadi kosong.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
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
                            if (isUser && data.html) {
                                const tableBody = document.getElementById('user-table-body');
                                if (tableBody) tableBody.outerHTML = data.html;
                            } else if (data.type === 'lokasi' && data.html) {
                                const wrapper = document.getElementById('lokasi-list-wrapper');
                                if (wrapper) wrapper.outerHTML = data.html;
                            } else if (data.type === 'kategori' && data.html) {
                                const wrapper = document.getElementById('kategori-list-wrapper');
                                if (wrapper) wrapper.outerHTML = data.html;
                            }
                            triggerToast(data.message || 'Berhasil dihapus.', 'success');
                        } else {
                            triggerToast(data.message || 'Gagal menghapus data.', 'danger');
                        }
                    })
                    .catch(err => {
                        triggerToast('Gagal menghapus data.', 'danger');
                    });
                }
            });
        }
    });
</script>
