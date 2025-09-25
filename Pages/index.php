<?php
// index.php
try {
    $cnx = new PDO("mysql:host=localhost;dbname=waste_collect", "root", "");
    $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$stmt = $cnx->query("SELECT id_pt, nom_pt, lieu, latitude, longitude FROM point_collecte");
$points = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Accueil — Gestion des points de collecte</title>

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />

  <style>
    html, body {
      height:100%;
      margin:0;
      padding:0;
      font-family: Arial, sans-serif;
    }
    #map {
      height:100vh;
      width:100%;
      z-index:0;
    }

    /* --- Menu arrondi haut gauche --- */
    .map-menu {
      position: absolute; 
      top: 20px; left: 20px;
      width: 400px;
      background: #ffffffee; 
      border-radius: 20px;
      z-index: 1000;
      padding: 20px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.25);
    }
    .map-menu .brand {
      text-align: center;
      margin-bottom: 15px;
    }
    .map-menu .brand img {
      width: 100%;
      height: auto;
      margin-bottom: 8px;
    }
    .map-menu .brand strong {
      display: block;
      font-size: 18px;
      color: #2b8aef;
      
    }
    .map-menu nav a {
      display: block;
      padding: 12px;
      margin: 8px 0;
      font-size: 15px;
      color: #333;
      text-decoration: none;
      background: #f4f4f4;
      border-radius: 12px;
      text-align: center;
      transition: all 0.3s ease;
    }
    .map-menu nav a:hover {
      background: #2f4f4f;
      color: #fff;
    }

    /* --- Modal --- */
    .modal {
      display: none;
      position: fixed;
      z-index: 2000;
      left: 0; top: 0; width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
    }
    .modal-content {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      max-width: 400px;
      width: 90%;
      text-align: center;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    .modal-content h3 {
      margin-top: 0;
      color: #2f4f4f;
    }
    .modal-content button {
      margin: 10px;
      padding: 10px 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .btn-close { background: #ccc; }
    .btn-route { background: #2f4f4f; color: #fff; }
  </style>
</head>
<body>

<div id="map"></div>

<div class="map-menu">
  <div class="brand">
    <img src="../Images/1.png" alt="Logo Waste Collect">
    
  </div>
  <nav>
    <a href="signal.php"><i class="fa-solid fa-bullhorn"></i> Effectuer un signalement</a>
    <a href="index.php"><i class="fa-solid fa-map-marker-alt"></i> Trouver un point de collecte proche de chez moi</a>
    <a href="login.html"><i class="fa-solid fa-user"></i> Se connecter</a>
  </nav>
</div>

<!-- Modal -->
<div id="pointModal" class="modal">
  <div class="modal-content">
    <h3 id="modalTitle"></h3>
    <p id="modalLieu"></p>
    <button class="btn-close" onclick="closeModal()">Fermer</button>
    <button class="btn-route" onclick="calcRoute()">Itinéraire</button>
  </div>
</div>

<!-- Leaflet + Routing Machine JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

<script>
  const points = <?php echo json_encode($points, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;

  const map = L.map('map').setView([4.0483, 9.7679], 12);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution:'&copy; OpenStreetMap'
  }).addTo(map);

  // Icône poubelle améliorée (image)
  const binIcon = L.icon({
    iconUrl: '../Images/ben.png', // 🔥 Mets ici ton image de belle poubelle (PNG/SVG)
    iconSize: [40, 40],
    iconAnchor: [20, 40],
    popupAnchor: [0, -35]
  });

  let selectedPoint = null;
  let routingControl = null;

  points.forEach(p=>{
    if(!p.latitude || !p.longitude) return;
    const m = L.marker([p.latitude, p.longitude],{icon:binIcon}).addTo(map);
    m.on("click", ()=>{
      selectedPoint = p;
      document.getElementById("modalTitle").innerText = p.nom_pt;
      document.getElementById("modalLieu").innerText = p.lieu || "";
      document.getElementById("pointModal").style.display = "flex";
    });
  });

  function closeModal(){
    document.getElementById("pointModal").style.display = "none";
  }

  function calcRoute(){
    closeModal();
    if(!selectedPoint) return;

    if(routingControl){
      map.removeControl(routingControl);
    }

    if(navigator.geolocation){
      navigator.geolocation.getCurrentPosition(pos=>{
        const userLatLng = [pos.coords.latitude, pos.coords.longitude];
        routingControl = L.Routing.control({
          waypoints: [
            L.latLng(userLatLng),
            L.latLng(selectedPoint.latitude, selectedPoint.longitude)
          ],
          lineOptions: { styles: [{color: 'green', weight: 5}] },
          createMarker: () => null,
          addWaypoints: false
        }).addTo(map);
      }, ()=>{
        alert("Impossible de récupérer votre position.");
      });
    } else {
      alert("La géolocalisation n'est pas supportée par votre navigateur.");
    }
  }
</script>
<script>
  // Fonction pour calculer la distance entre 2 coordonnées 
function getDistance(lat1, lon1, lat2, lon2) {
  const R = 6371; // Rayon de la terre en km
  const dLat = (lat2 - lat1) * Math.PI / 180;
  const dLon = (lon2 - lon1) * Math.PI / 180;
  const a = 
    Math.sin(dLat/2) * Math.sin(dLat/2) +
    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
    Math.sin(dLon/2) * Math.sin(dLon/2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
  return R * c; // distance en km
}

// Action quand on clique sur "Trouver un point de collecte"
document.querySelector('a[href="index.php"]').addEventListener("click", function(e){
  e.preventDefault(); // empêche la redirection
  if(navigator.geolocation){
    navigator.geolocation.getCurrentPosition(pos=>{
      const userLat = pos.coords.latitude;
      const userLon = pos.coords.longitude;

      // Calculer la distance de chaque point
      let proches = points.map(p=>{
        return {
          ...p,
          distance: getDistance(userLat, userLon, p.latitude, p.longitude)
        };
      });

      // Trier par distance croissante
      proches.sort((a,b)=>a.distance-b.distance);

      // Garder seulement les 5 plus proches (ou dans un rayon de 3 km par ex.)
      proches = proches.slice(0,5);

      // Nettoyer la carte (enlever anciens marqueurs )
      map.eachLayer(layer=>{
        if(layer instanceof L.Marker || layer instanceof L.Circle) map.removeLayer(layer);
      });

      // Ajouter la position de l’utilisateur
      L.marker([userLat, userLon]).addTo(map).bindPopup("Vous êtes ici").openPopup();

      // Ajouter les points proches
      proches.forEach(p=>{
        const m = L.marker([p.latitude, p.longitude], {icon: binIcon}).addTo(map);
        m.bindPopup(`<b>${p.nom_pt}</b><br>${p.lieu}<br><i>${p.distance.toFixed(2)} km</i>`);
      });

      // Centrer la carte sur l’utilisateur
      map.setView([userLat, userLon], 14);

    }, ()=>{ alert("Impossible de récupérer votre position."); });
  } else {
    alert("La géolocalisation n'est pas supportée par votre navigateur.");
  }
});

  </script>
</body>
</html>