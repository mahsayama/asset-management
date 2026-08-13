    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="toast align-items-center text-bg-success border-0 shadow-lg fade show mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <span class="fw-medium"><?php echo $this->session->flashdata('success'); ?></span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="toast align-items-center text-bg-danger border-0 shadow-lg fade show mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-2">
                        <i class="bi bi-x-circle-fill fs-5"></i>
                        <span class="fw-medium"><?php echo $this->session->flashdata('error'); ?></span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function updateActiveSidebarLink() {
            const currentPath = window.location.pathname.toLowerCase();
            const links = document.querySelectorAll('#sidebar-nav-links .nav-link');
            
            links.forEach(link => {
                const href = link.getAttribute('href');
                if (!href) return;
                const linkPath = new URL(href, window.location.origin).pathname.toLowerCase();
                
                link.classList.remove('active');
                
                if (linkPath.includes('/inventory') && (currentPath.includes('/inventory') || currentPath.includes('/tambah') || currentPath.includes('/edit') || currentPath.includes('/asset'))) {
                    link.classList.add('active');
                } else if (linkPath.includes('/reports') && currentPath.includes('/reports')) {
                    link.classList.add('active');
                } else if (linkPath.includes('/settings') && currentPath.includes('/settings')) {
                    link.classList.add('active');
                } else if ((linkPath.includes('/dashboard') || linkPath.endsWith('/') || linkPath === '') && (currentPath.includes('/dashboard') || currentPath.endsWith('/') || currentPath === '')) {
                    link.classList.add('active');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateActiveSidebarLink();

            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            toastElList.map(function (toastEl) {
                return new bootstrap.Toast(toastEl, { delay: 5000 }).show(); 
            });
        });

        document.body.addEventListener('htmx:afterSettle', updateActiveSidebarLink);
        document.body.addEventListener('htmx:pushedIntoHistory', updateActiveSidebarLink);

        // Global Event Delegation for Sidebar Toggle (Works on Dashboard, Inventory, Reports, Settings & all HTMX swaps)
        document.addEventListener('click', function(e) {
            const toggleBtn = e.target.closest('#sidebarToggle, .btn-toggle-sidebar');
            if (toggleBtn) {
                e.preventDefault();
                const html = document.documentElement;
                html.classList.toggle('collapsed');
                localStorage.setItem('sidebar-collapsed', html.classList.contains('collapsed'));
            }
        });

        if (!window.hasToastListener) {
            document.body.addEventListener('showToast', function(evt) {
                const data = evt.detail; 
                const message = data.message;
                const type = data.tags; 
                
                let icon = 'bi-info-circle-fill';
                if (type === 'success') icon = 'bi-check-circle-fill';
                if (type === 'danger') icon = 'bi-x-circle-fill';
                if (type === 'warning') icon = 'bi-exclamation-triangle-fill';

                const toastHtml = `
                    <div class="toast align-items-center text-bg-${type} border-0 shadow-lg fade show mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body d-flex align-items-center gap-2">
                                <i class="bi ${icon} fs-5"></i>
                                <span class="fw-medium">${message}</span>
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                `;

                const container = document.querySelector('.toast-container');
                if (container) {
                    container.insertAdjacentHTML('beforeend', toastHtml);
                    const newToastEl = container.lastElementChild;
                    const toastInstance = new bootstrap.Toast(newToastEl, { delay: 5000 });
                    toastInstance.show();
                    
                    newToastEl.addEventListener('hidden.bs.toast', function () {
                        newToastEl.remove();
                    });
                }
            });
            window.hasToastListener = true;
        }
    </script>
</body>
</html>
