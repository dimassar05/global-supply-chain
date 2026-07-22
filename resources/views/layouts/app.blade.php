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
        
        /* --- SIDEBAR (KHUSUS DARK MODE) --- */
        .sidebar { 
            width: var(--sidebar-width);
            height: 100vh; 
            background-color: #0f172a; /* Warna gelap modern (Slate 900) */
            border-right: none;
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
            color: #03fbff;
            display: flex;
            align-items: center;
            text-decoration: none; 
        }
        .sidebar-brand:hover {
            color: #03fbff;
        }
        .sidebar-brand i { color: #03fbff; font-size: 24px; }
        
        .nav-menu { 
            padding: 0 15px 20px 15px; 
            flex-grow: 1; 
            overflow-y: auto; 
            overflow-x: hidden;
        }
        .nav-menu::-webkit-scrollbar { width: 5px; }
        .nav-menu::-webkit-scrollbar-track { background: transparent; }
        .nav-menu::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        
        .sidebar a:not(.sidebar-brand) { 
            color: #ffffff; 
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
        .sidebar a:not(.sidebar-brand) i { width: 32px; font-size: 18px; transition: color 0.2s; }
        
        /* Active & Hover States untuk Sidebar Gelap */
        .sidebar a:not(.sidebar-brand):hover { 
            background-color: #1e293b;
            color: #ffffff;
        }
        .sidebar a.active { 
            background-color: rgba(79, 70, 229, 0.15);
            color: #03fbff !important; 
        }
        .sidebar a.active i { color: #03fbff; }

        /* --- MAIN WRAPPER & TOPBAR (LIGHT MODE) --- */
        .main-wrapper { 
            margin-left: var(--sidebar-width); 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-bottom: 40px; /* Ruang untuk Ticker di bawah */
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

        /* --- NEWS TICKER (BOTTOM BAR) --- */
        .news-ticker-bar {
            position: fixed;
            bottom: 0;
            left: var(--sidebar-width);
            right: 0;
            height: 40px;
            background-color: #0f172a;
            color: #ffffff;
            display: flex;
            align-items: center;
            z-index: 1000;
            border-top: 1px solid #ffffff;
        }
        .ticker-label {
            background-color: #0f172a;
            padding: 0 25px;
            height: 100%;
            display: flex;
            align-items: center;
            font-weight: 800;
            font-size: 11.5px;
            letter-spacing: 1px;
            color: #03fbff;
            z-index: 2;
            border-right: 2px solid #f0f1f1;
            text-transform: uppercase;
        }
        .ticker-wrap {
            flex: 1;
            overflow: hidden;
            height: 100%;
            position: relative;
            display: flex;
            align-items: center;
        }
        .ticker-move {
            display: flex;
            white-space: nowrap;
            animation: ticker-scroll 30s linear infinite;
            padding-left: 100%; 
        }
        .ticker-move:hover {
            animation-play-state: paused; 
        }
        .ticker-item {
            margin-right: 70px;
            font-size: 13.5px;
            font-weight: 500;
            cursor: pointer;
            transition: color 0.2s;
            display: flex;
            align-items: center;
        }
        .ticker-item:hover {
            color: #03fbff;
        }
        @keyframes ticker-scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="{{ url('/countries') }}" class="sidebar-brand">
            <i class="fas fa-globe-americas me-2"></i> Global <span style="color: #03fbff; margin-left: 5px;">Supply Chain</span>
        </a>
        
        <div class="nav-menu mt-3">
            <a href="{{ url('/countries') }}" class="{{ request()->is('countries') ? 'active' : '' }}"><i class="fas fa-flag"></i> Countries</a>
            <a href="{{ url('/risk-score') }}" class="{{ request()->is('risk-score') ? 'active' : '' }}"><i class="fas fa-shield-halved"></i> Risk Score</a>
            <a href="{{ url('/weather') }}" class="{{ request()->is('weather') ? 'active' : '' }}"><i class="fas fa-cloud-sun-rain"></i> Weather Monitoring</a>
            <a href="{{ url('/currency') }}" class="{{ request()->is('currency') ? 'active' : '' }}"><i class="fas fa-money-bill-trend-up"></i> Currency</a>
            <a href="{{ url('/news') }}" class="{{ request()->is('news') ? 'active' : '' }}"><i class="fas fa-newspaper"></i> News</a>
            <a href="{{ url('/port') }}" class="{{ request()->is('port') ? 'active' : '' }}"><i class="fas fa-anchor"></i> Port</a>
            <a href="{{ url('/comparison') }}" class="{{ request()->is('comparison') ? 'active' : '' }}"><i class="fas fa-code-compare"></i> Countries Comparison</a>
            <a href="{{ url('/watchlist') }}" class="{{ request()->is('watchlist') ? 'active' : '' }}"><i class="fas fa-bookmark"></i> Watchlist</a>

            @auth
                @if(auth()->user()->isAdmin())
                    <div class="px-3 pt-3 pb-1 text-uppercase fw-bold" style="font-size: 10.5px; color: #03fbff; letter-spacing: 1px;">
                        <i class="fas fa-lock me-1"></i> Admin Menu
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin*') ? 'active' : '' }}" style="background: rgba(3, 251, 255, 0.08); border: 1px dashed rgba(3, 251, 255, 0.3);">
                        <i class="fas fa-user-shield text-info"></i> Admin Panel
                    </a>
                @endif
            @endauth
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
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background={{ auth()->user()->isAdmin() ? '4f46e5' : '0284c7' }}&color=fff" 
                                 alt="{{ auth()->user()->name }}" class="rounded-circle me-2" width="34" height="34">
                            <div class="d-flex flex-column me-2">
                                <span class="fw-bold fs-6 text-dark lh-1 mb-1">{{ auth()->user()->name }}</span>
                                <span class="text-muted fw-semibold" style="font-size: 11px;">{{ ucfirst(auth()->user()->role) }}</span>
                            </div>
                            <i class="fas fa-chevron-down text-muted ms-1" style="font-size: 11px;"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end shadow border rounded-4 p-2 mt-2" style="min-width: 220px;">
                            <li class="px-3 py-2 border-bottom mb-1">
                                <div class="fw-bold text-dark fs-6">{{ auth()->user()->name }}</div>
                                <div class="text-muted small mb-1">{{ auth()->user()->email }}</div>
                                <span class="badge {{ auth()->user()->isAdmin() ? 'bg-primary' : 'bg-secondary' }} rounded-pill" style="font-size: 10px; letter-spacing: 0.5px;">
                                    <i class="fas {{ auth()->user()->isAdmin() ? 'fa-user-shield' : 'fa-user' }} me-1"></i> {{ strtoupper(auth()->user()->role) }}
                                </span>
                            </li>
                            @if(auth()->user()->isAdmin())
                                <li>
                                    <a class="dropdown-item py-2 rounded-3 fw-semibold text-dark" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-user-shield text-primary me-2"></i> Admin Panel
                                    </a>
                                </li>
                            @endif
                            <li><hr class="dropdown-divider my-1"></li>
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

    <!-- NEWS TICKER BAR (MUNCUL DI SEMUA HALAMAN) -->
    <div class="news-ticker-bar">
        <div class="ticker-label">
            <i class="fas fa-lightbulb text-warning me-2"></i>Article
        </div>
        <div class="ticker-wrap">
            <div class="ticker-move">
                @php
                    // Memanggil data artikel langsung dari database
                    $tickerArticles = \Illuminate\Support\Facades\DB::table('articles')
                                        ->orderBy('created_at', 'desc')
                                        ->limit(5)
                                        ->get();
                @endphp

                @forelse($tickerArticles as $article)
                    <div class="ticker-item" onclick="openArticleModal('{{ htmlspecialchars(addslashes($article->title)) }}', '{{ htmlspecialchars(addslashes($article->content)) }}', '{{ $article->created_at ? \Carbon\Carbon::parse($article->created_at)->format('d M Y') : '-' }}')">
                        <i class="fas fa-caret-right text-muted me-2"></i>
                        {{ $article->title }}
                    </div>
                @empty
                    <div class="ticker-item text-muted">
                        <i class="fas fa-info-circle me-2"></i> Belum ada artikel atau analisis terbaru dari Admin.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal Untuk Membaca Artikel dari Ticker -->
    <div class="modal fade" id="readArticleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill mb-2 px-3 py-2">
                        <i class="fas fa-user-shield me-1"></i> Analisis Internal
                    </span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-2">
                    <h4 class="fw-bold text-dark mb-2" id="modalTickerTitle">Memuat Judul...</h4>
                    <p class="text-muted small mb-4" id="modalTickerDate"><i class="far fa-calendar-alt me-1"></i> Tanggal</p>
                    
                    <div class="p-3 bg-light rounded-3 text-dark" style="line-height: 1.7; font-size: 15px;" id="modalTickerContent">
                        Memuat konten...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Fungsi untuk menangkap klik dari Ticker dan membuka Modal
        function openArticleModal(title, content, date) {
            document.getElementById('modalTickerTitle').innerText = title;
            document.getElementById('modalTickerContent').innerText = content;
            document.getElementById('modalTickerDate').innerHTML = '<i class="far fa-calendar-alt me-1"></i> Dipublikasikan: ' + date;
            
            var articleModal = new bootstrap.Modal(document.getElementById('readArticleModal'));
            articleModal.show();
        }
    </script>
    
    @stack('scripts')
</body>
</html>