@extends('layouts.app')

@section('page_title', 'Weather Monitoring')

@section('content')
<!-- CSS Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    /* BUNGKUS PETA */
    .map-wrapper {
        width: 100%;
        height: calc(100vh - 130px); 
        min-height: 550px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        position: relative;
        z-index: 1; 
    }

    #map {
        width: 100%;
        height: 100%;
        background: #f8fafc;
    }

     /* CSS UNTUK SEARCH DI LUAR PETA */
    .search-section {
        position: relative;
        z-index: 1050; 
    }
    .search-results-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background: white;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        max-height: 250px;
        overflow-y: auto;
        margin-top: 5px;
        border: 1px solid #e2e8f0;
        padding-left: 0;
        z-index: 9999 !important; /* Paksa dropdown selalu di atas peta */
    }

    .search-item {
        list-style: none;
        padding: 10px 15px;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
        border-bottom: 1px solid #f1f5f9;
    }
    .search-item:hover { 
        background-color: #f8fafc; 
        color: #0f172a; 
    }

    /* LEGENDA WARNA CUACA (Melayang di kanan bawah) */
    .map-legend {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(5px);
        padding: 12px 18px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        z-index: 1000;
        border: 1px solid #e2e8f0;
        font-size: 12px;
        font-weight: 700;
    }
    .legend-line { display: flex; align-items: center; margin-bottom: 5px; color: #475569; }
    .legend-dot { width: 12px; height: 12px; border-radius: 50%; margin-right: 8px; }

    /* TITIK RADAR DI PETA */
    .radar-dot {
        color: #ffffff;
        border-radius: 50%;
        border: 2px solid #ffffff;
        text-align: center;
        font-size: 10px;
        font-weight: 800;
        box-shadow: 0 3px 8px rgba(0,0,0,0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s;
    }
    .radar-dot:hover { transform: scale(1.3); z-index: 9999 !important; }

    /* WARNA STATUS PERBEDAAN CUACA MAKSIMAL */
    .w-normal { background-color: #10b981; } /* Hijau = Cerah Berawan */
    .w-rain { background-color: #3b82f6; }   /* Biru = Hujan */
    .w-wind { background-color: #f59e0b; }   /* Kuning/Oranye = Angin Kencang */
    .w-storm { background-color: #ef4444; }  /* Merah = Badai */

    /* EFEK KEDAP-KEDIP (BLINKING ANIMATION) JIKA BAHAYA */
    .radar-blink {
        animation: danger-flash 0.8s infinite alternate;
    }
    @keyframes danger-flash {
        0% { opacity: 1; box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.8); }
        100% { opacity: 0.2; box-shadow: 0 0 15px 6px rgba(239, 68, 68, 0.2); }
    }

    /* Styling Isi Pop-Up */
    .popup-content { text-align: center; min-width: 160px; }
    .popup-title { margin: 0 0 6px 0; font-weight: 800; color: #0f172a; font-size: 14px; }
    .popup-badge { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; display: inline-block; margin-bottom: 8px; }
    .popup-stats { display: flex; justify-content: space-between; font-size: 12px; font-weight: 600; color: #475569; border-top: 1px solid #e2e8f0; padding-top: 6px; }
</style>

<div class="card mb-4" style="border-radius: 16px; background-color: #ffffff; border: 1px solid #cbd5e1; box-shadow: 0 4px 10px rgba(0,0,0,0.08);">
    <div class="card-body p-4 position-relative">
        <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 13px; letter-spacing: 1px;">
            <i class="fas fa-search-location me-2 text-primary"></i> Cari Lokasi Cuaca
        </label>
        
        <div class="input-group" style="max-width: 500px;">
            <span class="input-group-text bg-white border-end-0 border-1 text-muted">
                <i class="fas fa-search"></i>
            </span>
            <!-- ID dan JS event disamakan dengan sistem aslimu -->
            <input type="text" id="mapSearchInput" class="form-control form-control-lg fw-bold text-dark border-start-0 border-1 ps-0 shadow-none" style="font-size: 16px;" placeholder="Ketik nama kota atau negara..." oninput="triggerSearchFilter()" onclick="showAllLocations()">
        </div>
        
        <!-- Hasil Dropdown Pencarian (Tetap Dipertahankan) -->
        <ul id="searchResults" class="search-results-dropdown d-none shadow" style="max-width: 500px; margin-top: 5px; position: absolute; z-index: 1000; background: white; border-radius: 8px; list-style: none; padding: 0;"></ul>
    </div>
</div>

        <div class="map-wrapper">
            <!-- 2. AREA PETA LEAFLET -->
            <div id="map"></div>

            <!-- 3. KOTAK LEGENDA CUACA -->
            <div class="map-legend">
                <div class="legend-line"><div class="legend-dot w-normal"></div> Cerah / Normal</div>
                <div class="legend-line"><div class="legend-dot w-rain"></div> Hujan</div>
                <div class="legend-line"><div class="legend-dot w-wind"></div> Angin Kencang</div>
                <div class="legend-line"><div class="legend-dot w-storm" style="animation: danger-flash 0.8s infinite alternate;"></div> Badai (Bahaya)</div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Inisialisasi Peta
    const map = L.map('map', { zoomControl: false }).setView([-0.7893, 113.9213], 5);
    L.control.zoom({ position: 'topleft' }).addTo(map);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; CARTO',
        maxZoom: 20
    }).addTo(map);

    let allLocations = [];
    let markerLayerGroup = {}; // Object untuk memegang referensi marker agar bisa dibuka lewat search

    // BANK DATA KOTA-KOTA UTAMA DI SELURUH DUNIA + SELURUH NEGARA DB LOKAL
    async function initWeatherRadar() {
        try {
            const dbRes = await fetch('/api/countries-data');
            const localDB = await dbRes.json();
            const mlRes = await fetch('https://raw.githubusercontent.com/mledoze/countries/master/countries.json');
            const mledoze = await mlRes.json();
            
            // Masukkan Data Negara
            localDB.forEach(dbC => {
                const mlC = mledoze.find(c => c.cca2 === dbC.code);
                if(mlC && mlC.latlng) {
                    allLocations.push({
                        type: 'Negara',
                        name: dbC.name,
                        lat: mlC.latlng[0],
                        lng: mlC.latlng[1]
                    });
                }
            });

            // DATA KOTA STRATEGIS GLOBAL (Supaya Peta Penuh & Ramai Titik Kota)
            const globalCities = [
                // Jagoan Utama
                { type: 'Kota', name: 'Lhokseumawe - Aceh', lat: 5.1801, lng: 97.1406 },
                { type: 'Kota', name: 'Banda Aceh - Indonesia', lat: 5.5483, lng: 95.3238 },
                { type: 'Kota', name: 'Medan - Indonesia', lat: 3.5952, lng: 98.6722 },
                { type: 'Kota', name: 'Jakarta - Indonesia', lat: -6.2088, lng: 106.8456 },
                { type: 'Kota', name: 'Surabaya - Indonesia', lat: -7.2504, lng: 112.7688 },
                { type: 'Kota', name: 'Makassar - Indonesia', lat: -5.1476, lng: 119.4327 },
                // Asia
                { type: 'Kota', name: 'Kuala Lumpur - Malaysia', lat: 3.1390, lng: 101.6869 },
                { type: 'Kota', name: 'Singapore City - Singapore', lat: 1.3521, lng: 103.8198 },
                { type: 'Kota', name: 'Bangkok - Thailand', lat: 13.7563, lng: 100.5018 },
                { type: 'Kota', name: 'Manila - Philippines', lat: 14.5995, lng: 120.9842 },
                { type: 'Kota', name: 'Tokyo - Japan', lat: 35.6762, lng: 139.6503 },
                { type: 'Kota', name: 'Osaka - Japan', lat: 34.6937, lng: 135.5023 },
                { type: 'Kota', name: 'Seoul - South Korea', lat: 37.5665, lng: 126.9780 },
                { type: 'Kota', name: 'Beijing - China', lat: 39.9042, lng: 116.4074 },
                { type: 'Kota', name: 'Shanghai - China', lat: 31.2304, lng: 121.4737 },
                { type: 'Kota', name: 'Hong Kong - HK', lat: 22.3193, lng: 114.1694 },
                { type: 'Kota', name: 'Mumbai - India', lat: 19.0760, lng: 72.8777 },
                { type: 'Kota', name: 'New Delhi - India', lat: 28.6139, lng: 77.2090 },
                // Australia
                { type: 'Kota', name: 'Sydney - Australia', lat: -33.8688, lng: 151.2093 },
                { type: 'Kota', name: 'Melbourne - Australia', lat: -37.8136, lng: 144.9631 },
                // Eropa
                { type: 'Kota', name: 'London - United Kingdom', lat: 51.5074, lng: -0.1278 },
                { type: 'Kota', name: 'Paris - France', lat: 48.8566, lng: 2.3522 },
                { type: 'Kota', name: 'Berlin - Germany', lat: 52.5200, lng: 13.4050 },
                { type: 'Kota', name: 'Frankfurt - Germany', lat: 50.1109, lng: 8.6821 },
                { type: 'Kota', name: 'Rotterdam - Netherlands', lat: 51.9244, lng: 4.4777 },
                { type: 'Kota', name: 'Moscow - Russia', lat: 55.7558, lng: 37.6173 },
                // Amerika
                { type: 'Kota', name: 'New York - USA', lat: 40.7128, lng: -74.0060 },
                { type: 'Kota', name: 'Los Angeles - USA', lat: 34.0522, lng: -118.2437 },
                { type: 'Kota', name: 'Chicago - USA', lat: 41.8781, lng: -87.6298 },
                { type: 'Kota', name: 'Sao Paulo - Brazil', lat: -23.5505, lng: -46.6333 },
                // Timur Tengah & Afrika
                { type: 'Kota', name: 'Dubai - UAE', lat: 25.2048, lng: 55.2708 },
                { type: 'Kota', name: 'Cairo - Egypt', lat: 30.0444, lng: 31.2357 },
                { type: 'Kota', name: 'Cape Town - South Africa', lat: -33.9249, lng: 18.4241 }
            ];

            allLocations = [...allLocations, ...globalCities];
            scanWeatherInChunks(); 
        } catch (error) { console.error(error); }
    }

    // 3. AMBIL DATA CUACA MASAL DENGAN METODE CHUNKING (ANTI LOG-OUT/LAG)
    async function scanWeatherInChunks() {
        const size = 40; 
        for (let i = 0; i < allLocations.length; i += size) {
            const chunk = allLocations.slice(i, i + size);
            const lats = chunk.map(c => c.lat.toFixed(2)).join(',');
            const lngs = chunk.map(c => c.lng.toFixed(2)).join(',');
            
            try {
                const res = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lats}&longitude=${lngs}&current=temperature_2m,weather_code,wind_speed_10m`);
                const dataArr = await res.json();
                const wData = Array.isArray(dataArr) ? dataArr : [dataArr];

                wData.forEach((data, idx) => {
                    if(data.current) createRadarDot(chunk[idx], data.current);
                });
            } catch (e) { console.log(e); }
        }
    }

    // 4. LOGIKA WARNA KONTRAST & EFEK KEDAP-KEDIP PADA BAHAYA
    function createRadarDot(loc, current) {
        const temp = Math.round(current.temperature_2m);
        const code = current.weather_code;
        const wind = current.wind_speed_10m;
        
        let status = "Cerah / Normal";
        let colorClass = "w-normal";
        let isDanger = false;
        let textBadge = "#10b981", iconBadge = "fa-circle-check";

        if ((code >= 51 && code <= 67) || (code >= 80 && code <= 82)) {
            status = "Hujan"; colorClass = "w-rain";
            textBadge = "#3b82f6"; iconBadge = "fa-cloud-rain";
        }
        if (wind > 40) { 
            status = "Angin Kencang"; colorClass = "w-wind";
            textBadge = "#ea580c"; iconBadge = "fa-wind";
        }
        if (code >= 95 && code <= 99) { 
            status = "Badai Petir"; colorClass = "w-storm radar-blink";
            isDanger = true;
            textBadge = "#ef4444"; iconBadge = "fa-triangle-exclamation fa-fade";
        }

        const icon = L.divIcon({
            html: `<div class="radar-dot ${colorClass} ${isDanger ? 'radar-blink' : ''}" style="width:24px; height:24px;">${temp}°</div>`,
            className: '',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });

        // HTML POP-UP 
        const marker = L.marker([loc.lat, loc.lng], {icon: icon}).addTo(map)
            .bindPopup(`
                <div style="text-align: center; font-family: inherit; min-width: 170px;">
                    <!-- Baris 1: Kota - Negara -->
                    <div style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; font-weight: 900; color: #0f172a; font-size: 14px;">
                        ${loc.name}
                    </div>
                    
                    <!-- Baris 2: Status Cuaca -->
                    <div style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; font-size: 13px; font-weight: 800; color: ${textBadge};">
                        <i class="fas ${iconBadge} me-1"></i> Status: ${status}
                    </div>

                    <!-- Baris 3: Suhu -->
                    <div style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; font-size: 13px; font-weight: 700; color: #334155;">
                        <i class="fas fa-temperature-half" style="color: #0ea5e9; width: 16px;"></i> Suhu: ${temp}°C
                    </div>

                    <!-- Baris 4: Kecepatan Angin -->
                    <div style="padding: 8px 0; font-size: 13px; font-weight: 700; color: #334155;">
                        <i class="fas fa-wind" style="color: #f59e0b; width: 16px;"></i> Angin: ${wind} km/h
                    </div>
                </div>
            `);

        markerLayerGroup[loc.name.toLowerCase()] = marker;
    }

    // 5. SISTEM ENGINE PENCARIAN PETA (SEARCH CONTROLLER)
    // 5. SISTEM ENGINE PENCARIAN & DROPDOWN NEGARA
    // Fungsi bantuan untuk mencetak list ke layar
    function renderDropdown(list) {
        const dropdown = document.getElementById('searchResults');
        dropdown.innerHTML = '';

        if(list.length > 0) {
            dropdown.classList.remove('d-none');
            list.forEach(loc => {
                const li = document.createElement('li');
                li.className = 'search-item';
                li.innerHTML = `<i class="fas ${loc.type === 'Kota' ? 'fa-city text-secondary' : 'fa-flag text-primary'} me-2"></i> ${loc.name}`;
                li.onclick = () => focusLocation(loc);
                dropdown.appendChild(li);
            });
        } else {
            dropdown.classList.add('d-none');
        }
    }

    // Fungsi untuk memunculkan SEMUA list saat kotak diklik (belum ngetik)
    function showAllLocations() {
        renderDropdown(allLocations);
    }

    // Fungsi memfilter list berdasarkan ketikan user
    function triggerSearchFilter() {
        const input = document.getElementById('mapSearchInput').value.toLowerCase();
        
        // Kalau kolom input dihapus sampai kosong, kembali tampilkan semua daftar
        if(input.length < 1) {
            showAllLocations();
            return;
        }

        // Cari data yang cocok dengan ketikan
        const matches = allLocations.filter(l => l.name && l.name.toLowerCase().includes(input));
        renderDropdown(matches);
    }

    // 6. FUNGSI UNTUK TERBANG DAN BUKA POPUP LOKASI TARGET
    function focusLocation(loc) {
        // Sembunyikan dropdown & bersihkan form input
        document.getElementById('searchResults').classList.add('d-none');
        document.getElementById('mapSearchInput').value = '';

        // Terbangkan kamera ke koordinat target (Zoom Level 7 biar dekat dan fokus)
        map.flyTo([loc.lat, loc.lng], 7, { animate: true, duration: 1.5 });

        // Trigger buka Pop-up marker secara otomatis jika sudah selesai loading
        setTimeout(() => {
            const marker = markerLayerGroup[loc.name.toLowerCase()];
            if(marker) marker.openPopup();
        }, 1600);
    }

    /// FUNGSI UNTUK MENUTUP DROPDOWN SAAT KLIK DI LUAR AREA
    document.addEventListener('click', function(event) {
        const searchInput = document.getElementById('mapSearchInput');
        const searchResults = document.getElementById('searchResults');
        
        // Pastikan elemennya ada di halaman
        if (searchInput && searchResults) {
            // Cek apakah klik terjadi di LUAR kotak input dan LUAR area dropdown
            if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
                // Sembunyikan dropdown dengan menambahkan class d-none
                searchResults.classList.add('d-none');
            }
        }
    });

    document.addEventListener('DOMContentLoaded', initWeatherRadar);
</script>
@endpush