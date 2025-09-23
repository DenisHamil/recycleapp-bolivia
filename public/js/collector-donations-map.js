document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('map').setView([-17.7833, -63.1821], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    if (typeof DONATIONS_URL === 'undefined') {
        console.error('❌ DONATIONS_URL no está definido.');
        return;
    }

    const AUTH_USER_ID = document.getElementById('authUserId').value;

    fetch(DONATIONS_URL)
        .then(res => res.ok ? res.json() : Promise.reject(`HTTP ${res.status}`))
        .then(donations => {
            if (!Array.isArray(donations)) throw new Error('Respuesta inválida.');

            // 🚫 Filtrar donaciones completadas
            donations = donations.filter(d => d.state !== 'completed' && d.state !== 'cancelled');


            const grouped = {};
            donations.forEach(d => {
                const key = `${d.latitude}-${d.longitude}`;
                if (!grouped[key]) grouped[key] = [];
                grouped[key].push(d);
            });

            Object.values(grouped).forEach(group => {
                const first = group[0];
                let currentIndex = 0;

                const marker = L.circleMarker([first.latitude, first.longitude], {
                    radius: 10,
                    fillColor: first.category_color,
                    color: first.category_color,
                    weight: 1,
                    opacity: 1,
                    fillOpacity: 0.9
                }).addTo(map);

                const createPopupContent = (donation, index, total) => `
                    <div style="max-width: 270px;">
                        <h6 class="mb-1 text-primary fw-bold">${donation.category_name}</h6>
                        <p class="mb-1"><strong>Donador:</strong> ${donation.donor_name}</p>
                        <p class="mb-1"><strong>Descripción:</strong> ${donation.description}</p>
                        <p class="mb-1"><strong>Peso:</strong> ${donation.weight ?? 'No especificado'} kg</p>
                        <p class="mb-1"><strong>Dirección:</strong> ${donation.address ?? 'No disponible'}</p>
                        <p class="mb-1"><strong>Disponible:</strong><br>
                            <small>
                                ${donation.available_from_date} ${donation.available_from_time} <br>
                                hasta <br>
                                ${donation.available_until_date} ${donation.available_until_time}
                            </small>
                        </p>
                        ${donation.image_path ? `<img src="${donation.image_path}" class="img-fluid rounded mt-2 border">` : ''}

                        ${donation.collector_id === AUTH_USER_ID && donation.state === 'accepted' ? `
                            <button class="btn btn-danger btn-sm w-100 mt-2 finalize-btn" data-proposal-id="${donation.proposal_id}">
                                ✅ Finalizar recolección
                            </button>
                        ` : donation.collector_id === null ? `
                            <button class="btn btn-sm btn-success w-100 mt-2 send-proposal-btn" data-donation-id="${donation.id}">
                                📬 Enviar propuesta
                            </button>
                        ` : ''}

                        ${total > 1 ? `
                            <div class="mt-2 text-center">
                                <button class="btn btn-sm btn-outline-primary me-1" id="prev-${donation.latitude}-${donation.longitude}">⬅️</button>
                                <span>${index + 1}/${total}</span>
                                <button class="btn btn-sm btn-outline-primary ms-1" id="next-${donation.latitude}-${donation.longitude}">➡️</button>
                            </div>` : ''}
                    </div>
                `;

                marker.bindPopup(createPopupContent(group[0], 0, group.length));

                marker.on('popupopen', () => {
                    const key = `${first.latitude}-${first.longitude}`;

                    if (group.length > 1) {
                        document.getElementById(`prev-${key}`)?.addEventListener('click', () => {
                            currentIndex = (currentIndex - 1 + group.length) % group.length;
                            marker.setPopupContent(createPopupContent(group[currentIndex], currentIndex, group.length));
                            marker.openPopup();
                        });

                        document.getElementById(`next-${key}`)?.addEventListener('click', () => {
                            currentIndex = (currentIndex + 1) % group.length;
                            marker.setPopupContent(createPopupContent(group[currentIndex], currentIndex, group.length));
                            marker.openPopup();
                        });
                    }

                    setTimeout(() => {
                        document.querySelectorAll('.send-proposal-btn').forEach(button => {
                            button.addEventListener('click', function () {
                                const donationId = this.dataset.donationId;
                                document.getElementById('donationIdInput').value = donationId;

                                const modal = new bootstrap.Modal(document.getElementById('proposalModal'));
                                modal.show();
                            });
                        });

                        document.querySelectorAll('.finalize-btn').forEach(button => {
                            button.addEventListener('click', function () {
                                const proposalId = this.dataset.proposalId;
                                const form = document.getElementById('completeForm');
                                form.action = `/collector/proposals/${proposalId}/complete`;

                                const modal = new bootstrap.Modal(document.getElementById('completeModal'));
                                modal.show();
                            });
                        });
                    }, 100);
                });
            });
        })
        .catch(err => console.error('🚨 Error al cargar donaciones:', err));
});
