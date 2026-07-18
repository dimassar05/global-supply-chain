@extends('layouts.app')

@section('page_title', 'News Intelligence')

@section('content')
<style>
    .custom-card {
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        border-radius: 16px;
    }

    /* 2. Desain Tombol Filter (Lebih Clean & Dashboard-look) */
    .filter-btn {
        border-radius: 8px; 
        padding: 8px 18px;
        font-weight: 600;
        font-size: 13px;
        letter-spacing: 0.5px;
        transition: all 0.2s ease;
        margin-right: 10px;
        margin-bottom: 10px;
        border: 1px solid #cbd5e1;
        cursor: pointer;
    }
    /* Saat Tombol Aktif (Warna Dark Navy senada dengan Sidebar) */
    .filter-btn.active-filter {
        background-color: #1e293b; /* Dark Slate / Navy */
        color: #ffffff;
        border-color: #1e293b;
        box-shadow: 0 4px 10px rgba(30, 41, 59, 0.2); /* Shadow yang lebih kalem */
    }
    
    /* Saat Tombol Tidak Aktif (Lebih menyatu dengan background) */
    .filter-btn:not(.active-filter) {
        background-color: #ffffff;
        color: #64748b; /* Teks abu-abu kebiruan */
        border-color: #cbd5e1;
    }
    
    /* Efek Hover untuk tombol tidak aktif */
    .filter-btn:not(.active-filter):hover {
        background-color: #f1f5f9;
        color: #0f172a;
        border-color: #94a3b8;
    }
    
    /* 3. Desain Card Berita (Konsisten dengan Custom Card) */
    .news-card {
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        border-radius: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden; /* Agar gambar tidak keluar dari radius */
    }
    .news-card:hover {
        transform: translateY(-4px);
        border-color: #94a3b8;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
    }
    .news-image-wrapper {
        height: 180px;
        overflow: hidden;
        border-bottom: 1px solid #f1f5f9;
    }
    .news-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .news-card:hover .news-image {
        transform: scale(1.05);
    }
    
    /* Batasi teks agar rapi (max 3 baris) */
    .clamp-text {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Label Sumber Berita */
    .news-source-badge {
        background-color: #f1f5f9;
        color: #0d6efd;
        font-weight: 700;
        font-size: 11px;
        padding: 5px 10px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

<!-- HEADER & TOMBOL FILTER (Desain Konsisten) -->
<div class="custom-card mb-4 p-4">
    <label class="form-label text-muted fw-bold text-uppercase mb-3" style="font-size: 13px; letter-spacing: 1px;">
        <i class="fas fa-filter me-2 text-primary"></i> Filter Topik Berita
    </label>
    <div class="d-flex flex-wrap" id="filter-buttons">
        <button class="filter-btn active-filter" onclick="fetchNews('logistics OR trade OR shipping OR economy OR geopolitics', this)">All Top News</button>
        <button class="filter-btn" onclick="fetchNews('logistics OR supply chain', this)"><i class="fas fa-truck-fast"></i> Logistics</button>
        <button class="filter-btn" onclick="fetchNews('global trade OR export import', this)"><i class="fas fa-globe"></i> Trade</button>
        <button class="filter-btn" onclick="fetchNews('shipping OR port OR cargo', this)"><i class="fas fa-ship"></i> Shipping</button>
        <button class="filter-btn" onclick="fetchNews('global economy OR inflation', this)"><i class="fas fa-chart-line"></i> Economy</button>
        <button class="filter-btn" onclick="fetchNews('geopolitics OR war OR conflict', this)"><i class="fas fa-shield-halved"></i> Geopolitics</button>
    </div>
</div>

<!-- KONTENER GRID BERITA -->
<div class="row g-4" id="news-container">
    <!-- Konten Berita akan dimuat di sini oleh JavaScript -->
</div>

@endsection

@push('scripts')
<script>
    // API KEY GNEWS
    const apiKey = 'eb68ad8b81cd4badd54a7b8406a0a7b2';

    async function fetchNews(queryKeyword, btnElement = null) {
        const container = document.getElementById('news-container');
        
        // Ganti Warna Tombol Filter
        if (btnElement) {
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active-filter');
            });
            btnElement.classList.add('active-filter');
        }

        // Tampilkan Loading
        container.innerHTML = `
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                <h5 class="mt-3 text-muted fw-bold">Memindai Berita Global...</h5>
            </div>
        `;

        try {
            const query = encodeURIComponent(queryKeyword);
            const url = `https://gnews.io/api/v4/search?q=${query}&lang=en&max=9&apikey=${apiKey}`;
            
            const response = await fetch(url);
            const data = await response.json();

            container.innerHTML = '';

            if (data.articles && data.articles.length > 0) {
                data.articles.forEach(article => {
                    const fallbackImg = 'https://images.unsplash.com/photo-1586528116311-ad8c738759be?q=80&w=800&auto=format&fit=crop';
                    const imgUrl = article.image || fallbackImg;
                    
                    const dateObj = new Date(article.publishedAt);
                    const formattedDate = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

                    const cardHTML = `
                        <div class="col-md-6 col-lg-4">
                            <div class="news-card">
                                <div class="news-image-wrapper">
                                    <img src="${imgUrl}" class="news-image" alt="News Image">
                                </div>
                                <div class="card-body d-flex flex-column p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="news-source-badge">
                                            <i class="fas fa-newspaper me-1"></i> ${article.source.name}
                                        </span>
                                        <small class="text-muted fw-bold" style="font-size:12px;"><i class="far fa-clock me-1"></i> ${formattedDate}</small>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2" style="font-size: 16px; line-height: 1.5;">
                                        ${article.title}
                                    </h5>
                                    <p class="text-muted clamp-text mb-4" style="font-size: 13px; line-height: 1.6;">
                                        ${article.description}
                                    </p>
                                    <div class="mt-auto">
                                        <a href="${article.url}" target="_blank" class="btn btn-outline-primary w-100 fw-bold rounded-3" style="font-size: 14px; letter-spacing: 0.5px;">
                                            Baca Selengkapnya <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.innerHTML += cardHTML;
                });
            } else {
                container.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-folder-open text-muted mb-3" style="font-size: 48px;"></i>
                        <h5 class="text-muted">Tidak ada berita ditemukan atau Limit API habis.</h5>
                    </div>
                `;
            }
        } catch (error) {
            container.innerHTML = `
                <div class="col-12 text-center py-5 text-danger">
                    <i class="fas fa-triangle-exclamation mb-3" style="font-size: 48px;"></i>
                    <h5 class="fw-bold">Gagal terhubung ke GNews API</h5>
                    <p>Pastikan koneksi internet aktif atau cek batas limit API Key.</p>
                </div>
            `;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        fetchNews('logistics OR trade OR shipping OR economy OR geopolitics');
    });
</script>
@endpush