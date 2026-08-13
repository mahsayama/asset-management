<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Asset Management System'; ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">
    
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 

    <script>
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            document.documentElement.classList.add('collapsed');
        }

        function toggleSidebar(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const html = document.documentElement;
            html.classList.toggle('collapsed');
            localStorage.setItem('sidebar-collapsed', html.classList.contains('collapsed'));
        }
    </script>

    <style>
        :root {
            --primary-color: #4e73df;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 70px;
            --transition-speed: 0.3s;
        }

        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; overflow-x: hidden; }
        
        .sidebar {
            min-height: 100vh; width: var(--sidebar-width); background: #ffffff;
            border-right: 1px solid #edf2f7; position: fixed; top: 0; left: 0; z-index: 1050;
            transition: width var(--transition-speed) ease;
            overflow: hidden; white-space: nowrap;
        }
        
        .main-content {
            margin-left: var(--sidebar-width); padding: 2rem;
            transition: margin-left var(--transition-speed) ease;
        }

        .inventory-sticky-header {
            position: sticky;
            top: 0;
            z-index: 1040;
            background-color: #f8f9fa;
            padding-top: 1.5rem;
            padding-bottom: 0.75rem;
            margin-top: -2rem;
            margin-left: -2rem;
            margin-right: -2rem;
            padding-left: 2rem;
            padding-right: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            transition: all 0.2s ease;
        }

        .htmx-indicator {
            opacity: 0;
            transition: opacity 200ms ease-in;
            position: fixed; top: 0; left: 0; width: 100%; height: 3px; 
            background: var(--primary-color); z-index: 9999;
        }
        .htmx-request .htmx-indicator { opacity: 1; }
        .htmx-request.htmx-indicator { opacity: 1; }

        .ts-control {
            border-radius: 0.5rem !important;
            padding: 0.7rem 1rem !important;
            background-color: #ffffff !important;
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important;
            border: 1px solid #dee2e6 !important;
        }
        .ts-wrapper.single .ts-control {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 0.75rem center !important;
            background-size: 16px 12px !important;
            padding-right: 2.25rem !important;
        }

        .fade-in {
            animation: modernFadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes modernFadeIn {
            from { opacity: 0; filter: blur(5px); transform: scale(0.99); }
            to { opacity: 1; filter: blur(0); transform: scale(1); }
        }

        html.collapsed .sidebar { width: var(--sidebar-collapsed-width); }
        html.collapsed .main-content { margin-left: var(--sidebar-collapsed-width); }
        
        html.collapsed .sidebar .nav-link span,
        html.collapsed .sidebar .sidebar-text,
        html.collapsed .sidebar .btn-text { opacity: 0; pointer-events: none; display: none; }

        html.collapsed .sidebar .nav-link,
        html.collapsed .sidebar .sidebar-brand,
        html.collapsed .sidebar .mt-auto { justify-content: center; padding-left: 0; padding-right: 0; }
        html.collapsed .sidebar .sidebar-brand i { margin-right: 0 !important; }

        .icon-collapsed { display: none; }
        .icon-expanded { display: inline-block; }
        html.collapsed .icon-expanded { display: none; }
        html.collapsed .icon-collapsed { display: inline-block; }

        .btn-toggle-sidebar {
            color: var(--primary-color); background: white; border: 1px solid #e3e6f0;
            display: flex; align-items: center; justify-content: center;
            width: 40px; height: 40px; transition: all 0.2s;
        }
        .btn-toggle-sidebar:hover { background: #f8f9fa; color: #2e59d9; transform: scale(1.05); }

        .nav-link { color: #64748b; font-weight: 500; padding: 0.75rem 1.5rem; display: flex; align-items: center; gap: 0.75rem; border-right: 3px solid transparent; transition: all 0.2s; }
        .nav-link:hover { color: var(--primary-color); background: #f8faff; }
        .nav-link.active { color: var(--primary-color); background: #f0f4ff; border-right-color: var(--primary-color); }
        
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.04); }
        .table thead th { background-color: #f8f9fa; color: #64748b; font-size: 0.8rem; text-transform: uppercase; font-weight: 600; }

        .profile-avatar {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50% !important;
            background: linear-gradient(135deg, #4e73df 0%, #6f42c1 100%);
            color: white;
            box-shadow: 0 4px 10px rgba(78, 115, 223, 0.15);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
            border: 2px solid white;
            outline: 1px solid rgba(0, 0, 0, 0.05);
        }

        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.25) !important;
            backdrop-filter: blur(6px) !important;
            -webkit-backdrop-filter: blur(6px) !important;
        }
        .modal-backdrop.show {
            opacity: 1 !important;
        }
    </style>
</head>

<body hx-boost="true">

    <div class="htmx-indicator"></div>
