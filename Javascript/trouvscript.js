// Initialisation de la carte
var map = L.map('map').setView([4.0503, 9.7679], 13); // Coordonnées de  Douala

// Couche OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Bouton géolocalisation
document.getElementById('btn-geo').addEventListener('click', function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var lat = position.coords.latitude;
            var lon = position.coords.longitude;
            map.setView([lat, lon], 15);
            L.marker([lat, lon]).addTo(map)
                .bindPopup("Vous êtes ici")
                .openPopup();
        }, function() {
            alert("Impossible de récupérer votre position.");
        });
    } else {
        alert("La géolocalisation n'est pas supportée par ce navigateur.");
    }
});
