@extends('layouts.app')

@section('page_title', 'Port Location')

@section('content')
<!-- Memanggil CSS Leaflet & Plugin MarkerCluster -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

<style>
    /* Desain Card Konsisten */
    .custom-card {
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        border-radius: 16px;
    }

    /* Container Peta */
    #map {
        height: 850px;
        border-radius: 12px;
        z-index: 1;
    }

    .form-select {
        font-weight: 500;
        color: #475569;
    }

    /* Desain Pop-up Marker Interaktif */
    .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .leaflet-popup-content { margin: 15px; line-height: 1.5; }
    .port-popup-title { font-weight: 800; color: #1e293b; font-size: 16px; margin-bottom: 5px; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; }
    .port-popup-info { font-size: 13px; color: #475569; margin-bottom: 3px; }
    
    /* Desain Badge Total */
    .total-badge {
        background-color: #0f172a;
        color: #ffffff;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 10px rgba(30, 41, 59, 0.2);
    }

    /* Customisasi Warna Cluster (Opsional, agar senada tema) */
    .marker-cluster-small { background-color: rgba(79, 70, 229, 0.6); }
    .marker-cluster-small div { background-color: rgba(79, 70, 229, 0.8); color: white; font-weight: bold; }
    .marker-cluster-medium { background-color: rgba(2, 132, 199, 0.6); }
    .marker-cluster-medium div { background-color: rgba(2, 132, 199, 0.8); color: white; font-weight: bold; }
    .marker-cluster-large { background-color: rgba(15, 23, 42, 0.6); }
    .marker-cluster-large div { background-color: rgba(15, 23, 42, 0.8); color: white; font-weight: bold; }
</style>

<!-- HEADER & FILTER PENCARIAN -->
<div class="custom-card mb-4 p-4">
    
    <!-- BARIS JUDUL DAN BADGE TOTAL -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <label class="form-label text-muted fw-bold text-uppercase mb-0" style="font-size: 13px; letter-spacing: 1px;">
            <i class="fas fa-magnifying-glass-location me-2 text-primary"></i> Pencarian Pelabuhan Global
        </label>
        
        <span class="badge rounded-pill px-3 py-2 total-badge" id="totalPortsBadge">
            <i class="fas fa-ship me-1"></i> Menghitung...
        </span>
    </div>
    
    <div class="row g-3 align-items-center">
        <!-- Dropdown Pilih Negara -->
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-flag"></i></span>
                <select id="searchCountry" class="form-select border-start-0" onchange="filterMap('country')">
                    <!-- Opsi diisi otomatis via JS -->
                </select>
            </div>
        </div>

        <!-- Dropdown Pilih Pelabuhan -->
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-anchor"></i></span>
                <select id="searchPort" class="form-select border-start-0" onchange="filterMap('port')">
                    <!-- Opsi diisi otomatis via JS -->
                </select>
            </div>
        </div>
    </div>
</div>

<!-- KONTENER PETA -->
<div class="custom-card p-2 mb-4">
    <div id="map"></div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Plugin MarkerCluster JS -->
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

<script>
    const map = L.map('map').setView([20.0, 0.0], 2);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap contributors', maxZoom: 18
    }).addTo(map);

    // MENGGUNAKAN MARKER CLUSTER GROUP (DENGAN SETTINGAN SKALA BENUA)
    let markerLayerGroup = L.markerClusterGroup({
        chunkedLoading: true,
        maxClusterRadius: 150, // Radius diperbesar drastis agar menyedot sebesar skala Benua
        disableClusteringAtZoom: 6, // Begitu di-zoom in (scroll maju), cluster langsung pecah semua biar detailnya kelihatan
        spiderfyOnMaxZoom: true // Mencegah marker bertumpuk mati
    }).addTo(map);
    
    const allPortsData = @json($ports);
    let markerReferences = {}; 

    // 1. Isi Dropdown Negara
    function populateCountries() {
        const countrySelect = document.getElementById('searchCountry');
        const uniqueCountries = [...new Set(allPortsData.map(p => p.country_name))].filter(Boolean).sort();
        
        countrySelect.innerHTML = '<option value="">Semua Negara...</option>';
        uniqueCountries.forEach(country => {
            countrySelect.innerHTML += `<option value="${country}">${country}</option>`;
        });
    }

    // 2. Isi Dropdown Pelabuhan
    function populatePorts(filterCountry = "") {
        const portSelect = document.getElementById('searchPort');
        portSelect.innerHTML = '<option value="">Semua Pelabuhan...</option>';

        let filteredData = allPortsData;
        if(filterCountry !== "") {
            filteredData = allPortsData.filter(p => (p.country_name || "").toLowerCase() === filterCountry.toLowerCase());
        }

        const sortedPorts = [...filteredData].sort((a, b) => (a.name || "").localeCompare(b.name || ""));
        sortedPorts.forEach(port => {
            if(port.name) {
                const label = `${port.name} - ${port.country_name}`;
                portSelect.innerHTML += `<option value="${port.name}">${label}</option>`;
            }
        });
    }

    // 3. Render Marker ke Peta & UPDATE TOTAL
    function renderMarkers(portsArray, autoPopupPortName = null) {
        markerLayerGroup.clearLayers();
        markerReferences = {}; 
        const markers = []; // Tampung marker di array dulu agar render lebih cepat

        // UPDATE BADGE TOTAL PELABUHAN
        const totalBadge = document.getElementById('totalPortsBadge');
        totalBadge.innerHTML = `<i class="fas fa-ship me-1"></i> ${portsArray.length} Pelabuhan`;

        portsArray.forEach(port => {
            if(!port.latitude || !port.longitude) return;

            const popupHTML = `
                <div class="port-popup-title"><i class="fas fa-anchor text-primary"></i> ${port.name}</div>
                <div class="port-popup-info"><strong><i class="fas fa-flag me-1"></i> Negara:</strong> ${port.country_name}</div>
                <div class="port-popup-info"><strong><i class="fas fa-location-dot me-1"></i> Kordinat:</strong> ${parseFloat(port.latitude).toFixed(4)}, ${parseFloat(port.longitude).toFixed(4)}</div>
            `;

            const marker = L.marker([port.latitude, port.longitude]);
            marker.bindPopup(popupHTML); 
            
            markers.push(marker); // Masukkan ke array sementara
            markerReferences[port.name.toLowerCase()] = marker;
        });

        if (markers.length > 0) {
            // Render semua marker ke dalam cluster sekaligus (jauh lebih ringan)
            markerLayerGroup.addLayers(markers);

            if (autoPopupPortName && markers.length === 1) {
                // Zoom agak dalam supaya cluster pecah menjadi titik marker aslinya
                map.setView(markers[0].getLatLng(), 14); 
                setTimeout(() => { 
                    if(markerReferences[autoPopupPortName.toLowerCase()]) {
                        markerReferences[autoPopupPortName.toLowerCase()].openPopup(); 
                    }
                }, 500);
            } else {
                const group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds(), { padding: [50, 50], maxZoom: 6 });
            }
        }
    }

    // 4. Logika Filter Dropdown Berantai & Reset Otomatis
    function filterMap(triggeredBy) {
        const selectedCountry = document.getElementById('searchCountry').value.toLowerCase();
        
        if (triggeredBy === 'country') {
            populatePorts(selectedCountry);
        }

        const selectedPort = document.getElementById('searchPort').value.toLowerCase();

        // LOGIKA RESET OTOMATIS
        if (selectedCountry === "" && selectedPort === "") {
            renderMarkers(allPortsData);
            map.setView([20.0, 0.0], 2); 
            return; 
        }

        const filteredPorts = allPortsData.filter(port => {
            const pName = (port.name || "").toLowerCase();
            const cName = (port.country_name || "").toLowerCase();
            
            const matchCountry = selectedCountry === "" || cName === selectedCountry;
            const matchPort = selectedPort === "" || pName === selectedPort;
            
            return matchCountry && matchPort;
        });

        const autoPopup = triggeredBy === 'port' && selectedPort !== "" ? selectedPort : null;
        renderMarkers(filteredPorts, autoPopup);
    }

    // 5. Jalankan saat halaman dibuka
    document.addEventListener('DOMContentLoaded', () => {
        populateCountries();
        populatePorts(""); 
        if(allPortsData && allPortsData.length > 0) {
            renderMarkers(allPortsData);
        }
    });
</script>
@endpush