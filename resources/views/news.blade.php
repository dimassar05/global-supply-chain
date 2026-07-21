@extends('layouts.app')

@section('page_title', 'News Intelligence')

@section('content')
<style>
    /* 1. HEADER CONSISTENCY (Sama persis dengan menu Watchlist/Countries) */
    .header-card {
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 25px;
    }

    /* 2. Desain Card Berita */
    .news-card {
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        border-radius: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden; 
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

    /* Label Analisis Sentimen */
    .sentiment-badge {
        font-weight: 700;
        font-size: 11px;
        padding: 5px 10px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .sentiment-positive { background-color: #dcfce7; color: #16a34a; }
    .sentiment-negative { background-color: #fee2e2; color: #dc2626; }
    .sentiment-neutral { background-color: #f1f5f9; color: #64748b; }
</style>

<!-- HEADER SECTION (Sudah pakai kotak putih seragam) -->
<div class="header-card">
    <label class="form-label text-muted fw-bold text-uppercase mb-2" style="font-size: 13px; letter-spacing: 1px;">
        <i class="fas fa-globe-americas me-2 text-primary"></i> News Intelligence
    </label>
    <h5 class="fw-bold text-dark mb-1">Global Market Updates</h5>
    <span class="text-muted" style="font-size: 13px;">Pemantauan otomatis sentimen berita global seputar Logistics, Trade, Shipping & Economy.</span>
</div>

<!-- KONTENER GRID BERITA -->
<div class="row g-4" id="news-container">
    <!-- Konten Berita akan dimuat di sini oleh JavaScript -->
</div>

@endsection

@push('scripts')
<script>
    const apiKey = 'eb68ad8b81cd4badd54a7b8406a0a7b2';
    
    // Variabel global untuk menyimpan kamus dari Database
    let dbPositiveWords = [];
    let dbNegativeWords = [];

    // 1. Fungsi Mengambil Kamus Sentimen dari Database
    async function fetchSentimentDictionary() {
        try {
            const response = await fetch('/api/sentiment-words');
            const data = await response.json();
            dbPositiveWords = data.positive;
            dbNegativeWords = data.negative;
        } catch (error) {
            console.error("Gagal memuat kamus sentimen dari database:", error);
            // Fallback (cadangan) kalau database gagal diakses
            dbPositiveWords = ['growth', 'increase', 'profit', 'stable', 'improve', 'boom', 'deal'];
            dbNegativeWords = ['war', 'crisis', 'inflation', 'delay', 'disaster', 'tariff', 'strike'];
        }
    }

    // 2. FUNGSI NLP MINI (Menganalisis teks pakai data Database)
    function analyzeSentiment(text) {
        const lowerText = text.toLowerCase();
        let score = 0;
        
        dbPositiveWords.forEach(word => {
            const regex = new RegExp('\\b' + word.toLowerCase() + '\\b', 'g');
            const matches = lowerText.match(regex);
            if(matches) score += matches.length;
        });
        
        dbNegativeWords.forEach(word => {
            const regex = new RegExp('\\b' + word.toLowerCase() + '\\b', 'g');
            const matches = lowerText.match(regex);
            if(matches) score -= matches.length;
        });

        if (score > 0) return { label: 'Positif', class: 'sentiment-positive', icon: 'fa-arrow-trend-up' };
        if (score < 0) return { label: 'Negatif', class: 'sentiment-negative', icon: 'fa-arrow-trend-down' };
        return { label: 'Netral', class: 'sentiment-neutral', icon: 'fa-minus' };
    }

    // 3. Fungsi Menarik Berita dari GNews
    async function fetchAllNews() {
        const container = document.getElementById('news-container');
        
        container.innerHTML = `
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                <h5 class="mt-3 text-muted fw-bold">Memindai Berita & Menganalisis Sentimen...</h5>
            </div>
        `;

        try {
            // Langsung memanggil gabungan 4 topik sekaligus
            const queryKeyword = 'logistics OR trade OR shipping OR economy';
            const query = encodeURIComponent(queryKeyword);
            
            // Catatan: GNews Free Tier membatasi maksimal 10 artikel per request.
            const url = `https://gnews.io/api/v4/search?q=${query}&lang=en&max=10&apikey=${apiKey}`;
            
            const response = await fetch(url);
            const data = await response.json();

            container.innerHTML = '';

            if (data.articles && data.articles.length > 0) {
                data.articles.forEach(article => {
                    const fallbackImg = 'https://images.unsplash.com/photo-1586528116311-ad8c738759be?q=80&w=800&auto=format&fit=crop';
                    const imgUrl = article.image || fallbackImg;
                    
                    const dateObj = new Date(article.publishedAt);
                    const formattedDate = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

                    const fullText = article.title + " " + article.description;
                    const sentiment = analyzeSentiment(fullText);

                    const cardHTML = `
                        <div class="col-md-6 col-lg-4">
                            <div class="news-card">
                                <div class="news-image-wrapper">
                                    <img src="${imgUrl}" class="news-image" alt="News Image">
                                </div>
                                <div class="card-body d-flex flex-column p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex gap-2 align-items-center">
                                            <span class="news-source-badge">
                                                <i class="fas fa-newspaper me-1"></i> ${article.source.name}
                                            </span>
                                            <span class="sentiment-badge ${sentiment.class}" title="Dianalisis menggunakan Database Kamus Sentimen">
                                                <i class="fas ${sentiment.icon}"></i> ${sentiment.label}
                                            </span>
                                        </div>
                                    </div>
                                    <small class="text-muted fw-bold mb-2 d-block" style="font-size:12px;">
                                        <i class="far fa-clock me-1"></i> Dipublikasikan: ${formattedDate}
                                    </small>
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
                container.innerHTML = `<div class="col-12 text-center py-5"><h5 class="text-muted">Tidak ada berita ditemukan.</h5></div>`;
            }
        } catch (error) {
            container.innerHTML = `<div class="col-12 text-center py-5 text-danger"><h5 class="fw-bold">Gagal terhubung ke GNews API</h5></div>`;
        }
    }

    // 4. Jalankan sistem saat halaman dimuat
    document.addEventListener('DOMContentLoaded', async () => {
        await fetchSentimentDictionary(); // Tunggu sampai kamus ditarik dari Database
        fetchAllNews(); // Langsung tarik semua topik gabungan
    });
</script>
@endpush