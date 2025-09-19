
<?php
?>
<html>
    <head>
       
</head>
<body>
    <main>
  <section>
    <div class="alert alert-1-primary">
      <h3 class="alert-title">Hello World</h3>
      <p class="alert-content">Lorem ipsum</p>
    </div>
    <div class="alert alert-2-secondary">
      <h3 class="alert-title">Hello World</h3>
      <p class="alert-content">Lorem ipsum</p>
    </div>
    <div class="alert alert-3-danger">
      <h3 class="alert-title">Hello World</h3>
      <p class="alert-content">Lorem ipsum</p>
    </div>
    <div class="alert alert-1-warning">
      <h3 class="alert-title">Hello World</h3>
      <p class="alert-content">Lorem ipsum</p>
    </div>
    <div class="alert alert-2-success">
      <h3 class="alert-title">Hello World</h3>
      <p class="alert-content">Lorem ipsum</p>
    </div>
  </section>
</main>
</body>




<!-- Modal -->
  <div id="id01" class="modal">
    <div class="modal-content">
      <span class="close" onclick="document.getElementById('id01').style.display='none'">&times;</span>
      <h3>Ajouter un point</h3>
      <form action="#" method="POST" class="form-grid">
        <div class="form-group">
          <label>Nom du point</label>
          <input type="text" name="nom_pt" required>
        </div>
        <div class="form-group">
          <label>Capacité</label>
          <input type="number" name="capacite" required>
        </div>
        <div class="form-group">
          <label>Lieu</label>
          <input type="text" name="lieu" required>
        </div>
        <div class="form-group">
          <label>État actuel</label>
          <select name="Etat">
            <option value="vide">Vide</option>
            <option value="rempli">Rempli</option>
          </select>
        </div>
        <div class="form-group" style="grid-column: 1 / span 2;">
          <label>Date de vidange</label>
          <input type="datetime-local" name="date_vidange" value="<?php echo date('Y-m-d\TH:i'); ?>">
        </div>
        <div class="form-group">
          <label>Entrer la latitude</label>
          <input type="text" name="latitude" id="latitude" placeholder="Latitude" required>
      </div>
      <div class="form-group">
        <label> Entrer la longitude</label>
          <input type="text" name="longitude" id="longitude" placeholder="Longitude" required>
      </div>
        <div class="form-actions">
          <button type="submit">Ajouter</button>
        </div>
      </form>
      <div class="prp">
          <h2>Carte avec mes points et itinéraires</h2>
<div id="map"></div>

<script>
  //  Initialisation de la carte 
  var map = L.map('map').setView([4.05, 9.7], 13);

  // Fond de carte OpenStreetMap (via Leaflet)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  // === Définition de quelques points ===
  var poits = [
<?php foreach ($poits as $poit):?>

  {nom: "<?= $poit['nom_pt']; ?>", lat: <?= $poit['latitude'] ;?>, lon: <?= $poit['longitude'] ;?>},
<?php endforeach;?>
  ];
  

  // Stockage du routing control (itinéraire) pour pouvoir le réinitialiser
  var routingControl = null;

  // Fonction pour ajouter un marqueur
  function ajouterPoint(p) {
      var marker = L.marker([p.lat, p.lon]).addTo(map);

      // Contenu du popup avec bouton
      var popupContent = `
        <div class="popup-content">
          <h4>${p.nom}</h4>
          <button class="btn-route" onclick="tracerItineraire(${p.lat}, ${p.lon})">
          Lancer votre Itinéraire
          </button>
        </div>
      `;

      marker.bindPopup(popupContent);
  }

  // Ajouter tous les points
  poits.forEach(ajouterPoint);

 
  // === Fonction de traçage d'itinéraire ===
  function tracerItineraire(destLat, destLon) {
      // Si l’utilisateur accepte la géolocalisation
      if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
              var userLat = position.coords.latitude;
              var userLon = position.coords.longitude;

              // Supprimer l’ancien itinéraire s’il existe
              if (routingControl) {
                  map.removeControl(routingControl);
              }

              // Créer le nouvel itinéraire
              routingControl = L.Routing.control({
                  waypoints: [
                      L.latLng(userLat, userLon),
                      L.latLng(destLat, destLon)
                  ],
                  routeWhileDragging: false,
                  language: 'fr'
              }).addTo(map);

          }, function() {
              alert("Impossible de récupérer votre position.");
          });
      } else {
          alert("La géolocalisation n'est pas supportée par votre navigateur.");
      }
  }

</script>