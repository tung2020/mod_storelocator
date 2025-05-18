<?php defined('_JEXEC') or die; ?>

<form id="addressForm">
    <input type="text" id="addressInput" placeholder="Nhập địa chỉ cửa hàng..." required />
    <button type="submit">Định vị</button>
</form>

<div id="map" style="height: 400px; margin-top: 10px;"></div>

<!-- Nút xác nhận & xem GG Map -->
<div style="margin-top:10px;">
    <button id="confirmLocation">✅ Xác nhận vị trí</button>
    <a id="viewOnGgMap" href="#" target="_blank" style="display:none;">🌐 Xem trên Google Maps</a>
</div>

<!-- Kết quả vị trí -->
<div id="result" style="margin-top: 10px;"></div>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const map = L.map('map').setView([10.7769, 106.7009], 13); // TP.HCM mặc định
    const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker = L.marker([10.7769, 106.7009], {draggable: true}).addTo(map);
    let currentLatLng = {lat: 10.7769, lng: 106.7009};

    marker.on('dragend', function (e) {
        const pos = marker.getLatLng();
        currentLatLng = pos;
        updateResult(pos.lat, pos.lng);
        showGgMapLink(pos.lat, pos.lng);
    });

    document.getElementById("addressForm").addEventListener("submit", function (e) {
        e.preventDefault();
        const address = document.getElementById("addressInput").value;

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    currentLatLng = {lat: lat, lng: lon};

                    marker.setLatLng([lat, lon]);
                    map.setView([lat, lon], 16);

                    updateResult(lat, lon);
                    showGgMapLink(lat, lon);
                } else {
                    alert("Không tìm thấy địa chỉ. Hãy nhập địa chỉ chi tiết hơn.");
                }
            });
    });

    function updateResult(lat, lng) {
        document.getElementById("result").innerHTML =
            `<strong>Vị trí đã chọn:</strong><br>Lat: ${lat.toFixed(6)}<br>Lng: ${lng.toFixed(6)}`;
    }

    document.getElementById("confirmLocation").addEventListener("click", function () {
        alert(`Đã lưu vị trí: ${currentLatLng.lat.toFixed(6)}, ${currentLatLng.lng.toFixed(6)}`);
    });

    function showGgMapLink(lat, lng) {
        const url = `https://www.google.com/maps?q=${lat},${lng}`;
        const link = document.getElementById("viewOnGgMap");
        link.href = url;
        link.style.display = "inline-block";
    }

    updateResult(currentLatLng.lat, currentLatLng.lng);
    showGgMapLink(currentLatLng.lat, currentLatLng.lng);
});
</script>
