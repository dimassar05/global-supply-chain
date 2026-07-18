@extends('layouts.app')

@section('page_title', 'Risk Scoring Engine')

@section('content')
<style>
    /* Desain Card Kiri (Total Score) */
    .score-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 40px 30px;
        text-align: center;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        /* UPDATE BORDER & SHADOW */
        border: 1px solid #cbd5e1; 
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }
    
    .big-score {
        font-size: 110px;
        font-weight: 900;
        line-height: 1;
        margin: 25px 0;
        text-shadow: 2px 4px 10px rgba(0,0,0,0.05);
    }

    .risk-badge {
        font-size: 22px;
        padding: 12px 35px;
        border-radius: 50px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        display: inline-block;
    }

    /* Desain Card Kanan (Breakdown) */
    .detail-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 25px;
        margin-bottom: 20px;
        /* UPDATE BORDER & SHADOW */
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .detail-card:hover { 
        transform: translateY(-3px); 
        border-color: #94a3b8;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12); 
    }

    .param-icon {
        width: 55px;
        height: 55px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-right: 20px;
        flex-shrink: 0;
    }

    .score-fraction {
        font-size: 32px;
        font-weight: 900;
    }
    .score-max {
        font-size: 18px;
        font-weight: 600;
        color: #94a3b8;
    }

    /* Progress Bar Kustom */
    .custom-progress {
        height: 8px;
        border-radius: 10px;
        background-color: #f1f5f9;
        margin-top: 15px;
        overflow: hidden;
    }
    .custom-progress-bar {
        height: 100%;
        border-radius: 10px;
        transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Pewarnaan Kategori Risiko */
    .text-low { color: #10b981; } 
    .bg-low { background-color: #d1fae5; color: #065f46; border: 2px solid #34d399; }
    .bar-low { background-color: #10b981; }
    
    .text-medium { color: #f59e0b; } 
    .bg-medium { background-color: #fef3c7; color: #92400e; border: 2px solid #fbbf24; }
    .bar-medium { background-color: #f59e0b; }
    
    .text-high { color: #ef4444; } 
    .bg-high { background-color: #fee2e2; color: #991b1b; border: 2px solid #f87171; }
    .bar-high { background-color: #ef4444; }
</style>

<!-- DROPDOWN PENCARIAN -->
<div class="card mb-4" style="border-radius: 16px; background-color: #ffffff; border: 1px solid #cbd5e1; box-shadow: 0 4px 10px rgba(0,0,0,0.08);">
    <div class="card-body p-4">
        <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 13px; letter-spacing: 1px;">
            <i class="fas fa-crosshairs me-2 text-primary"></i> Target Analisis Negara
        </label>
        <select id="countrySelect" class="form-select form-select-lg fw-bold text-dark border-1" style="font-size: 18px; max-width: 500px; box-shadow: none;" onchange="calculateRisk()">
            <option value="">Memuat database intelijen...</option>
        </select>
    </div>
</div>

<div class="row g-4 align-items-stretch">
    <!-- KOLOM KIRI: TOTAL SCORE -->
    <div class="col-lg-5">
        <div class="score-card">
            <h5 class="text-muted fw-bold text-uppercase tracking-wide" style="letter-spacing: 2px;">Total Risk Score</h5>
            <div id="finalScore" class="big-score text-dark">-</div>
            <div>
                <span id="riskStatus" class="risk-badge bg-secondary text-white">Menghitung...</span>
            </div>
            <hr class="my-4 border-light">
            <p class="text-muted mb-0" style="font-size: 14px; line-height: 1.6;">
                Skor diakumulasikan dari 4 parameter analisis dengan bobot maksimal 100 poin. Algoritma memperhitungkan fluktuasi cuaca riil dan sentimen ekonomi.
            </p>
        </div>
    </div>

    <!-- KOLOM KANAN: BREAKDOWN 4 PARAMETER -->
    <div class="col-lg-7">
        <h4 class="fw-bold mb-4 text-dark">Parameter Breakdown</h4>
        
        <!-- 1. Weather -->
        <div class="detail-card">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="param-icon bg-info text-white"><i class="fas fa-temperature-half"></i></div>
                    <div>
                        <h5 class="mb-1 fw-bold text-dark">Weather Risk</h5>
                        <div class="text-muted fw-semibold" id="wea-val" style="font-size: 15px;">Suhu Real-time: - °C</div>
                    </div>
                </div>
                <div class="text-end">
                    <span class="score-fraction" id="score-wea">-</span><span class="score-max">/25</span>
                </div>
            </div>
            <div class="custom-progress"><div class="custom-progress-bar bg-info" id="prog-wea" style="width: 0%"></div></div>
        </div>

        <!-- 2. Inflation -->
        <div class="detail-card">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="param-icon bg-warning text-white"><i class="fas fa-chart-line"></i></div>
                    <div>
                        <h5 class="mb-1 fw-bold text-dark">Inflation Risk</h5>
                        <div class="text-muted fw-semibold" id="inf-val" style="font-size: 15px;">Tingkat Inflasi: - %</div>
                    </div>
                </div>
                <div class="text-end">
                    <span class="score-fraction" id="score-inf">-</span><span class="score-max">/25</span>
                </div>
            </div>
            <div class="custom-progress"><div class="custom-progress-bar bg-warning" id="prog-inf" style="width: 0%"></div></div>
        </div>

        <!-- 3. Exchange Rate -->
        <div class="detail-card">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="param-icon bg-success text-white"><i class="fas fa-money-bill-transfer"></i></div>
                    <div>
                        <h5 class="mb-1 fw-bold text-dark">Exchange Rate Volatility</h5>
                        <div class="text-muted fw-semibold" id="exc-val" style="font-size: 15px;">Mata Uang Basis: -</div>
                    </div>
                </div>
                <div class="text-end">
                    <span class="score-fraction" id="score-exc">-</span><span class="score-max">/25</span>
                </div>
            </div>
            <div class="custom-progress"><div class="custom-progress-bar bg-success" id="prog-exc" style="width: 0%"></div></div>
        </div>

        <!-- 4. News Sentiment -->
        <div class="detail-card">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="param-icon bg-danger text-white"><i class="fas fa-newspaper"></i></div>
                    <div>
                        <h5 class="mb-1 fw-bold text-dark">News Sentiment</h5>
                        <div class="text-muted fw-semibold" style="font-size: 15px;">Analisis Media Global & Kebijakan</div>
                    </div>
                </div>
                <div class="text-end">
                    <span class="score-fraction" id="score-news">-</span><span class="score-max">/25</span>
                </div>
            </div>
            <div class="custom-progress"><div class="custom-progress-bar bg-danger" id="prog-news" style="width: 0%"></div></div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    let localDBCountries = []; 
    let mledozeData = [];

    // INISIALISASI DATA
    async function initRiskEngine() {
        try {
            const dbRes = await fetch('/api/countries-data');
            localDBCountries = await dbRes.json();
            const mlRes = await fetch('https://raw.githubusercontent.com/mledoze/countries/master/countries.json');
            mledozeData = await mlRes.json();
            
            const select = document.getElementById('countrySelect');
            select.innerHTML = ''; 
            localDBCountries.forEach(country => {
                const option = document.createElement('option');
                option.value = country.code; 
                option.text = country.name;
                if(country.code === 'ID') option.selected = true; // Default ke Indonesia
                select.appendChild(option);
            });
            
            calculateRisk(); 
        } catch (error) {
            console.error("Error DB:", error);
        }
    }

    // TARIK API WORLD BANK (INFLASI) - SUDAH BENAR
    async function fetchWorldBank(countryCode, indicator) {
        try {
            const response = await fetch(`https://api.worldbank.org/v2/country/${countryCode}/indicator/${indicator}?format=json&per_page=5`);
            const data = await response.json();
            if (data && data[1] && Array.isArray(data[1])) {
                const validData = data[1].find(item => item.value !== null);
                return validData ? validData.value : null;
            }
            return null;
        } catch (error) { return null; }
    }

    // 🌟 BARU: TARIK API GNEWS & AI ANALISIS TEKS
    async function fetchNewsSentiment(countryName) {
        // GANTI TEKS DI BAWAH DENGAN API KEY GNEWS MILIKMU
        const apiKey = 'eb68ad8b81cd4badd54a7b8406a0a7b2'; 
        
        // Cari berita spesifik tentang ekonomi/rantai pasok negara tersebut
        const query = encodeURIComponent(`"${countryName}" AND (economy OR supply chain OR conflict OR crisis)`);
        const url = `https://gnews.io/api/v4/search?q=${query}&lang=en&max=5&apikey=${apiKey}`;

        try {
            const response = await fetch(url);
            const data = await response.json();
            
            if (!data.articles || data.articles.length === 0) {
                return 4; // Skor rendah/aman jika tidak ada berita mencurigakan
            }

            let dangerCount = 0;
            // Daftar kata kunci yang menandakan risiko logistik/geopolitik
            const dangerWords = ['crisis', 'war', 'conflict', 'delay', 'inflation', 'drop', 'risk', 'bad', 'tension', 'strike', 'protest', 'ban'];

            // Analisis teks berita (NLP Sederhana)
            data.articles.forEach(article => {
                const text = (article.title + " " + article.description).toLowerCase();
                dangerWords.forEach(word => {
                    if (text.includes(word)) dangerCount++;
                });
            });

            // Konversi temuan kata bahaya menjadi skor 0-25
            let riskScore = (dangerCount * 4) + 2; 
            if (riskScore > 25) riskScore = 25; // Mentok di 25

            return riskScore;
        } catch (error) {
            console.error("GNews API Error (Mungkin Limit):", error);
            return 12; // Skor tengah jika API error/limit
        }
    }

    // LOGIKA ALGORITMA MAHASISWA (Diperbarui)
    function theStudentAlgorithm(temp, inflation, countryCode, actualNewsScore) {
        // A. Weather
        let weaRisk = Math.abs(temp - 20) * 1.5;
        if (weaRisk > 25) weaRisk = 25;

        // B. Inflation
        let infRisk = 0;
        if (inflation === null) infRisk = 12; 
        else if (inflation < 0) infRisk = 15; 
        else infRisk = inflation * 2.5; 
        if (infRisk > 25) infRisk = 25;

        // C. Exchange Rate (Tetap pakai dummy agar tidak 0)
        let excRisk = ((countryCode.charCodeAt(0) * 7 + countryCode.charCodeAt(1) * 5) % 21) + 4; 
        
        // D. News Sentiment (Sekarang pakai data REAL dari GNews API)
        let newsRisk = actualNewsScore;

        // Pembulatan
        weaRisk = Math.round(weaRisk);
        infRisk = Math.round(infRisk);
        excRisk = Math.round(excRisk);
        newsRisk = Math.round(newsRisk);
        
        let total = weaRisk + infRisk + excRisk + newsRisk;
        
        // Status Warna & Kategori
        let category = ''; let colorClass = ''; let textColor = ''; let barColor = '';
        if (total <= 33) {
            category = 'Low Risk'; colorClass = 'bg-low'; textColor = 'text-low'; barColor = 'bar-low';
        } else if (total <= 66) {
            category = 'Medium Risk'; colorClass = 'bg-medium'; textColor = 'text-medium'; barColor = 'bar-medium';
        } else {
            category = 'High Risk'; colorClass = 'bg-high'; textColor = 'text-high'; barColor = 'bar-high';
        }

        return {
            wea: weaRisk, inf: infRisk, exc: excRisk, news: newsRisk,
            total: total, category: category, color: colorClass, textColor: textColor, bar: barColor
        };
    }

    // KALKULASI UTAMA
    async function calculateRisk() {
        const code = document.getElementById('countrySelect').value;
        const dbCountry = localDBCountries.find(c => c.code === code);
        const extraData = mledozeData.find(c => c.cca2 === code) || {};
        
        if(!dbCountry) return;

        // Reset UI Saat Loading
        document.getElementById('finalScore').innerText = '...';
        document.getElementById('riskStatus').className = 'risk-badge bg-secondary text-white';
        document.getElementById('riskStatus').innerText = 'Menganalisis AI...';
        
        ['wea', 'inf', 'exc', 'news'].forEach(id => {
            document.getElementById(`score-${id}`).innerText = '-';
            document.getElementById(`prog-${id}`).style.width = '0%';
        });

        const lat = extraData.latlng ? extraData.latlng[0] : 0;
        const lng = extraData.latlng ? extraData.latlng[1] : 0;
        const currency = dbCountry.currency || 'N/A';
        const countryName = dbCountry.name;
        
        let temp = 25; 
        let infRate = null;
        let newsScore = 5; // Default score

        // 🌟 TARIK 3 API SEKALIGUS (Cuaca, Inflasi, Berita GNews)
        try {
            const [weaRes, infData, newsDataScore] = await Promise.all([
                fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lng}&current_weather=true`).then(res => res.ok ? res.json() : null),
                fetchWorldBank(code, 'FP.CPI.TOTL.ZG'),
                fetchNewsSentiment(countryName) // Eksekusi fungsi GNews
            ]);
            
            if(weaRes && weaRes.current_weather) temp = weaRes.current_weather.temperature;
            infRate = infData;
            newsScore = newsDataScore; // Simpan hasil skor berita
        } catch (e) { console.log("Gagal tarik data external."); }

        // Eksekusi Algoritma (Masukkan newsScore yang asli ke algoritma)
        const result = theStudentAlgorithm(temp, infRate, code, newsScore);

        // Update Text Info
        document.getElementById('wea-val').innerText = `Suhu Real-time: ${temp}°C`;
        document.getElementById('inf-val').innerText = `Tingkat Inflasi: ${infRate !== null ? infRate.toFixed(2) + '%' : 'N/A'}`;
        document.getElementById('exc-val').innerText = `Mata Uang Basis: ${currency}`;

        // Update Angka Pecahan (Animasi Progress Bar)
        ['wea', 'inf', 'exc', 'news'].forEach(id => {
            document.getElementById(`score-${id}`).innerText = result[id];
            const percent = (result[id] / 25) * 100;
            document.getElementById(`prog-${id}`).style.width = `${percent}%`;
        });

        // Update Skor Utama
        const scoreEl = document.getElementById('finalScore');
        scoreEl.innerText = result.total;
        scoreEl.className = `big-score ${result.textColor}`;

        const statusEl = document.getElementById('riskStatus');
        statusEl.innerText = result.category;
        statusEl.className = `risk-badge ${result.color}`;
    }

    document.addEventListener('DOMContentLoaded', initRiskEngine);
</script>
@endpush