<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Global Supply Chain</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-body: #f8fafc;
            --sidebar-width: 280px;
            --primary-color: #4f46e5; 
            --text-main: #0f172a;
            --text-muted: #64748b;
        }

        body { 
            background-color: var(--bg-body); 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            overflow-x: hidden; 
        }
        
        /* --- SIDEBAR (KHUSUS DARK MODE) --- */
        .sidebar { 
            width: var(--sidebar-width);
            height: 100vh; 
            background-color: #0f172a; 
            border-right: none;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }
        
        /* PERUBAHAN BRAND SIDEBAR */
        .sidebar-brand {
            padding: 30px 25px;
            font-size: 20px;
            font-weight: 800;
            color: #03fbff;
            display: flex;
            align-items: center;
            letter-spacing: 0.5px;
        }
        .sidebar-brand i { 
            color: #03fbff; 
            font-size: 24px; 
            margin-right: 12px;
        }
        
        .nav-menu { 
            padding: 0 15px 20px 15px; 
            flex-grow: 1; 
            overflow-y: auto; 
            overflow-x: hidden;
            margin-top: 10px;
        }
        .nav-menu::-webkit-scrollbar { width: 5px; }
        .nav-menu::-webkit-scrollbar-track { background: transparent; }
        .nav-menu::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        
        .sidebar a { 
            color: #ffffff; 
            text-decoration: none; 
            padding: 12px 15px; 
            margin-bottom: 8px; /* Jarak antar menu diperlebar sedikit */
            display: flex; 
            align-items: center;
            border-radius: 10px;
            transition: all 0.2s ease-in-out;
            font-size: 14.5px;
            font-weight: 600;
        }
        .sidebar a i { width: 32px; font-size: 18px; transition: color 0.2s; }
        
        /* Active & Hover States untuk Sidebar Gelap */
        .sidebar a:hover { 
            background-color: #1e293b;
            color: #ffffff;
        }
        .sidebar a.active { 
            background-color: rgba(79, 70, 229, 0.15);
            color: #03fbff; 
        }
        .sidebar a.active i { color: #03fbff; }

        /* --- MAIN WRAPPER & TOPBAR --- */
        .main-wrapper { 
            margin-left: var(--sidebar-width); 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .top-navbar {
            background-color: #ffffff;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .main-content { padding: 40px; flex-grow: 1; }
        
        /* --- GLOBAL UI ELEMENTS --- */
        .glass-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.04);
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <!-- BRAND SIDEBAR YANG SUDAH DIUBAH -->
        <div class="sidebar-brand">
            <i class="fas fa-shield-halved"></i> Admin Panel
        </div>
        
        <div class="nav-menu mt-4">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users-gear"></i> Manajemen User
            </a>
            <a href="{{ route('admin.ports.index') }}" class="{{ request()->routeIs('admin.ports.*') ? 'active' : '' }}">
                <i class="fas fa-anchor"></i> Dataset Pelabuhan
            </a>
            <a href="{{ route('admin.articles.index') }}" class="{{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
                <i class="fas fa-newspaper"></i> Artikel Analisis
            </a>
        </div>
    </div>

    <div class="main-wrapper">
        <div class="top-navbar">
            <div>
                <h5 class="mb-0 fw-bold text-dark" id="page-title">@yield('page_title', 'Dashboard')</h5>
            </div>
            <div class="d-flex align-items-center">
                @auth
                    <div class="dropdown">
                        <div class="d-flex align-items-center bg-white rounded-pill p-1 pe-3 border shadow-sm cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer; transition: all 0.2s;">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=4f46e5&color=fff" 
                                 alt="{{ auth()->user()->name }}" class="rounded-circle me-2" width="34" height="34">
                            <div class="d-flex flex-column me-2">
                                <span class="fw-bold fs-6 text-dark lh-1 mb-1">{{ auth()->user()->name }}</span>
                                <span class="text-muted fw-semibold" style="font-size: 11px;">Administrator</span>
                            </div>
                            <i class="fas fa-chevron-down text-muted ms-1" style="font-size: 11px;"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end shadow border rounded-4 p-2 mt-2" style="min-width: 220px;">
                            <li class="px-3 py-2 border-bottom mb-1">
                                <div class="fw-bold text-dark fs-6">{{ auth()->user()->name }}</div>
                                <div class="text-muted small mb-1">{{ auth()->user()->email }}</div>
                                <span class="badge bg-primary rounded-pill" style="font-size: 10px; letter-spacing: 0.5px;">
                                    <i class="fas fa-user-shield me-1"></i> ADMIN
                                </span>
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger rounded-3 fw-semibold">
                                        <i class="fas fa-right-from-bracket me-2"></i> Keluar (Logout)
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                        <i class="fas fa-right-to-bracket me-1"></i> Login
                    </a>
                @endauth
            </div>
        </div>

        <div class="main-content">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>