@extends('layouts.app')

@section('page_title', 'Global Country Dashboard')

@section('content')
<!-- CSS Leaflet saja (TomSelect sudah dihapus) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    /* Styling Peta */
    #map { 
        height: calc(100vh - 300px); 
        min-height: 500px;
        border-radius: 16px; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.05); 
        z-index: 1; 
        border: 1px solid #e2e8f0;
    }
    
    /* Styling 5 Kotak Metrik Horizontal */
    .metric-box { 
        background: #ffffff; 
        border-radius: 12px; 
        padding: 15px; 
        box-shadow: 0 2px 10px rgba(0,0,0,0.02); 
        border: 1px solid #f1f5f9;
        display: flex; 
        align-items: center; 
        height: 100%;
        transition: transform 0.2s;
    }
    .metric-box:hover { transform: translateY(-3px); border-color: #e2e8f0; }
    
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
</style>

<!-- DROPDOWN NEGARA (Native Browser, Lebar 450px) -->
<div class="mb-4" style="width: 420px; max-width: 100%;">
    <label class="form-label text-muted fw-bold" style="font-size: 12px;">
        <i class="fas fa-globe-americas me-1"></i> PILIH NEGARA
    </label>
    <!-- Kita pakai form-select bawaan Bootstrap agar tampilannya rapi -->
    <select id="countrySelect" class="form-select form-select-lg shadow-sm fw-bold text-dark border-0" style="font-size: 15px;" onchange="changeCountry()">
        <option value="">Memuat database...</option>
    </select>
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

<!-- PETA BAWAH -->
<div class="row">
    <div class="col-12">
        <div id="map"></div>
    </div>
</div>
@endsection

@push('scripts')
<!-- JS Leaflet saja -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const map = L.map('map').setView([-0.7893, 113.9213], 5);
    let currentMarker = null;
    
    let localDBCountries = []; 
    let mledozeData = [];      

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);

    // 1. TARIK DATA DARI DATABASE (Nama, Kode, Currency) & GITHUB MLEDOZE (Lat, Lng)
    async function fetchAllData() {
        try {
            const dbRes = await fetch('/api/countries-data');
            localDBCountries = await dbRes.json();

            const mlRes = await fetch('https://raw.githubusercontent.com/mledoze/countries/master/countries.json');
            mledozeData = await mlRes.json();
            
            populateDropdown();
        } catch (error) {
            console.error("Gagal menarik data awal:", error);
            document.getElementById('countrySelect').innerHTML = '<option>Gagal Terhubung</option>';
        }
    }

    function populateDropdown() {
        const select = document.getElementById('countrySelect');
        select.innerHTML = ''; 
        
        localDBCountries.forEach(country => {
            const option = document.createElement('option');
            option.value = country.code; 
            option.text = country.name;
            if(country.code === 'ID') option.selected = true; 
            select.appendChild(option);
        });
        
        changeCountry(); 
    }

    // 2. HELPER WORLD BANK API (Diperbarui agar Anti-N/A)
    async function fetchWorldBank(countryCode, indicator) {
        try {
            // Hapus mrnev=1. Ganti dengan per_page=5 (Ambil 5 tahun terakhir untuk cari yang aman)
            const response = await fetch(`https://api.worldbank.org/v2/country/${countryCode}/indicator/${indicator}?format=json&per_page=5`);
            const data = await response.json();
            
            // Looping data 5 tahun terakhir, cari value pertama yang TIDAK null
            if (data && data[1] && Array.isArray(data[1])) {
                const validData = data[1].find(item => item.value !== null);
                if (validData) {
                    return validData.value;
                }
            }
            return null; // Kembalikan null jika kelima tahun terakhir kosong semua
        } catch (error) {
            console.error(`WB API Error (${indicator}):`, error);
            return null;
        }
    }

    // 3. FUNGSI UPDATE CARD SAAT NEGARA DIPILIH
    async function changeCountry() {
        const selectedCode = document.getElementById('countrySelect').value;
        
        const dbCountry = localDBCountries.find(c => c.code === selectedCode);
        const extraData = mledozeData.find(c => c.cca2 === selectedCode) || {};
        
        if(!dbCountry) return;

        // Beri efek loading
        ['gdp', 'inf', 'pop', 'cur', 'wea'].forEach(id => document.getElementById(`val-${id}`).innerText = 'Memuat...');

        // A. MATA UANG (Dari Database)
        const currency = dbCountry.currency ? dbCountry.currency : 'N/A';
        document.getElementById('val-cur').innerText = currency;
        
        // B. KOORDINAT PETA (Dari Mledoze)
        const lat = extraData.latlng ? extraData.latlng[0] : 0;
        const lng = extraData.latlng ? extraData.latlng[1] : 0;
        const capital = (extraData.capital && extraData.capital.length > 0) ? extraData.capital[0] : dbCountry.name;

        if (lat !== 0 && lng !== 0) {
            map.flyTo([lat, lng], 5, { animate: true, duration: 1.5 });
            if(currentMarker) map.removeLayer(currentMarker);
            currentMarker = L.marker([lat, lng]).addTo(map)
                .bindPopup(`<div style="text-align:center;"><b>${dbCountry.name}</b><br>Capital: ${capital}</div>`)
                .openPopup();
        }

        // C. API CUACA OPEN-METEO
        fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lng}&current_weather=true`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('val-wea').innerText = `${data.current_weather.temperature}°C`;
            }).catch(() => {
                document.getElementById('val-wea').innerText = `N/A`;
            });

        // D. API WORLD BANK (GDP, INFLASI, POPULASI) - Berjalan Paralel
        Promise.all([
            fetchWorldBank(selectedCode, 'SP.POP.TOTL'),      // Populasi Total
            fetchWorldBank(selectedCode, 'NY.GDP.MKTP.CD'),   // GDP Current US$
            fetchWorldBank(selectedCode, 'FP.CPI.TOTL.ZG')    // Inflasi Tahunan
        ]).then(([popData, gdpData, infData]) => {
            
            // LOGIKA POPULASI (Diperketat)
            if (popData !== null && popData !== undefined) {
                document.getElementById('val-pop').innerText = (popData / 1000000).toFixed(1) + ' Juta Jiwa';
            } else {
                document.getElementById('val-pop').innerText = 'Data N/A';
            }

            // LOGIKA GDP
            if (gdpData !== null && gdpData !== undefined) {
                document.getElementById('val-gdp').innerText = `$${(gdpData / 1000000000000).toFixed(2)} T`;
            } else {
                document.getElementById('val-gdp').innerText = 'Data N/A';
            }

            // LOGIKA INFLASI
            if (infData !== null && infData !== undefined) {
                document.getElementById('val-inf').innerText = `${infData.toFixed(2)}%`;
            } else {
                document.getElementById('val-inf').innerText = 'Data N/A';
            }
            
        });
    }

    document.addEventListener('DOMContentLoaded', fetchAllData);
</script>
@endpush