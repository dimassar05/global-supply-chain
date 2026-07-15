<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Supply Chain</title>
    
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
        
        /* --- SIDEBAR --- */
        .sidebar { 
            width: var(--sidebar-width);
            height: 100vh; 
            background-color: #ffffff;
            border-right: 1px solid #e2e8f0;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }
        .sidebar-brand {
            padding: 30px 25px;
            font-size: 19px;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
        }
        .sidebar-brand i { color: var(--primary-color); font-size: 24px; }
        
        .nav-menu { 
            padding: 0 15px 20px 15px; 
            flex-grow: 1; 
            overflow-y: auto; 
            overflow-x: hidden;
        }
        .nav-menu::-webkit-scrollbar { width: 5px; }
        .nav-menu::-webkit-scrollbar-track { background: transparent; }
        .nav-menu::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        
        .sidebar a { 
            color: var(--text-muted); 
            text-decoration: none; 
            padding: 12px 15px; 
            margin-bottom: 4px;
            display: flex; 
            align-items: center;
            border-radius: 10px;
            transition: all 0.2s ease-in-out;
            font-size: 14.5px;
            font-weight: 600;
        }
        .sidebar a i { width: 32px; font-size: 18px; transition: color 0.2s; }
        
        /* Active & Hover States */
        .sidebar a:hover { background-color: #f1f5f9; color: var(--text-main); }
        .sidebar a.active { 
            background-color: #eef2ff; 
            color: var(--primary-color); 
        }
        .sidebar a.active i { color: var(--primary-color); }

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
        <div class="sidebar-brand">
            <i class="fas fa-globe-americas me-2"></i> Global <span style="color: var(--primary-color); margin-left: 5px;">Supply Chain</span>
        </div>
        
        <div class="nav-menu mt-3">
            <a href="/countries" class="{{ request()->is('countries') ? 'active' : '' }}"><i class="fas fa-flag"></i> Countries</a>
            <a href="/risk-score" class="{{ request()->is('risk-score') ? 'active' : '' }}"><i class="fas fa-shield-halved"></i> Risk Score</a>
            <a href="/weather" class="{{ request()->is('weather') ? 'active' : '' }}"><i class="fas fa-cloud-sun-rain"></i> Weather Monitoring</a>
            <a href="/currency" class="{{ request()->is('currency') ? 'active' : '' }}"><i class="fas fa-money-bill-trend-up"></i> Currency</a>
            <a href="/news" class="{{ request()->is('news') ? 'active' : '' }}"><i class="fas fa-newspaper"></i> News</a>
            <a href="/port" class="{{ request()->is('port') ? 'active' : '' }}"><i class="fas fa-anchor"></i> Port</a>
            <a href="/visualization" class="{{ request()->is('visualization') ? 'active' : '' }}"><i class="fas fa-chart-line"></i> Data Visualization</a>
            <a href="/comparison" class="{{ request()->is('comparison') ? 'active' : '' }}"><i class="fas fa-code-compare"></i> Country Comparison</a>
            <a href="/watchlist" class="{{ request()->is('watchlist') ? 'active' : '' }}"><i class="fas fa-bookmark"></i> Watchlist</a>
        </div>
    </div>

    <div class="main-wrapper">
        <div class="top-navbar">
            <div>
                <h5 class="mb-0 fw-bold text-dark" id="page-title">@yield('page_title', 'Dashboard')</h5>
            </div>
            <div class="d-flex align-items-center">
                <button class="btn btn-light rounded-circle me-3 p-2 text-muted" style="width: 40px; height: 40px;">
                    <i class="fas fa-bell"></i>
                </button>
                <div class="d-flex align-items-center bg-light rounded-pill p-1 pe-3 border">
                    <img src="https://ui-avatars.com/api/?name=Manager&background=4f46e5&color=fff" alt="User" class="rounded-circle me-2" width="32" height="32">
                    <span class="fw-bold fs-6 text-dark">Logistics Mgr</span>
                </div>
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