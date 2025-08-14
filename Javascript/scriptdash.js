// Carte Leaflet centrée sur Douala
var map = L.map('map').setView([4.0511, 9.7679], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// Exemple de points de collecte avec coordonnées (à remplacer par données réelles)
var points = [
    {name:"Yassa", coords:[4.042, 9.704]},
    {name:"Marché Central", coords:[4.051, 9.713]},
    {name:"Dakar", coords:[4.052, 9.750]},
    {name:"Bonamoussadi", coords:[4.083, 9.735]}
];

points.forEach(p => {
    L.marker(p.coords).addTo(map).bindPopup(p.name);
});
