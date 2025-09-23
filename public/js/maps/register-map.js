document.addEventListener("DOMContentLoaded", function () {
    const donorType = document.getElementById('donor_type');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');

    let marker;
    let color = '#007bff'; // Por defecto azul (familia)

    // Inicializar mapa centrado en Cochabamba
    const map = L.map('map').setView([-17.3935, -66.1570], 13);

    // Cargar capa base
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Función para obtener color por tipo de usuario
    function getColor(type) {
        switch (type) {
            case 'family':
                return '#007bff'; // Azul
            case 'organization':
                return '#43a047'; // Verde
            case 'collector':
                return '#ff9800'; // Naranja
            default:
                return '#007bff';
        }
    }

    // Si existe select de tipo, escucha cambios para actualizar color
    donorType?.addEventListener('change', () => {
        color = getColor(donorType.value);

        // Si ya hay marcador, lo quitamos y reiniciamos
        if (marker) {
            map.removeLayer(marker);
            marker = null;
            latitudeInput.value = '';
            longitudeInput.value = '';
        }
    });

    // Click en el mapa para colocar pin
    map.on('click', function (e) {
        const { lat, lng } = e.latlng;

        if (marker) {
            map.removeLayer(marker);
        }

        marker = L.circleMarker([lat, lng], {
            radius: 10,
            fillColor: color,
            color: '#333',
            weight: 1,
            opacity: 1,
            fillOpacity: 0.9
        }).addTo(map);

        latitudeInput.value = lat;
        longitudeInput.value = lng;
    });
});
