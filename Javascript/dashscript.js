// Carte Leaflet — centrée sur Douala (aucun marqueur ajouté)
const map = L.map('map', { scrollWheelZoom: true }).setView([4.0511, 9.7679], 12);

// Fond OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 20,
  attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

/* 
  Remarques :
  - Tous les pictogrammes de la sidebar/entêtes/recherche/cloche sont des <img>.
    Remplace simplement les fichiers dans:
      images/logo.png
      images/icons/*.png
      images/headers/*.png
  - Si tu veux des marqueurs plus tard, on peut forcer les icônes Leaflet:
    L.Icon.Default.mergeOptions({
      iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
      shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png'
    });
*/