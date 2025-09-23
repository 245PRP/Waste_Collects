
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
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Accueil — Gestion des points de collecte</title>
  <link rel="stylesheet" href="../CSS/styl.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>

  <!-- NAVBAR -->
  <header class="navbar">
    <div class="brand">
      <img src="../Images/1.png" alt="Logo" class="logo">
      <strong>Collecte & Propreté</strong>
    </div>
    <nav class="menu">
      <a href="#">Accueil</a>
      <a href="#missions" class="dropbtn">Nos Missions</a>
      <a href="../Pages/signal.php">Point de collecte</a>
      <a href="../Pages/login.html" class="btn-login">Se connecter</a>
    </nav>
  </header>

  <!-- CARROUSEL -->
  <section class="carousel" id="hero">
    <div class="slide is-active">
      <img src="../Images/a2.jfif" alt="Déchets - slide 1">
      <div class="overlay"></div>
      <div class="caption">
        <h1>Pratiquons des attitudes saines pour notre environnement</h1>
        <p>Chaque geste de tri et de propreté compte pour Douala.</p>
      </div>
    </div>

    <div class="slide">
      <img src="../Images/v4.jpg" alt="Déchets - slide 2">
      <div class="overlay"></div>
      <div class="caption">
        <h1>Réduire, Réutiliser, Recycler</h1>
        <p>Moins de déchets dans nos rues, plus de vie dans nos quartiers.</p>
      </div>
    </div>

    <div class="slide">
      <img src="../Images/v3.jfif" alt="Déchets - slide 3">
      <div class="overlay"></div>
      <div class="caption">
        <h1>Agissons ensemble, aujourd’hui</h1>
        <p>Signalez, localisez, collectez — au service des citoyens.</p>
      </div>
    </div>

    <!-- Contrôles -->
    <button class="ctrl prev" aria-label="Précédent">❮</button>
    <button class="ctrl next" aria-label="Suivant">❯</button>
    <div class="dots" aria-label="Navigation du carrousel"></div>
  </section>

  <!-- NOS MISSIONS -->
  <section class="section" id="missions">
    <h2 class="section-title">Nos Missions</h2>
    <div class="missions-grid">
      <article class="mission-card">
        <h3>Collecte Optimisée  <i class="fa-solid fa-dumpster"></i></h3>
        <p>Planifier et suivre les tournées en temps réel pour éviter les doublons et réduire les coûts.</p>
      </article>
      <article class="mission-card">
        <h3>Sensibilisation</h3>
        <p>Informer les citoyens sur les bons gestes et encourager le signalement des problèmes.</p>
      </article>
      <article class="mission-card">
        <h3>Traçabilité  <i class="fa-solid fa-bars-progress"></i></h3>
        <p>Mesurer les performances : zones collectées, délais d’intervention, efficacité des équipes.</p>
      </article>
      <article class="mission-card">
        <h3>Recyclage  <i class="fa-solid fa-recycle"></i></h3>
        <p>Donner une nouvelle vie aux matériaux pour réduire l’impact environnemental.</p>
      </article>
    </div>
  </section>

  <!-- REJOIGNEZ-NOUS -->
  <section class="join">
    <div class="join-content">
      <h2>Rejoignez-nous</h2>
      <p>Participez à la propreté de votre ville : créez un compte citoyen, devenez volontaire ou partenaire.</p>
      <div class="join-actions">
        <a href="../Pages/inscr.html" class="btn primary">Créer un compte</a>
        <a href="#" class="btn outline">Devenir partenaire</a>
      </div>
    </div>
  </section>

  <!-- FOOTER (NOIR) -->
  <footer class="site-footer">
    <div class="footer-grid">
      <div class="footer-brand">
        <img src="../Images/1.png" alt="Logo">
        <div class="join-socials">
        <span>Ou continuez avec :</span>
        <img src="../Images/facebook (2).png" alt="Facebook">
        <img src="../Images/gmail.png" alt="Gmail">
      </div>
        <div>
          <div class="foot-title">Collecte & Propreté</div>
          <p>Une ville propre commence par un geste simple.</p>
        </div>
      </div>

      <div class="footer-col">
        <div class="foot-title">Point de collecte</div>
        <a href="#">Trouver un point de collecte</a>
        <a href="#">Signaler un problème</a>
        <a href="#">Consulter la carte</a>
      </div>

      <div class="footer-col">
        <div class="foot-title">Nos Missions</div>
        <a href="#missions">Collecte optimisée</a>
        <a href="#missions">Sensibilisation</a>
        <a href="#missions">Traçabilité</a>
        <a href="#missions">Recyclage</a>
      </div>

      <div class="footer-col">
        <div class="foot-title">Nos Contacts</div>
        <p>📞 690 42 09 53</p>
        <p>📞 677 62 84 01</p>
        <p>✉️ contact@collecte-verte.cm</p>
        <p>📍 Douala, Cameroun</p>
      </div>
    </div>
    <div class="foot-note">© 2025 — Tous droits réservés.</div>
  </footer>

  <script src="../Javascript/script.js" defer></script>
</body>
</html>
new
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

    /* --- Menu arrondi sur la gauche --- */
    .map-menu {
      position: absolute; 
      top: 50%; left: 20px;
      transform: translateY(-50%);
      width: 280px;
      background: #ffffffee; 
      border-radius: 20px;
      z-index: 1000;
      padding: 25px 20px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.25);
    }
    .map-menu h2 {
      margin-top: 0;
      font-size: 20px;
      margin-bottom: 20px;
      text-align: center;
      color: #2b8aef;
    }
    .map-menu nav a {
      display: block;
      padding: 12px;
      margin: 10px 0;
      font-size: 15px;
      color: #333;
      text-decoration: none;
      background: #f4f4f4;
      border-radius: 12px;
      text-align: center;
      transition: all 0.3s ease;
    }
    .map-menu nav a:hover {
      background: #2b8aef;
      color: #fff;
    }

    /* --- Icône poubelle --- */
    .bin-marker {
      width:36px;height:36px;border-radius:50%;
      background:#2b8aef;color:#fff;display:flex;
      align-items:center;justify-content:center;
      border:2px solid #fff; 
      box-shadow:0 2px 6px rgba(0,0,0,0.3);
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
      color: #2b8aef;
    }
    .modal-content button {
      margin: 10px;
      padding: 10px 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .btn-close { background: #ccc; }
    .btn-route { background: #2b8aef; color: #fff; }
  </style>
</head>
<body>

<div id="map"></div>

<div class="map-menu">
  <h2>Collecte & Propreté</h2>
  <nav>
    <a href="signal.php"><i class="fa-solid fa-bullhorn"></i> Effectuer un signalement</a>
    <a href="trouv.html"><i class="fa-solid fa-map-marker-alt"></i> Trouver un point de collecte</a>
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

  const binIcon = L.divIcon({
    html:'<div class="bin-marker"><i class="fa-solid fa-trash"></i></div>',
    className:'',
    iconSize:[36,36],
    iconAnchor:[18,36]
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
          lineOptions: {
            styles: [{color: 'green', weight: 5}]
          },
          createMarker: () => null, // pas de marqueur en plus
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
</body>
</html>