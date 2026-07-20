@extends('layouts.app')

@section('page_title', 'Country Comparison')

@section('content')
<!-- TAMBAHKAN CSS SELECT2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    /* Card Pencarian */
    .search-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border-radius: 12px;
        padding: 20px;
    }

    /* Card Utama Perbandingan */
    .compare-container {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        border-radius: 16px;
        padding: 30px;
    }

    /* Header Negara */
    .country-title-area {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }
    .iso-tag {
        background: #f1f5f9;
        color: #3b82f6;
        font-weight: 800;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 14px;
        letter-spacing: 1px;
    }
    .country-name {
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    /* Kotak Metrik */
    .metrics-wrapper {
        display: grid;
        grid-template-columns: repeat(4, 1fr); 
        gap: 15px;
    }
    @media (max-width: 992px) {
        .metrics-wrapper { grid-template-columns: repeat(2, 1fr); }
    }
    .metric-card {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 10px;
        padding: 20px 15px;
        text-align: center;
        transition: background 0.2s;
    }
    .metric-card:hover {
        background: #ffffff;
        border-color: #e2e8f0;
    }
    .metric-icon {
        font-size: 22px;
        color: #64748b;
        margin-bottom: 12px;
    }
    .metric-title {
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 700;
        color: #64748b;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .metric-data {
        font-size: 17px;
        font-weight: 800;
        color: #1e293b;
    }

    /* Divider Elegan */
    .compare-divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 30px 0;
    }
    .compare-divider::before, .compare-divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px dashed #cbd5e1;
    }
    .compare-divider i {
        margin: 0 20px;
        background: #f8fafc;
        color: #94a3b8;
        padding: 12px;
        border-radius: 50%;
        border: 1px solid #e2e8f0;
        font-size: 16px;
    }

    /* CUSTOM STYLING UNTUK SELECT2 BIAR RAPI */
    .select2-container .select2-selection--single {
        height: 42px !important;
        border-radius: 8px !important;
        border: 2px solid #e2e8f0 !important;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        font-weight: 700;
        color: #1e293b;
    }
    .select2-search__field {
        border-radius: 6px !important;
    }
</style>

<!-- KONTROL PEMILIHAN NEGARA -->
<div class="search-card mb-4">
    <label class="form-label text-muted fw-bold text-uppercase mb-3" style="font-size: 12px; letter-spacing: 1px;">
        <i class="fas fa-sliders me-2 text-primary"></i> Analisis Perbandingan Negara
    </label>
    
    <div class="row align-items-center">
        <!-- Hapus onchange bawaan HTML karena akan dihandle oleh jQuery Select2 -->
        <div class="col-md-5">
            <select id="countryA" class="form-select text-dark">
                <option value="">Memuat data...</option>
            </select>
        </div>
        <div class="col-md-2 text-center py-2 py-md-0">
            <span class="text-muted fw-bold" style="font-size: 12px; letter-spacing: 1px;"><i class="fa-solid fa-code-compare me-1"></i> BANDINGKAN</span>
        </div>
        <div class="col-md-5">
            <select id="countryB" class="form-select text-dark">
                <option value="">Memuat data...</option>
            </select>
        </div>
    </div>
</div>

<!-- HASIL PERBANDINGAN -->
<div class="compare-container">
    <!-- NEGARA A (ATAS) -->
    <div class="country-section">
        <div class="country-title-area">
            <div class="iso-tag" id="codeA">-</div>
            <h4 class="country-name" id="nameA">Memuat...</h4>
        </div>
        
        <div class="metrics-wrapper">
            <div class="metric-card">
                <i class="fa-solid fa-building-columns metric-icon"></i>
                <div class="metric-title">GDP (PDB)</div>
                <div class="metric-data" id="gdpA"><i class="fas fa-spinner fa-spin text-muted"></i></div>
            </div>
            <div class="metric-card">
                <i class="fa-solid fa-chart-line metric-icon"></i>
                <div class="metric-title">Inflation Rate</div>
                <div class="metric-data" id="inflationA"><i class="fas fa-spinner fa-spin text-muted"></i></div>
            </div>
            <div class="metric-card">
                <i class="fa-solid fa-money-bill-transfer metric-icon"></i>
                <div class="metric-title">Currency (vs USD)</div>
                <div class="metric-data" id="currencyA"><i class="fas fa-spinner fa-spin text-muted"></i></div>
            </div>
            <div class="metric-card">
                <i class="fa-solid fa-cloud-sun metric-icon"></i>
                <div class="metric-title">Current Weather</div>
                <div class="metric-data" id="weatherA"><i class="fas fa-spinner fa-spin text-muted"></i></div>
            </div>
        </div>
    </div>

    <!-- PEMISAH ELEGAN -->
    <div class="compare-divider">
        <i class="fa-solid fa-code-compare"></i>
    </div>

    <!-- NEGARA B (BAWAH) -->
    <div class="country-section">
        <div class="country-title-area">
            <div class="iso-tag bg-light text-secondary border" id="codeB">-</div>
            <h4 class="country-name" id="nameB">Memuat...</h4>
        </div>
        
        <div class="metrics-wrapper">
            <div class="metric-card">
                <i class="fa-solid fa-building-columns metric-icon"></i>
                <div class="metric-title">GDP (PDB)</div>
                <div class="metric-data" id="gdpB"><i class="fas fa-spinner fa-spin text-muted"></i></div>
            </div>
            <div class="metric-card">
                <i class="fa-solid fa-chart-line metric-icon"></i>
                <div class="metric-title">Inflation Rate</div>
                <div class="metric-data" id="inflationB"><i class="fas fa-spinner fa-spin text-muted"></i></div>
            </div>
            <div class="metric-card">
                <i class="fa-solid fa-money-bill-transfer metric-icon"></i>
                <div class="metric-title">Currency (vs USD)</div>
                <div class="metric-data" id="currencyB"><i class="fas fa-spinner fa-spin text-muted"></i></div>
            </div>
            <div class="metric-card">
                <i class="fa-solid fa-cloud-sun metric-icon"></i>
                <div class="metric-title">Current Weather</div>
                <div class="metric-data" id="weatherB"><i class="fas fa-spinner fa-spin text-muted"></i></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- TAMBAHKAN JQUERY & SELECT2 JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Tarik data dari Controller
    const rawCountriesData = @json($countries); 
    let dbCountries = {};
    let mledozeData = [];

    // 1. Inisialisasi Data
    async function initData() {
        try {
            const mlRes = await fetch('https://raw.githubusercontent.com/mledoze/countries/master/countries.json');
            mledozeData = await mlRes.json();

            let optionsHTML = '';
            
            // PENGURUTAN BERDASARKAN ABJAD (A-Z)
            rawCountriesData.sort((a, b) => a.name.localeCompare(b.name));

            rawCountriesData.forEach(c => {
                const code = c.code; 
                const extraData = mledozeData.find(m => m.cca2 === code || m.cca3 === code) || {};

                dbCountries[code] = {
                    name: c.name,
                    code: code,
                    currency: c.currency || 'USD',
                    lat: extraData.latlng ? extraData.latlng[0] : 0,
                    lon: extraData.latlng ? extraData.latlng[1] : 0
                };

                optionsHTML += `<option value="${code}">${code} - ${c.name}</option>`;
            });

            const selA = document.getElementById('countryA');
            const selB = document.getElementById('countryB');
            
            selA.innerHTML = optionsHTML;
            selB.innerHTML = optionsHTML;

            // SET DEFAULT KE INDONESIA DAN UNITED STATES
            if (dbCountries['ID']) selA.value = 'ID';
            else if (dbCountries['IDN']) selA.value = 'IDN'; // Fallback jika pakai ISO3

            if (dbCountries['US']) selB.value = 'US';
            else if (dbCountries['USA']) selB.value = 'USA'; // Fallback jika pakai ISO3

            // AKTIFKAN SELECT2 AGAR BISA SEARCH/KETIK
            $('#countryA').select2({ width: '100%', placeholder: "Cari Negara..." });
            $('#countryB').select2({ width: '100%', placeholder: "Cari Negara..." });

            // Trigger compareCountries saat dropdown dipilih
            $('#countryA, #countryB').on('change', function() {
                compareCountries();
            });

            // Jalankan perbandingan awal
            compareCountries();
        } catch (error) {
            console.error("Gagal memuat inisialisasi data:", error);
        }
    }

    // 2. Helper World Bank
    async function fetchWorldBank(countryCode, indicator) {
        try {
            const response = await fetch(`https://api.worldbank.org/v2/country/${countryCode}/indicator/${indicator}?format=json&per_page=5`);
            const data = await response.json();
            
            if (data && data[1] && Array.isArray(data[1])) {
                const validData = data[1].find(item => item.value !== null);
                if (validData) {
                    return validData.value;
                }
            }
            return null;
        } catch (error) {
            return null;
        }
    }

    // 3. Fungsi Eksekusi Perbandingan
    async function compareCountries() {
        // Ambil value dari Select2
        const codeA = $('#countryA').val();
        const codeB = $('#countryB').val();
        
        const cA = dbCountries[codeA];
        const cB = dbCountries[codeB];

        if(!cA || !cB) return;

        // Update Header UI
        document.getElementById('codeA').innerText = cA.code;
        document.getElementById('nameA').innerText = cA.name;
        document.getElementById('codeB').innerText = cB.code;
        document.getElementById('nameB').innerText = cB.name;

        // Tampilkan Loading
        const loadingHtml = '<i class="fas fa-spinner fa-spin text-muted fs-6"></i>';
        const metrics = ['gdp', 'inflation', 'currency', 'weather'];
        metrics.forEach(m => {
            document.getElementById(`${m}A`).innerHTML = loadingHtml;
            document.getElementById(`${m}B`).innerHTML = loadingHtml;
        });

        // A. Mata Uang (Exchange Rate)
        fetch('https://api.exchangerate-api.com/v4/latest/USD')
            .then(res => res.json())
            .then(data => {
                const rateA = data.rates[cA.currency];
                const rateB = data.rates[cB.currency];
                document.getElementById('currencyA').innerText = rateA ? `${rateA.toLocaleString('id-ID')} ${cA.currency}` : 'Data N/A';
                document.getElementById('currencyB').innerText = rateB ? `${rateB.toLocaleString('id-ID')} ${cB.currency}` : 'Data N/A';
            }).catch(() => {
                document.getElementById('currencyA').innerText = 'Data N/A';
                document.getElementById('currencyB').innerText = 'Data N/A';
            });

        // B. Cuaca (Open-Meteo)
        if(cA.lat !== 0 && cA.lon !== 0) {
            fetch(`https://api.open-meteo.com/v1/forecast?latitude=${cA.lat}&longitude=${cA.lon}&current_weather=true`)
                .then(res => res.json())
                .then(data => { document.getElementById('weatherA').innerHTML = `${data.current_weather.temperature}°C`; })
                .catch(() => { document.getElementById('weatherA').innerText = 'Data N/A'; });
        } else {
            document.getElementById('weatherA').innerText = 'Data N/A';
        }

        if(cB.lat !== 0 && cB.lon !== 0) {
            fetch(`https://api.open-meteo.com/v1/forecast?latitude=${cB.lat}&longitude=${cB.lon}&current_weather=true`)
                .then(res => res.json())
                .then(data => { document.getElementById('weatherB').innerHTML = `${data.current_weather.temperature}°C`; })
                .catch(() => { document.getElementById('weatherB').innerText = 'Data N/A'; });
        } else {
            document.getElementById('weatherB').innerText = 'Data N/A';
        }

        // C. GDP & Inflasi
        const [gdpA, infA, gdpB, infB] = await Promise.all([
            fetchWorldBank(codeA, 'NY.GDP.MKTP.CD'),
            fetchWorldBank(codeA, 'FP.CPI.TOTL.ZG'),
            fetchWorldBank(codeB, 'NY.GDP.MKTP.CD'),
            fetchWorldBank(codeB, 'FP.CPI.TOTL.ZG')
        ]);

        document.getElementById('gdpA').innerText = gdpA ? `$${(gdpA / 1000000000000).toFixed(2)} T` : 'Data N/A';
        document.getElementById('gdpB').innerText = gdpB ? `$${(gdpB / 1000000000000).toFixed(2)} T` : 'Data N/A';
        document.getElementById('inflationA').innerHTML = infA ? `${infA.toFixed(2)}%` : 'Data N/A';
        document.getElementById('inflationB').innerHTML = infB ? `${infB.toFixed(2)}%` : 'Data N/A';
    }

    document.addEventListener('DOMContentLoaded', initData);
</script>
@endpush