@extends('layouts.app')

@section('page_title', 'Favorite Watchlist')

@section('content')
<!-- CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    /* 1. SEARCH BAR CONSISTENCY (Sama dengan menu Countries) */
    .search-card {
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 25px;
    }

    /* 2. SELECT2 CONSISTENCY */
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

    /* 3. WATCHLIST CARD CONSISTENCY */
    .watch-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        padding: 20px;
        height: 100%;
        position: relative;
        transition: transform 0.2s;
    }
    .watch-card:hover { transform: translateY(-3px); }

    .watch-title { font-size: 18px; font-weight: 800; color: #1e293b; margin-bottom: 2px; padding-right: 40px; }
    .watch-iso { font-size: 13px; color: #64748b; font-weight: 700; margin-bottom: 15px; }

    /* Tombol Hapus */
    .btn-remove {
        position: absolute; top: 15px; right: 15px;
        background: #fee2e2; color: #ef4444; border: none;
        width: 35px; height: 35px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        transition: 0.2s;
    }
    .btn-remove:hover { background: #ef4444; color: #fff; }

    /* Metrik mini dalam kartu */
    .mini-metrics { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .metric-item { background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid #f1f5f9; }
    .metric-label { font-size: 9px; color: #64748b; text-transform: uppercase; font-weight: 700; margin-bottom: 3px; }
    .metric-val { font-size: 13px; color: #1e293b; font-weight: 800; }
    
    /* Pesan Kosong */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #ffffff;
        border-radius: 16px;
        border: 2px dashed #cbd5e1;
        color: #64748b;
    }
    .empty-state i { font-size: 48px; color: #cbd5e1; margin-bottom: 15px; }
</style>

<!-- HEADER & SEARCH -->
<div class="search-card">
    <label class="form-label text-muted fw-bold text-uppercase mb-3" style="font-size: 13px; letter-spacing: 1px;">
        <i class="fas fa-bookmark me-2 text-primary"></i> Watchlist Monitoring
    </label>
    <div style="display: flex; gap: 10px; max-width: 600px;">
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
    
    // Inisialisasi Array Watchlist (Kosong di awal, akan diisi dari Database)
    let myWatchlist = [];
    
    // Token CSRF untuk keamanan request POST Laravel
    const csrfToken = '{{ csrf_token() }}';

    // 1. TARIK DATA AWAL
    async function fetchAllData() {
        try {
            // A. Tarik database lokal
            const dbRes = await fetch('/api/countries-data');
            localDBCountries = await dbRes.json();

            // B. Tarik data Mledoze (koordinat cuaca)
            const mlRes = await fetch('https://raw.githubusercontent.com/mledoze/countries/master/countries.json');
            mledozeData = await mlRes.json();
            
            // C. Tarik Watchlist dari Tabel Database (Backend API)
            const wlRes = await fetch('/api/watchlist');
            myWatchlist = await wlRes.json(); 
            
            // Urutkan Abjad
            localDBCountries.sort((a, b) => a.name.localeCompare(b.name));
            
            populateDropdown();
            renderWatchlist(); 
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

    // 3. TAMBAH KE WATCHLIST (DATABASE)
    async function addToWatchlist() {
        const code = $('#countrySelect').val();
        if(!code) return alert('Silakan pilih negara terlebih dahulu!');
        
        if(myWatchlist.includes(code)) {
            return alert('Negara ini sudah ada di dalam Watchlist Anda.');
        }

        // A. Langsung tampilkan di UI (Optimistic Update)
        myWatchlist.push(code);
        renderWatchlist();
        $('#countrySelect').val('').trigger('change'); // Reset dropdown
        
        // B. Kirim data ke Database via API
        try {
            await fetch('/api/watchlist/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ code: code })
            });
        } catch (error) {
            console.error("Gagal menyimpan ke database", error);
        }
    }

    // 4. HAPUS DARI WATCHLIST (DATABASE)
    async function removeFromWatchlist(code) {
        // A. Langsung hapus dari UI
        myWatchlist = myWatchlist.filter(item => item !== code);
        renderWatchlist();

        // B. Hapus dari Database via API
        try {
            await fetch('/api/watchlist/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ code: code })
            });
        } catch (error) {
            console.error("Gagal menghapus dari database", error);
        }
    }

    // 5. RENDER KARTU WATCHLIST KE LAYAR
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

            // Template HTML Kartu
            const cardHTML = `
                <div class="col-md-6 col-lg-4" id="card-${code}">
                    <div class="watch-card">
                        <button class="btn-remove" onclick="removeFromWatchlist('${code}')" title="Hapus dari Watchlist">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        
                        <div class="watch-title">${dbCountry.name}</div>
                        <div class="watch-iso">${code}</div>

                        <div class="mini-metrics">
                            <div class="metric-item">
                                <div class="metric-label"><i class="fas fa-chart-pie me-1 text-primary"></i> GDP</div>
                                <div class="metric-val" id="wl-gdp-${code}"><i class="fas fa-spinner fa-spin text-muted"></i></div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-label"><i class="fas fa-arrow-trend-up me-1 text-danger"></i> Inflasi</div>
                                <div class="metric-val" id="wl-inf-${code}"><i class="fas fa-spinner fa-spin text-muted"></i></div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-label"><i class="fas fa-money-bill-wave me-1 text-success"></i> Kurs</div>
                                <div class="metric-val" id="wl-cur-${code}"><i class="fas fa-spinner fa-spin text-muted"></i></div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-label"><i class="fas fa-cloud-sun me-1 text-warning"></i> Cuaca</div>
                                <div class="metric-val" id="wl-wea-${code}"><i class="fas fa-spinner fa-spin text-muted"></i></div>
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

    // 6. FETCH DATA LIVE UNTUK SETIAP KARTU
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