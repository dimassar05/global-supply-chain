@extends('layouts.admin')

@section('page_title', 'Overview Dashboard')

@section('content')
<style>
    .custom-card {
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        border-radius: 16px;
    }
</style>

<div class="container-fluid px-0">
    
    <!-- Welcome Banner -->
    <div class="card border-0 rounded-4 mb-4 text-white overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);">
        <div class="card-body p-4 p-md-5 position-relative">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-info text-dark px-3 py-2 rounded-pill fw-bold mb-3">
                        <i class="fas fa-shield-alt me-1"></i> CONTROL PANEL - SYSTEM OVERVIEW
                    </span>
                    <h2 class="fw-bold mb-2">Selamat Datang di Portal Admin</h2>
                    <p class="text-white-50 mb-0">Kelola seluruh dataset, pengguna terdaftar, dan artikel analisis publikasi dalam satu pusat kontrol.</p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0 d-none d-lg-block">
                    <i class="fas fa-chart-line fa-6x opacity-25" style="color: #03fbff;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Executive Stat Cards (DIREVISI) -->
    <div class="row g-4 mb-4">
        
        <!-- 1. Card Total Pengguna (Diubah menjadi col-xl-4) -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="glass-card p-4 h-100 border-start border-4 border-primary">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted fw-semibold small">Total Pengguna</span>
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-dark">{{ $totalUsers }}</h3>
                <span class="text-muted small"><i class="fas fa-user-check text-success me-1"></i> {{ $totalAdmins }} Admin, {{ $totalRegularUsers }} User</span>
            </div>
        </div>

        <!-- 2. Card Dataset Pelabuhan (Diubah menjadi col-xl-4 & Teks 250) -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="glass-card p-4 h-100 border-start border-4 border-info">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted fw-semibold small">Dataset Pelabuhan</span>
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info">
                        <i class="fas fa-ship fa-lg"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-dark">{{ $totalPorts }}</h3>
                <span class="text-muted small"><i class="fas fa-globe text-info me-1"></i> Tersebar di 250 Negara</span>
            </div>
        </div>

        <!-- 3. Card Artikel Analisis (Diubah menjadi col-xl-4) -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="glass-card p-4 h-100 border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted fw-semibold small">Artikel Analisis</span>
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success">
                        <i class="fas fa-newspaper fa-lg"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-dark">{{ $totalArticles }}</h3>
                <span class="text-muted small"><i class="fas fa-file-signature text-success me-1"></i> Publikasi Intelijen</span>
            </div>
        </div>

        <!-- CARD DATABASE NEGARA TELAH DIHAPUS -->

    </div>

    <!-- Quick Navigation Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="custom-card p-4 text-center h-100 d-flex flex-column align-items-center justify-content-center">
                <div class="rounded-circle bg-primary bg-opacity-10 p-4 text-primary mb-3">
                    <i class="fas fa-user-gear fa-2x"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Manajemen User</h5>
                <p class="text-muted small mb-3">Tambah pengguna baru, ubah role (Admin/User), atau hapus akun pengguna.</p>
                <a href="{{ route('admin.users.index') }}" class="btn btn-primary rounded-pill px-4 fw-semibold mt-auto">
                    <i class="fas fa-arrow-right me-1"></i> Kelola User
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="custom-card p-4 text-center h-100 d-flex flex-column align-items-center justify-content-center">
                <div class="rounded-circle bg-info bg-opacity-10 p-4 text-info mb-3">
                    <i class="fas fa-anchor fa-2x"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Dataset Pelabuhan</h5>
                <p class="text-muted small mb-3">Tambah lokasi pelabuhan global baru, atur koordinat peta, atau perbarui data.</p>
                <a href="{{ route('admin.ports.index') }}" class="btn btn-info text-white rounded-pill px-4 fw-semibold mt-auto">
                    <i class="fas fa-arrow-right me-1"></i> Kelola Pelabuhan
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="custom-card p-4 text-center h-100 d-flex flex-column align-items-center justify-content-center">
                <div class="rounded-circle bg-success bg-opacity-10 p-4 text-success mb-3">
                    <i class="fas fa-newspaper fa-2x"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Artikel Analisis</h5>
                <p class="text-muted small mb-3">Tulis artikel berita analisis pasar baru, tag sentimen risiko, atau kelola publikasi.</p>
                <a href="{{ route('admin.articles.index') }}" class="btn btn-success rounded-pill px-4 fw-semibold mt-auto">
                    <i class="fas fa-arrow-right me-1"></i> Kelola Artikel
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Tables Preview -->
    <div class="row g-4">
        <!-- Pengguna Terbaru -->
        <div class="col-lg-6">
            <div class="custom-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-users me-2 text-primary"></i> Pengguna Terdaftar Terbaru</h6>
                    <a href="{{ route('admin.users.index') }}" class="small fw-bold text-primary text-decoration-none">Lihat Semua <i class="fas fa-chevron-right"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Pengguna</th>
                                <th>Email</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentUsers as $u)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&background={{ $u->isAdmin() ? '4f46e5' : '64748b' }}&color=fff" 
                                             alt="{{ $u->name }}" class="rounded-circle me-2" width="28" height="28">
                                        <span class="fw-bold text-dark small">{{ $u->name }}</span>
                                    </div>
                                </td>
                                <td class="text-muted small">{{ $u->email }}</td>
                                <td>
                                    <span class="badge {{ $u->isAdmin() ? 'bg-primary' : 'bg-secondary' }} rounded-pill" style="font-size: 9px;">
                                        {{ strtoupper($u->role) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Artikel Terbaru -->
        <div class="col-lg-6">
            <div class="custom-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-file-lines me-2 text-success"></i> Artikel Analisis Terbaru</h6>
                    <a href="{{ route('admin.articles.index') }}" class="small fw-bold text-success text-decoration-none">Lihat Semua <i class="fas fa-chevron-right"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Judul Artikel</th>
                                <th>Kategori</th>
                                <th>Sentimen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($recentArticles->isEmpty())
                                <tr><td colspan="3" class="text-center py-4 text-muted small">Belum ada artikel dipublikasikan.</td></tr>
                            @else
                                @foreach($recentArticles as $a)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark small text-truncate" style="max-width: 200px;">{{ $a->title }}</div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border rounded-pill" style="font-size: 9px;">{{ $a->category }}</span></td>
                                    <td>
                                        @if($a->sentiment === 'positive')
                                            <span class="badge bg-success text-white rounded-pill" style="font-size: 9px;">Positif</span>
                                        @elseif($a->sentiment === 'negative')
                                            <span class="badge bg-danger text-white rounded-pill" style="font-size: 9px;">Negatif</span>
                                        @else
                                            <span class="badge bg-secondary text-white rounded-pill" style="font-size: 9px;">Netral</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection