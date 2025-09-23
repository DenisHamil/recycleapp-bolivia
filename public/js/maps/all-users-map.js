document.addEventListener('DOMContentLoaded', function () {
    const donors = window.donors || [];
    const collectors = window.collectors || [];

    const map = L.map('allUsersMap').setView([-17.3895, -66.1568], 12); // Centro en Cochabamba

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Función para agregar marcadores
    function addCircleMarker(user, color, popupHtml) {
        if (user.latitude && user.longitude) {
            L.circleMarker([user.latitude, user.longitude], {
                radius: 8,
                color: color,
                fillColor: color,
                fillOpacity: 0.9
            }).addTo(map).bindPopup(popupHtml);
        }
    }

    // Donadores (color azul)
    donors.forEach(donor => {
        const popup = `
            <strong>Donador</strong><br>
            <strong>Nombre:</strong> ${donor.first_name || ''} ${donor.last_name || ''}<br>
            <strong>Email:</strong> ${donor.email}<br>
            <strong>Dirección:</strong> ${donor.address || 'No disponible'}
        `;
        addCircleMarker(donor, '#007bff', popup);
    });

    // Recolectores (color verde)
    collectors.forEach(collector => {
        const popup = `
            <strong>Recolector</strong><br>
            <strong>Empresa:</strong> ${collector.company_name}<br>
            <strong>Representante:</strong> ${collector.representative_name}<br>
            <strong>Email:</strong> ${collector.email}<br>
            <strong>Dirección:</strong> ${collector.address || 'No disponible'}
        `;
        addCircleMarker(collector, '#43a047', popup);
    });
});
