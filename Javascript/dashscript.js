// Filtre recherche
const searchInput = document.getElementById('searchInput');
const table = document.getElementById('signalsTable');

searchInput.addEventListener('input', () => {
  const q = searchInput.value.toLowerCase().trim();
  for (const tr of table.tBodies[0].rows) {
    const txt = tr.innerText.toLowerCase();
    tr.style.display = txt.includes(q) ? '' : 'none';
  }
  populateMarkers();
});

// Carte Leaflet centrée sur Douala
const map = L.map('map', { scrollWheelZoom: true }).setView([4.0511, 9.7679], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 20,
  attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// Couleurs selon statut
function getCSS(varName){
  return getComputedStyle(document.documentElement).getPropertyValue(varName).trim() || '#000';
}
const statusColor = {
  "cassé": getCSS('--danger'),
  "plein": getCSS('--warn'),
  "absent": getCSS('--absent'),
  "renversé": getCSS('--ok')
};

// Coordonnées quartiers
const places = {
  "Yassa":[4.0383, 9.7922],
  "Marché Central":[4.0485, 9.7042],
  "Bonamoussadi":[4.0811, 9.7489],
  "Akwa":[4.0503, 9.7047],
  "Deïdo":[4.0703, 9.7048],
  "Bali":[4.0644, 9.7069],
  "New-Bell":[4.0523, 9.7240],
  "Logbaba":[4.0958, 9.7895],
  "Makepe":[4.0823, 9.7413],
  "Bepanda":[4.0809, 9.7170],
  "Bonanjo":[4.0519, 9.6870],
  "Dakar":[4.0539, 9.7163]
};

const markers = L.layerGroup().addTo(map);

function populateMarkers(){
  markers.clearLayers();
  for(const tr of table.tBodies[0].rows){
    if(tr.style.display==='none') continue;
    const place = tr.dataset.place;
    const status = (tr.dataset.status || '').toLowerCase();
    const name = tr.cells[0].textContent.trim();
    const coords = places[place];
    if(!coords) continue;

    const color = statusColor[status] || '#3388ff';

    const marker = L.circleMarker(coords,{
      radius: 9, weight: 2, color: color, fillColor: color, fillOpacity:.25
    })
    .bindPopup(<b>${name}</b><br>${place}<br><small>Statut: ${status.toUpperCase()}</small>)
    .addTo(markers);

    L.circleMarker(coords,{radius:4, weight:0, fillColor:color, fill:true, fillOpacity:.9}).addTo(markers);
  }
  if(markers.getLayers().length){
    map.fitBounds(L.featureGroup(markers.getLayers()).getBounds().pad(0.25));
  }
}
populateMarkers();