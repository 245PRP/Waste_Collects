// Initialisation de la carte Leaflet
var map = L.map('map').setView([4.0503, 9.7679], 12); // Coordonnées Douala

// Couche de fond OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// Lieux et coordonnées
const lieux = {
    "Yassa": [4.0533, 9.8295],
    "Marché Central": [4.0506, 9.7671],
    "Dakar": [4.0600, 9.7600],
    "Bonamoussadi": [4.0920, 9.7360]
};

// Ajout des marqueurs depuis le tableau
document.querySelectorAll("tbody tr").forEach(row => {
    const nom = row.cells[0].innerText;
    const statut = row.cells[1].innerText;
    const lieu = row.cells[2].innerText;
    if (lieux[lieu]) {
        L.marker(lieux[lieu])
            .addTo(map)
            .bindPopup(<b>${nom}</b><br>${lieu}<br><small>Statut: ${statut}</small>);
    }
});