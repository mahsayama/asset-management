    <div id="permanent-sidebar" class="sidebar d-flex flex-column">
        <div class="p-4 border-bottom d-flex align-items-center sidebar-brand" style="height: 80px;">
             <a href="<?php echo base_url('dashboard'); ?>" class="text-decoration-none fw-bold text-primary m-0 d-flex align-items-center" style="letter-spacing: -0.5px; line-height: 1.2;">
                <i class="bi bi-box-seam-fill me-2 fs-4"></i>
                <div class="sidebar-text">
                    ASSET FLOW<br>
                    <span class="small text-muted fw-normal" style="font-size: 0.7rem;">SYSTEM V1.0 (CI3)</span>
                </div>
             </a>
        </div>

        <?php $segment = $this->uri->segment(1); ?>
        <nav class="nav flex-column mt-3" id="sidebar-nav-links">
            <a href="<?php echo base_url('dashboard'); ?>" class="nav-link <?php echo ($segment == 'dashboard' || $segment == '') ? 'active' : ''; ?>">
                <i class="bi bi-grid-1x2 fs-5"></i> <span>Dashboard</span>
            </a>
            <a href="<?php echo base_url('inventory'); ?>" class="nav-link <?php echo ($segment == 'inventory' || $segment == 'tambah' || $segment == 'edit' || $segment == 'asset') ? 'active' : ''; ?>">
                <i class="bi bi-box fs-5"></i> <span>Inventory</span>
            </a>
            <a href="<?php echo base_url('reports'); ?>" class="nav-link <?php echo ($segment == 'reports') ? 'active' : ''; ?>">
                 <i class="bi bi-graph-up fs-5"></i> <span>Reports</span>
            </a>
            <a href="<?php echo base_url('settings'); ?>" class="nav-link <?php echo ($segment == 'settings') ? 'active' : ''; ?>">
                <i class="bi bi-gear fs-5"></i> <span>Settings</span>
            </a>
        </nav>

        <div class="mt-auto p-4 border-top">
             <a href="<?php echo base_url('logout'); ?>" class="btn btn-outline-danger w-100 btn-sm fw-medium d-flex align-items-center justify-content-center" hx-boost="false">
                <i class="bi bi-box-arrow-right"></i> 
                <span class="ms-2 btn-text">Logout</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <?php if ($segment !== 'inventory'): ?>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 mb-md-5">
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
                    <h4 class="fw-bold text-dark m-0"><?php echo isset($title) ? $title : 'Dashboard'; ?></h4>
                    <p class="text-muted small m-0"><?php echo isset($subtitle) ? $subtitle : 'Overview aset IT perusahaan'; ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="fade-in">
