@extends('layouts.app')

@section('page_title', 'Favorite Watchlist')

@section('content')
<!-- CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    /* Styling Header & Search */
    .watchlist-header {
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    /* Styling Select2 biar menyatu dengan tema */
    .select2-container .select2-selection--single {
        height: 45px !important;
        border-radius: 10px !important;
        border: 1px solid #cbd5e1 !important;
        display: flex;
        align-items: center;
        font-weight: bold;
        color: #1e293b;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 43px !important;
    }

    /* Kartu Negara Favorit */
    .watch-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        padding: 20px;
        height: 100%;
        position: relative;
        transition: all 0.3s ease;
    }
    .watch-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1;
    }

    /* Tombol Hapus */
    .btn-remove {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #fef2f2;
        color: #ef4444;
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-remove:hover {
        background: #ef4444;
        color: #ffffff;
    }

    /* Bagian Header Kartu */
    .watch-title {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 2px;
        padding-right: 40px; /* Space untuk tombol hapus */
    }
    .watch-iso {
        font-size: 12px;
        color: #64748b;
        font-weight: 700;
        letter-spacing: 1px;
        margin-bottom: 20px;
    }

    /* Grid Metrik Mini */
    .mini-metrics {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }
    .mini-metric-item {
        background: #f8fafc;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid #f1f5f9;
    }
    .mini-metric-label {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .mini-metric-val {
        font-size: 14px;
        color: #1e293b;
        font-weight: 800;
    }
    .mini-icon {
        margin-right: 6px;
        color: #94a3b8;
    }

    /* Pesan Kosong */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #ffffff;
        border-radius: 16px;
        border: 2px dashed #cbd5e1;
        color: #64748b;
    }
    .empty-state i {
        font-size: 48px;
        color: #cbd5e1;
        margin-bottom: 15px;
    }
</style>

<!-- HEADER & TAMBAH NEGARA -->
<div class="watchlist-header">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-bookmark text-primary me-2"></i> Watchlist Monitoring</h5>
        <span class="text-muted" style="font-size: 13px;">Pantau metrik utama dari negara-negara pilihan Anda secara real-time.</span>
    </div>
    <div style="width: 100%; max-width: 350px; display: flex; gap: 10px;">
        <select id="countrySelect" class="form-select" style="width: 100%;">
            <option value="">Memuat data...</option>
        </select>
        <button class="btn btn-primary fw-bold px-4" onclick="addToWatchlist()" style="border-radius: 10px;">
            <i class="fas fa-plus"></i>
        </button>
    </div>
</div>

<!-- GRID KARTU WATCHLIST -->
<div class="row g-4" id="watchlistContainer">
    <!-- Kartu akan di-generate oleh JS di sini -->
</div>

<!-- EMPTY STATE (Jika belum ada yang di-save) -->
<div id="emptyState" class="empty-state" style="display: none;">
    <i class="fas fa-folder-open"></i>
    <h5 class="fw-bold text-dark">Watchlist Masih Kosong</h5>
    <p class="mb-0">Silakan cari dan tambahkan negara dari kolom di atas untuk mulai memantau.</p>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    let localDBCountries = []; 
    let mledozeData = [];
    
    // Inisialisasi Array Watchlist dari LocalStorage
    let myWatchlist = JSON.parse(localStorage.getItem('supplyChainWatchlist')) || [];

    // 1. TARIK DATA AWAL
    async function fetchAllData() {
        try {
            const dbRes = await fetch('/api/countries-data');
            localDBCountries = await dbRes.json();

            const mlRes = await fetch('https://raw.githubusercontent.com/mledoze/countries/master/countries.json');
            mledozeData = await mlRes.json();
            
            // Urutkan Abjad
            localDBCountries.sort((a, b) => a.name.localeCompare(b.name));
            
            populateDropdown();
            renderWatchlist(); // Tampilkan yang sudah di-save
        } catch (error) {
            console.error("Gagal memuat data:", error);
        }
    }

    // 2. ISI DROPDOWN
    function populateDropdown() {
        let options = '<option value="">Cari & Tambah Negara...</option>';
        localDBCountries.forEach(country => {
            options += `<option value="${country.code}">${country.code} - ${country.name}</option>`;
        });
        
        $('#countrySelect').html(options);
        $('#countrySelect').select2({ placeholder: "Cari & Tambah Negara..." });
    }

    // 3. TAMBAH KE WATCHLIST
    function addToWatchlist() {
        const code = $('#countrySelect').val();
        if(!code) return alert('Silakan pilih negara terlebih dahulu!');
        
        if(myWatchlist.includes(code)) {
            return alert('Negara ini sudah ada di dalam Watchlist Anda.');
        }

        myWatchlist.push(code);
        saveAndRender();
        
        // Reset dropdown setelah ditambah
        $('#countrySelect').val('').trigger('change');
    }

    // 4. HAPUS DARI WATCHLIST
    function removeFromWatchlist(code) {
        myWatchlist = myWatchlist.filter(item => item !== code);
        saveAndRender();
    }

    // 5. SIMPAN KE LOCALSTORAGE & RENDER ULANG
    function saveAndRender() {
        localStorage.setItem('supplyChainWatchlist', JSON.stringify(myWatchlist));
        renderWatchlist();
    }

    // 6. RENDER KARTU WATCHLIST KE LAYAR
    function renderWatchlist() {
        const container = document.getElementById('watchlistContainer');
        const emptyState = document.getElementById('emptyState');
        
        container.innerHTML = '';

        if(myWatchlist.length === 0) {
            emptyState.style.display = 'block';
            return;
        }

        emptyState.style.display = 'none';

        myWatchlist.forEach(code => {
            const dbCountry = localDBCountries.find(c => c.code === code);
            const extraData = mledozeData.find(c => c.cca2 === code) || {};
            
            if(!dbCountry) return;

            const currency = dbCountry.currency || 'USD';
            const lat = extraData.latlng ? extraData.latlng[0] : 0;
            const lng = extraData.latlng ? extraData.latlng[1] : 0;

            // Template HTML untuk setiap kartu
            const cardHTML = `
                <div class="col-md-6 col-lg-4" id="card-${code}">
                    <div class="watch-card">
                        <button class="btn-remove" onclick="removeFromWatchlist('${code}')" title="Hapus dari Watchlist">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        
                        <div class="watch-title">${dbCountry.name}</div>
                        <div class="watch-iso">${code}</div>

                        <div class="mini-metrics">
                            <div class="mini-metric-item">
                                <div class="mini-metric-label"><i class="fas fa-chart-pie mini-icon text-primary"></i> GDP</div>
                                <div class="mini-metric-val" id="wl-gdp-${code}"><i class="fas fa-spinner fa-spin text-muted"></i></div>
                            </div>
                            <div class="mini-metric-item">
                                <div class="mini-metric-label"><i class="fas fa-arrow-trend-up mini-icon text-danger"></i> Inflasi</div>
                                <div class="mini-metric-val" id="wl-inf-${code}"><i class="fas fa-spinner fa-spin text-muted"></i></div>
                            </div>
                            <div class="mini-metric-item">
                                <div class="mini-metric-label"><i class="fas fa-money-bill-wave mini-icon text-success"></i> Kurs</div>
                                <div class="mini-metric-val" id="wl-cur-${code}"><i class="fas fa-spinner fa-spin text-muted"></i></div>
                            </div>
                            <div class="mini-metric-item">
                                <div class="mini-metric-label"><i class="fas fa-cloud-sun mini-icon text-warning"></i> Cuaca</div>
                                <div class="mini-metric-val" id="wl-wea-${code}"><i class="fas fa-spinner fa-spin text-muted"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', cardHTML);

            // Eksekusi penarikan data API untuk kartu ini
            fetchLiveCardData(code, lat, lng, currency);
        });
    }

    // 7. FETCH DATA LIVE UNTUK SETIAP KARTU (Sama seperti dashboard)
    async function fetchLiveCardData(code, lat, lng, currency) {
        // A. Cuaca (Open-Meteo)
        if(lat !== 0 && lng !== 0) {
            fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lng}&current_weather=true`)
                .then(res => res.json())
                .then(data => { document.getElementById(`wl-wea-${code}`).innerText = `${data.current_weather.temperature}°C`; })
                .catch(() => { document.getElementById(`wl-wea-${code}`).innerText = 'N/A'; });
        } else {
            document.getElementById(`wl-wea-${code}`).innerText = 'N/A';
        }

        // B. Kurs (Exchange Rate)
        fetch('https://api.exchangerate-api.com/v4/latest/USD')
            .then(res => res.json())
            .then(data => {
                const rate = data.rates[currency];
                document.getElementById(`wl-cur-${code}`).innerText = rate ? `${rate.toLocaleString('id-ID')} ${currency}` : 'N/A';
            }).catch(() => { document.getElementById(`wl-cur-${code}`).innerText = 'N/A'; });

        // C. World Bank (GDP & Inflasi)
        Promise.all([
            fetch(`https://api.worldbank.org/v2/country/${code}/indicator/NY.GDP.MKTP.CD?format=json&per_page=5`).then(r => r.json()),
            fetch(`https://api.worldbank.org/v2/country/${code}/indicator/FP.CPI.TOTL.ZG?format=json&per_page=5`).then(r => r.json())
        ]).then(([gdpRes, infRes]) => {
            
            // Ekstrak GDP
            let gdpVal = 'N/A';
            if (gdpRes && gdpRes[1] && Array.isArray(gdpRes[1])) {
                const validGdp = gdpRes[1].find(item => item.value !== null);
                if (validGdp) gdpVal = `$${(validGdp.value / 1000000000000).toFixed(2)} T`;
            }
            document.getElementById(`wl-gdp-${code}`).innerText = gdpVal;

            // Ekstrak Inflasi
            let infVal = 'N/A';
            if (infRes && infRes[1] && Array.isArray(infRes[1])) {
                const validInf = infRes[1].find(item => item.value !== null);
                if (validInf) infVal = `${validInf.value.toFixed(2)}%`;
            }
            document.getElementById(`wl-inf-${code}`).innerText = infVal;

        }).catch(() => {
            document.getElementById(`wl-gdp-${code}`).innerText = 'N/A';
            document.getElementById(`wl-inf-${code}`).innerText = 'N/A';
        });
    }

    document.addEventListener('DOMContentLoaded', fetchAllData);
</script>
@endpush