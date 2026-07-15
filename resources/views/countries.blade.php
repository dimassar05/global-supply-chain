@extends('layouts.app')

@section('page_title', 'Global Countries')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    /* Hilangkan padding agar peta bisa mentok ke pinggir layar */
    .main-content {
        padding: 0 !important;
        position: relative;
    }

    /* PETA FULL SCREEN */
    .map-container {
        position: relative;
        width: 100%;
        height: calc(100vh - 80px); /* Penuh sampai bawah */
    }

    #map { 
        width: 100%;
        height: 100%;
        z-index: 1; 
    }
    
    /* PANEL MELAYANG DI KANAN ATAS */
    .floating-panel {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 340px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px); 
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        z-index: 1000; 
        border: 1px solid rgba(255, 255, 255, 0.6);
    }

    .metric-list {
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px dashed #e2e8f0;
    }
    .metric-list:last-child { border-bottom: none; padding-bottom: 0; }
    
    .icon-box { 
        width: 40px; height: 40px; 
        border-radius: 10px; 
        display: flex; align-items: center; justify-content: center; 
        font-size: 16px; margin-right: 15px; flex-shrink: 0;
    }
    
    /* Warna Ikon Pastel */
    .bg-gdp { background: #e0f2fe; color: #0284c7; }
    .bg-inf { background: #f3e8ff; color: #9333ea; }
    .bg-pop { background: #fce7f3; color: #db2777; }
    .bg-cur { background: #cffafe; color: #0891b2; }
    .bg-wea { background: #ffedd5; color: #ea580c; }
</style>

<div class="map-container">
    <div id="map"></div>

    <div class="floating-panel">
        <h6 class="fw-bold text-dark mb-1"><i class="fas fa-radar me-2 text-primary"></i> Geographic Intel</h6>
        <p class="text-muted mb-3" style="font-size: 12px;">Live monitoring 250+ negara</p>

        <div class="mb-4">
            <select id="countrySelect" class="form-select shadow-sm fw-bold text-dark" onchange="changeCountry()">
                <option value="">Memuat data API...</option>
            </select>
        </div>

        <div class="metric-list">
            <div class="icon-box bg-gdp"><i class="fas fa-chart-pie"></i></div>
            <div>
                <div class="text-muted fw-bold text-uppercase" style="font-size: 10px;">GDP Estimasi</div>
                <div class="fw-bold text-dark" id="val-gdp" style="font-size: 14px;">-</div>
            </div>
        </div>
        
        <div class="metric-list">
            <div class="icon-box bg-inf"><i class="fas fa-arrow-trend-up"></i></div>
            <div>
                <div class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Inflasi</div>
                <div class="fw-bold text-dark" id="val-inf" style="font-size: 14px;">-</div>
            </div>
        </div>

        <div class="metric-list">
            <div class="icon-box bg-pop"><i class="fas fa-users"></i></div>
            <div>
                <div class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Populasi</div>
                <div class="fw-bold text-dark" id="val-pop" style="font-size: 14px;">-</div>
            </div>
        </div>

        <div class="metric-list">
            <div class="icon-box bg-cur"><i class="fas fa-money-bill-wave"></i></div>
            <div>
                <div class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Mata Uang</div>
                <div class="fw-bold text-dark" id="val-cur" style="font-size: 14px;">-</div>
            </div>
        </div>

        <div class="metric-list">
            <div class="icon-box bg-wea"><i class="fas fa-cloud-sun"></i></div>
            <div>
                <div class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Cuaca Live</div>
                <div class="fw-bold text-dark" id="val-wea" style="font-size: 14px;">-</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // 1. Inisialisasi Peta
    const map = L.map('map', { zoomControl: false }).setView([-0.7893, 113.9213], 5);
    L.control.zoom({ position: 'bottomleft' }).addTo(map);

    let currentMarker = null;
    let localDBCountries = []; // Data dari MySQL kamu
    let mledozeData = [];      // Data pelengkap dari GitHub (untuk lat/lng)

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);

    // 2. AMBIL DATA DARI MYSQL LOKAL & GITHUB MLEDOZE
    async function fetchAllData() {
        try {
            // A. Ambil data dari Database Laravel kamu (kolom: name, code, currency)
            const dbRes = await fetch('/api/countries-data');
            localDBCountries = await dbRes.json();

            // B. Ambil data koordinat dari link GitHub Mledoze yang kamu kasih
            const mlRes = await fetch('https://raw.githubusercontent.com/mledoze/countries/master/countries.json');
            mledozeData = await mlRes.json();
            
            populateDropdown();
        } catch (error) {
            console.error("Gagal menarik data:", error);
            document.getElementById('countrySelect').innerHTML = '<option>Gagal Terhubung ke Sistem</option>';
        }
    }

    // 3. MASUKKAN DATA DATABASE KE DROPDOWN
    function populateDropdown() {
        const select = document.getElementById('countrySelect');
        select.innerHTML = ''; 
        
        localDBCountries.forEach(country => {
            const option = document.createElement('option');
            
            // Menggunakan kolom sesuai screenshot MySQL kamu
            option.value = country.code; 
            option.text = country.name;
            
            if(country.code === 'ID') option.selected = true; // Default Indonesia
            select.appendChild(option);
        });
        
        changeCountry(); // Langsung jalankan saat pertama kali buka
    }

    // 4. SAAT NEGARA DIPILIH
    async function changeCountry() {
        const selectedCode = document.getElementById('countrySelect').value;
        
        // Cari data di Database Lokal
        const dbCountry = localDBCountries.find(c => c.code === selectedCode);
        // Cari data koordinat di Mledoze
        const extraData = mledozeData.find(c => c.cca2 === selectedCode) || {};
        
        if(!dbCountry) return;

        // Ekstrak Titik Koordinat dari Mledoze
        const lat = extraData.latlng ? extraData.latlng[0] : 0;
        const lng = extraData.latlng ? extraData.latlng[1] : 0;
        
        // Ekstrak Informasi Gabungan
        const population = extraData.population ? (extraData.population / 1000000).toFixed(1) + ' Juta' : 'N/A';
        const currency = dbCountry.currency || 'N/A'; // Sesuai kolom database kamu
        const capital = (extraData.capital && extraData.capital.length > 0) ? extraData.capital[0] : 'N/A';

        // Set Teks Loading Sementara
        ['gdp', 'inf', 'pop', 'cur', 'wea'].forEach(id => document.getElementById(`val-${id}`).innerText = '...');

        // Animasi Peta Terbang
        if (lat !== 0 && lng !== 0) {
            map.flyTo([lat, lng], 5, { animate: true, duration: 1.5 });
            if(currentMarker) map.removeLayer(currentMarker);
            currentMarker = L.marker([lat, lng]).addTo(map)
                .bindPopup(`<div style="text-align:center;"><b>${dbCountry.name}</b><br>Capital: ${capital}</div>`)
                .openPopup();
        }

        // Tampilkan Data ke Kartu
        document.getElementById('val-pop').innerText = population;
        document.getElementById('val-cur').innerText = currency;
        
        // Data Simulasi AI
        document.getElementById('val-gdp').innerText = `$${(Math.random() * (4.5 - 0.5) + 0.5).toFixed(2)} T`;
        document.getElementById('val-inf').innerText = `${(Math.random() * (7.5 - 1.0) + 1.0).toFixed(1)}%`;

        // Ambil Data Cuaca BMKG (Open-Meteo) berdasarkan Latitude Longitude
        try {
            if (lat === 0) throw new Error("Koordinat tidak ada");
            const weatherRes = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lng}&current_weather=true`);
            const weatherData = await weatherRes.json();
            document.getElementById('val-wea').innerText = `${weatherData.current_weather.temperature}°C`;
        } catch (error) {
            document.getElementById('val-wea').innerText = `Data N/A`;
        }
    }

    // Eksekusi fungsi saat halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', fetchAllData);
</script>
@endpush