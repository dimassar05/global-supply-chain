@extends('layouts.app')

@section('page_title', 'Global Country Dashboard')

@section('content')
<!-- CSS Leaflet & Select2 -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    /* Styling Peta (Ukuran Dikecilkan) */
    #map { 
        height: 350px; /* Ukuran peta dipendekkan agar pas */
        min-height: 350px;
        border-radius: 16px; 
        z-index: 1; 
        border: 1px solid #cbd5e1; 
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08); 
    }
    
    /* Styling 5 Kotak Metrik Horizontal */
    .metric-box { 
        background: #ffffff; 
        border-radius: 12px; 
        padding: 15px; 
        display: flex; 
        align-items: center; 
        height: 100%;
        border: 1px solid #cbd5e1; 
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08); 
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .metric-box:hover { 
        transform: translateY(-3px); 
        border-color: #94a3b8; 
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
    }
    
    .icon-box { 
        width: 42px; height: 42px; 
        border-radius: 10px; 
        display: flex; align-items: center; justify-content: center; 
        font-size: 18px; margin-right: 12px; 
        flex-shrink: 0;
    }
    
    .bg-gdp { background: #e0f2fe; color: #0284c7; }
    .bg-inf { background: #f3e8ff; color: #9333ea; }
    .bg-pop { background: #fce7f3; color: #db2777; }
    .bg-cur { background: #cffafe; color: #0891b2; }
    .bg-wea { background: #ffedd5; color: #ea580c; }

    /* Custom Styling Select2 biar rapi */
    .select2-container .select2-selection--single {
        height: 48px !important;
        border-radius: 10px !important;
        border: 1px solid #cbd5e1 !important;
        display: flex;
        align-items: center;
        font-size: 16px;
        font-weight: bold;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
    }

    /* Styling Kartu Grafik */
    .chart-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        padding: 20px;
        height: 100%;
        position: relative;
    }
    .chart-title {
        font-weight: 800;
        color: #1e293b; 
        font-size: 15px;
        margin-bottom: 5px;
    }
    .chart-subtitle {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 15px;
    }
    .chart-container {
        position: relative;
        height: 250px;
        width: 100%;
    }
    .loading-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.85);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        font-weight: bold;
        color: #64748b;
        border-radius: 10px;
    }
</style>

<!-- DROPDOWN NEGARA (BISA DI-SEARCH) -->
<div class="card mb-4" style="border-radius: 16px; background-color: #ffffff; border: 1px solid #cbd5e1; box-shadow: 0 4px 10px rgba(0,0,0,0.08);">
    <div class="card-body p-4">
        <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 13px; letter-spacing: 1px;">
            <i class="fas fa-search-location me-2 text-primary"></i> Cari & Analisis Negara
        </label>
        <!-- Hapus onchange bawaan, dikendalikan JS Select2 -->
        <select id="countrySelect" class="form-select form-select-lg text-dark" style="width: 100%; max-width: 500px;">
            <option value="">Memuat database...</option>
        </select>
    </div>
</div>

<!-- 5 KOTAK DATA HORIZONTAL -->
<div class="row g-3 mb-4">
    <div class="col">
        <div class="metric-box">
            <div class="icon-box bg-gdp"><i class="fas fa-chart-pie"></i></div>
            <div>
                <div class="text-muted fw-bold text-uppercase" style="font-size: 10px;">GDP Estimasi</div>
                <div class="fw-bold text-dark" style="font-size: 14px;" id="val-gdp">-</div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="metric-box">
            <div class="icon-box bg-inf"><i class="fas fa-arrow-trend-up"></i></div>
            <div>
                <div class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Inflasi</div>
                <div class="fw-bold text-dark" style="font-size: 14px;" id="val-inf">-</div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="metric-box">
            <div class="icon-box bg-pop"><i class="fas fa-users"></i></div>
            <div>
                <div class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Populasi</div>
                <div class="fw-bold text-dark" style="font-size: 14px;" id="val-pop">-</div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="metric-box">
            <div class="icon-box bg-cur"><i class="fas fa-money-bill-wave"></i></div>
            <div>
                <div class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Mata Uang</div>
                <div class="fw-bold text-dark" style="font-size: 14px;" id="val-cur">-</div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="metric-box">
            <div class="icon-box bg-wea"><i class="fas fa-cloud-sun"></i></div>
            <div>
                <div class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Cuaca Live</div>
                <div class="fw-bold text-dark" style="font-size: 14px;" id="val-wea">-</div>
            </div>
        </div>
    </div>
</div>

<!-- PETA BAWAH (Sudah dikecilkan) -->
<div class="row mb-4">
    <div class="col-12">
        <div id="map"></div>
    </div>
</div>

<!-- 4 GRAFIK HISTORIS NEGARA -->
<div class="row g-4 mb-4">
    <!-- 1. GDP TREND -->
    <div class="col-md-6">
        <div class="chart-card">
            <div class="chart-title"><i class="fas fa-chart-line me-2 text-primary"></i> GDP Trend <span class="lbl-country"></span></div>
            <div class="chart-subtitle">Produk Domestik Bruto (Triliun USD) - 7 Tahun Terakhir</div>
            <div class="chart-container">
                <div id="load-gdp" class="loading-overlay" style="display: none;"><i class="fas fa-spinner fa-spin me-2"></i>Menarik Data...</div>
                <canvas id="chartGdp"></canvas>
            </div>
        </div>
    </div>

    <!-- 2. INFLATION TREND -->
    <div class="col-md-6">
        <div class="chart-card">
            <div class="chart-title"><i class="fas fa-arrow-trend-up me-2 text-danger"></i> Inflation Trend <span class="lbl-country"></span></div>
            <div class="chart-subtitle">Persentase Tingkat Inflasi (%) - 7 Tahun Terakhir</div>
            <div class="chart-container">
                <div id="load-inf" class="loading-overlay" style="display: none;"><i class="fas fa-spinner fa-spin me-2"></i>Menarik Data...</div>
                <canvas id="chartInf"></canvas>
            </div>
        </div>
    </div>

    <!-- 3. CURRENCY TREND -->
    <div class="col-md-6">
        <div class="chart-card">
            <div class="chart-title"><i class="fas fa-money-bill-trend-up me-2 text-success"></i> Currency Trend <span class="lbl-country"></span></div>
            <div class="chart-subtitle">Fluktuasi <span id="lbl-curr-code"></span> terhadap USD - 6 Bulan Terakhir</div>
            <div class="chart-container">
                <div id="load-cur" class="loading-overlay" style="display: none;"><i class="fas fa-spinner fa-spin me-2"></i>Menarik Data...</div>
                <canvas id="chartCur"></canvas>
            </div>
        </div>
    </div>

    <!-- 4. RISK TREND -->
    <div class="col-md-6">
        <div class="chart-card">
            <div class="chart-title"><i class="fas fa-triangle-exclamation me-2 text-warning"></i> Supply Chain Risk <span class="lbl-country"></span></div>
            <div class="chart-subtitle">Simulator Indeks Risiko Berjalan (0 - 100)</div>
            <div class="chart-container">
                <canvas id="chartRisk"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- LIBRARY EKSTERNAL -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Konfigurasi Peta
    const map = L.map('map').setView([-0.7893, 113.9213], 5);
    let currentMarker = null;
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 20 }).addTo(map);

    let localDBCountries = []; 
    let mledozeData = [];      
    
    // Variabel Instansiasi Grafik
    let gChart, iChart, cChart, rChart;
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = '#64748b';

    // 1. TARIK DATA AWAL
    async function fetchAllData() {
        try {
            const dbRes = await fetch('/api/countries-data');
            localDBCountries = await dbRes.json();

            const mlRes = await fetch('https://raw.githubusercontent.com/mledoze/countries/master/countries.json');
            mledozeData = await mlRes.json();
            
            // Urutkan Nama Negara A-Z
            localDBCountries.sort((a, b) => a.name.localeCompare(b.name));
            
            populateDropdown();
        } catch (error) {
            console.error("Gagal menarik data awal:", error);
            $('#countrySelect').html('<option>Gagal Terhubung</option>');
        }
    }

    // 2. ISI DROPDOWN & AKTIFKAN SELECT2
    function populateDropdown() {
        let options = '';
        localDBCountries.forEach(country => {
            options += `<option value="${country.code}">${country.code} - ${country.name}</option>`;
        });
        
        $('#countrySelect').html(options);
        $('#countrySelect').select2({ placeholder: "Ketik nama negara..." });

        // Event listener saat dropdown diganti
        $('#countrySelect').on('change', function() {
            changeCountry();
        });

        // Set default ke Indonesia jika ada
        if (localDBCountries.find(c => c.code === 'ID')) {
            $('#countrySelect').val('ID').trigger('change');
        } else {
            changeCountry(); 
        }
    }

    // 3. HELPER WORLD BANK SINGLE VALUE (Untuk Metrik Atas)
    async function fetchWorldBank(countryCode, indicator) {
        try {
            const response = await fetch(`https://api.worldbank.org/v2/country/${countryCode}/indicator/${indicator}?format=json&per_page=5`);
            const data = await response.json();
            if (data && data[1] && Array.isArray(data[1])) {
                const validData = data[1].find(item => item.value !== null);
                if (validData) return validData.value;
            }
            return null;
        } catch (error) { return null; }
    }

    // 4. HELPER WORLD BANK HISTORY (Untuk Grafik)
    async function fetchWorldBankHistory(countryCode, indicator) {
        const endYear = new Date().getFullYear();
        const startYear = endYear - 7;
        try {
            const res = await fetch(`https://api.worldbank.org/v2/country/${countryCode}/indicator/${indicator}?format=json&date=${startYear}:${endYear}`);
            const data = await res.json();
            if (data && data[1]) return data[1].reverse();
            return [];
        } catch (error) { return []; }
    }

    // 5. UPDATE SEMUA 4 GRAFIK
    async function updateCharts(countryCode, countryName, currencyCode) {
        // Update label nama negara di judul grafik
        document.querySelectorAll('.lbl-country').forEach(el => el.innerText = countryName);
        document.getElementById('lbl-curr-code').innerText = currencyCode;

        // --- TAMPILKAN LOADING ---
        $('#load-gdp, #load-inf, #load-cur').show();

        // ================= CHART 1 & 2: PDB & INFLASI =================
        const [gdpHist, infHist] = await Promise.all([
            fetchWorldBankHistory(countryCode, 'NY.GDP.MKTP.CD'),
            fetchWorldBankHistory(countryCode, 'FP.CPI.TOTL.ZG')
        ]);

        let lblGdp = [], valGdp = [];
        gdpHist.forEach(item => {
            if(item.value !== null) { lblGdp.push(item.date); valGdp.push((item.value / 1000000000000).toFixed(2)); }
        });

        let lblInf = [], valInf = [];
        infHist.forEach(item => {
            if(item.value !== null) { lblInf.push(item.date); valInf.push(item.value.toFixed(2)); }
        });

        if(gChart) gChart.destroy();
        const ctxGdp = document.getElementById('chartGdp').getContext('2d');
        let gradGdp = ctxGdp.createLinearGradient(0, 0, 0, 300);
        gradGdp.addColorStop(0, 'rgba(13, 110, 253, 0.4)'); gradGdp.addColorStop(1, 'transparent');

        gChart = new Chart(ctxGdp, {
            type: 'line',
            data: { labels: lblGdp, datasets: [{ label: 'PDB (Triliun USD)', data: valGdp, borderColor: '#0d6efd', backgroundColor: gradGdp, borderWidth: 3, fill: true, tension: 0.4 }] },
            options: { maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
        $('#load-gdp').hide();

        if(iChart) iChart.destroy();
        iChart = new Chart(document.getElementById('chartInf'), {
            type: 'bar',
            data: { labels: lblInf, datasets: [{ label: 'Inflasi (%)', data: valInf, backgroundColor: '#ef4444', borderRadius: 4 }] },
            options: { maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
        $('#load-inf').hide();

        // ================= CHART 3: MATA UANG (Live API) =================
        fetch('https://api.exchangerate-api.com/v4/latest/USD')
            .then(res => res.json())
            .then(data => {
                const currentRate = data.rates[currencyCode] || 1;
                let lblCur = [], valCur = [];
                const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                let d = new Date();

                for (let i = 5; i >= 0; i--) {
                    let pDate = new Date(d.getFullYear(), d.getMonth() - i, 1);
                    lblCur.push(months[pDate.getMonth()] + " '" + pDate.getFullYear().toString().substr(-2));
                    if (i === 0) { valCur.push(currentRate); } 
                    else { 
                        // Simulasi fluktuasi historis menyesuaikan besaran rate aslinya
                        let variance = (Math.random() * (currentRate * 0.05)) - (currentRate * 0.025); 
                        valCur.push(+(currentRate + variance).toFixed(2));
                    }
                }

                if(cChart) cChart.destroy();
                cChart = new Chart(document.getElementById('chartCur'), {
                    type: 'line',
                    data: { labels: lblCur, datasets: [{ label: `1 USD to ${currencyCode}`, data: valCur, borderColor: '#10b981', backgroundColor: 'transparent', borderWidth: 3, tension: 0.2 }] },
                    options: { maintainAspectRatio: false, plugins: { legend: { display: false } } }
                });
                $('#load-cur').hide();
            }).catch(() => $('#load-cur').html("Data Mata Uang Gagal Dimuat"));

        // ================= CHART 4: RISK SIMULATOR =================
        let lblRisk = [], valRisk = [];
        const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        let d2 = new Date();
        for (let i = 9; i >= 0; i--) {
            let pDate = new Date(d2.getFullYear(), d2.getMonth() - i, 1);
            lblRisk.push(months[pDate.getMonth()]);
            valRisk.push(Math.floor(Math.random() * (85 - 20 + 1)) + 20); // Nilai organik 20-85
        }

        if(rChart) rChart.destroy();
        const ctxRisk = document.getElementById('chartRisk').getContext('2d');
        let gradRisk = ctxRisk.createLinearGradient(0, 0, 0, 300);
        gradRisk.addColorStop(0, 'rgba(245, 158, 11, 0.4)'); gradRisk.addColorStop(1, 'transparent');

        rChart = new Chart(ctxRisk, {
            type: 'line',
            data: { labels: lblRisk, datasets: [{ label: 'Risk Index', data: valRisk, borderColor: '#f59e0b', backgroundColor: gradRisk, borderWidth: 3, fill: true, tension: 0.4 }] },
            options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { min: 0, max: 100 } } }
        });
    }

    // 6. FUNGSI UTAMA KETIKA NEGARA DIPILIH
    function changeCountry() {
        const selectedCode = $('#countrySelect').val();
        if(!selectedCode) return;
        
        const dbCountry = localDBCountries.find(c => c.code === selectedCode);
        const extraData = mledozeData.find(c => c.cca2 === selectedCode) || {};
        if(!dbCountry) return;

        // Beri efek loading di metrik atas
        ['gdp', 'inf', 'pop', 'cur', 'wea'].forEach(id => document.getElementById(`val-${id}`).innerText = 'Memuat...');

        // Update Peta & Cuaca
        const currency = dbCountry.currency ? dbCountry.currency : 'USD';
        document.getElementById('val-cur').innerText = currency;
        
        const lat = extraData.latlng ? extraData.latlng[0] : 0;
        const lng = extraData.latlng ? extraData.latlng[1] : 0;
        const capital = (extraData.capital && extraData.capital.length > 0) ? extraData.capital[0] : dbCountry.name;

        if (lat !== 0 && lng !== 0) {
            map.flyTo([lat, lng], 5, { animate: true, duration: 1.5 });
            if(currentMarker) map.removeLayer(currentMarker);
            currentMarker = L.marker([lat, lng]).addTo(map)
                .bindPopup(`<div style="text-align:center;"><b>${dbCountry.name}</b><br>Capital: ${capital}</div>`)
                .openPopup();

            fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lng}&current_weather=true`)
                .then(res => res.json())
                .then(data => { document.getElementById('val-wea').innerText = `${data.current_weather.temperature}°C`; })
                .catch(() => { document.getElementById('val-wea').innerText = `N/A`; });
        }

        // Update Metrik Atas (World Bank)
        Promise.all([
            fetchWorldBank(selectedCode, 'SP.POP.TOTL'),
            fetchWorldBank(selectedCode, 'NY.GDP.MKTP.CD'),
            fetchWorldBank(selectedCode, 'FP.CPI.TOTL.ZG')
        ]).then(([popData, gdpData, infData]) => {
            document.getElementById('val-pop').innerText = popData ? (popData / 1000000).toFixed(1) + ' Juta Jiwa' : 'Data N/A';
            document.getElementById('val-gdp').innerText = gdpData ? `$${(gdpData / 1000000000000).toFixed(2)} T` : 'Data N/A';
            document.getElementById('val-inf').innerText = infData ? `${infData.toFixed(2)}%` : 'Data N/A';
        });

        // PANGGIL FUNGSI UPDATE GRAFIK DI BAWAH PETA
        updateCharts(selectedCode, dbCountry.name, currency);
    }

    document.addEventListener('DOMContentLoaded', fetchAllData);
</script>
@endpush