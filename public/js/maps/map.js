document.addEventListener('DOMContentLoaded', function () {
    const collectors = window.collectors;

    collectors.forEach(collector => {
        // Verificar si las coordenadas son válidas
        if (collector.latitude && collector.longitude) {
            console.log(`Lat: ${collector.latitude}, Long: ${collector.longitude}`);  // Ver las coordenadas en la consola

            const mapId = 'map' + collector.id;
            const map = L.map(mapId).setView([collector.latitude, collector.longitude], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            L.circleMarker([collector.latitude, collector.longitude], {
                radius: 8,
                color: '#28a745',
                fillColor: '#28a745',
                fillOpacity: 0.9
            }).addTo(map).bindPopup(collector.company_name);
        } else {
            console.error('Coordenadas inválidas para el recolector:', collector);
        }
    });
});
