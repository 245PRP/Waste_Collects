<?php
 session_start();
$id   = $_SESSION["id_user"];
//connexion à la base de donnée
try {
    $cnx= new PDO("mysql:host=localhost;dbname=waste_collect","root","");
}
catch(PDOException $e){
    echo"Erreur de connexion à la base de donnée veuillez réesayer plutard:".$e->getMessage();
}
  
  
try {
    // Tournées traitées
    $stmt = $cnx->prepare("SELECT COUNT(*) FROM ramassage, point_collecte WHERE ramassage.id_pt=point_collecte.id_pt AND id_user = :id AND statut = 'traitee'");
    $stmt->execute(['id' => $id]);
    $traitees = $stmt->fetchColumn();

    // Tournées en retard (en attente ET date > aujourd’hui)
    $stmt = $cnx->prepare("SELECT COUNT(*) FROM ramassage, point_collecte WHERE ramassage.id_pt=point_collecte.id_pt AND  id_user = :id AND statut = 'en attente' AND date_tour < CURDATE()");
    $stmt->execute(['id' => $id]);
    $retards = $stmt->fetchColumn();

    // Tournées en attente (date <= aujourd’hui)
    $stmt = $cnx->prepare("SELECT COUNT(*) FROM ramassage, point_collecte WHERE ramassage.id_pt=point_collecte.id_pt AND  id_user = :id AND statut = 'en attente'");
    $stmt->execute(['id' => $id]);
    $attente = $stmt->fetchColumn();

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
try {
    $sql = "SELECT  * FROM ramassage, point_collecte WHERE  ramassage.id_pt=point_collecte.id_pt AND ramassage.id_user=$id ";

    $stmt = $cnx->prepare($sql);
    $stmt->execute();
    $tounees = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}


if(!$_SESSION['id_user']){
  header('Location:../Pages/login.html');
} 
$nom=$_SESSION["nom_user"];
$role=$_SESSION["role"];
// Récupérer tous les points
$stmt = $cnx->prepare("SELECT * FROM point_collecte");
$stmt->execute();
$points = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>WASTE Collect</title>
  <link rel="stylesheet" href="../CSS/dashstyle.css" />
  <link rel="stylesheet" href="../CSS/chauff.css" />
  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <!-- CSS + JS du plugin Routing Machine -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
  <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

  <style>
    /* === Tableau + Carte côte-à-côte === */
    .section-grid {
      display: flex;
      flex-wrap: wrap; /* en mobile ça passe en colonne */
      gap: 20px;
      margin-top: 20px;
    }

    .table-column {
      flex: 1 1 48%;
      min-width: 300px;
    }

    .table-scroll {
      max-height: 500px;
      overflow-y: auto;
    }

    .map-column {
      flex: 1 1 48%;
      min-width: 300px;
    }

    .map-wrapper {
      background: #fff;
      border-radius: 12px;
      padding: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

    #map {
      height: 500px;
      width: 100%;
      border-radius: 8px;
    }

    .popup-content { text-align: center; }
    .btn-route {
        display: inline-block;
        margin-top: 5px;
        padding: 6px 12px;
        background: #2f4f4f;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        cursor: pointer;
    }
    .btn-route:hover { background: #758687; }

    .btn-info { background: #2f4f4f; color: white; padding: 5px 20px 5px 20px; border-radius:20px; text-decoration: none; }
    .btn-info:hover { background: #3a6161; }
    .rtd{
      background: red;
      border-radius:50%;
      color:#fff;
      padding:5px 10px 5px 10px;
    }
  </style>
</head>
<body>
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="brand">
      <div class="logo-circle">
        <img src="../Images/1.png" alt="WASTE Collect" />
      </div>
    </div>
    <nav class="menu">
      <a class="menu-item" href="../php/phpdash.php">
        <i class="fa-solid fa-house" style="color: #cfa13b"></i><span>Mes Tournées de Ramassage</span>
      </a>
      <a class="menu-item" href="../php/info.php">
        <i class="fa-solid fa-house" style="color: #cfa13b"></i><span>Mes Informations </span>
      </a>
      <?php if ($retards<=0) {?>
        
      <a class="menu-item" href="../php/statut.php">
        <i class="fa-solid fa-house" style="color: #cfa13b"></i><span>Statuts de mes Tournées </span>
      </a>
      <?php } else{ ?>
        
      <a class="menu-item" href="../php/statut.php" style="color: red">
        <i class="fa-solid fa-house"></i><span>Statuts de mes Tournées </span><span class="rtd"><strong><?php echo $retards ?></strong><span>
      </a>
      <?php } 
      ?>
      <?php if(($role==="administrateur")){?>
        <a class="menu-item" href="../Pages/dashboard.php">
          <i class="fa-solid fa-house" style="color: #cfa13b"></i><span>Accueil</span>
        </a>
        <a class="menu-item" href="../Pages/point.php">
          <i class="fa-solid fa-calendar-check" style="color: #cfa13b"></i>
          <span>Gestion des Points de Collecte</span>
        </a>
        <a class="menu-item" href="../php/tourner.php">
          <i class="fa-solid fa-truck" style="color: #cfa13b"></i>
          <span>Tournées de ramassage</span>
        </a>
        <a class="menu-item" href="../php/signale.php">
          <i class="fa-solid fa-calendar-check" style="color: #cfa13b"></i>
          <span>Gestion des Signalements</span>
        </a>
        <a class="menu-item" href="../php/camion.php">
          <i class="fa-solid fa-truck" style="color: #cfa13b"></i>
          <span>Gestion des chauffeurs et camions</span>
        </a>
        <a class="menu-item" href="../php/stat.php">
          <i class="fa-solid fa-chart-column" style="color: #cfa13b"></i>
          <span>Analyse Statistiques</span>
        </a>
        <a class="menu-item" href="../php/config.php">
          <i class="fa-solid fa-gears" style="color: #cfa13b"></i>
          <span>Configuration</span>
        </a>
        <a class="menu-item" href="../php/notif.php">
          <i class="fa-solid fa-bell" style="color: #cfa13b"></i>
          <span>Notifications</span>
        </a>
      <?php } ?>
      <a class="menu-item" href="../php/logout.php">
        <i class="fa-solid fa-arrow-right-from-bracket" style="color: #cfa13b"></i>
        <span>Déconnexion</span>
      </a>
    </nav>
  </aside>

  <!-- MAIN -->
  <div class="main">
    <!-- HEADER -->
    <header class="header">
      <div class="search">
        <img src="../Images/rechercher.png" class="search-icon" alt="" />
        <input type="search" placeholder="Rechercher…" />
      </div>
      <div class="header-right">
        <div class="app-name"><?php echo $nom; ?></div>
      </div>
    </header>

    <!-- STATS -->
    <section class="stats">
      <article class="stat">
        <div class="stat-title">Mes tournées Traitéées</div>
        <div class="stat-value"><?php echo $traitees; ?></div>
      </article>
      <article class="stat">
        <div class="stat-title">Mes tournées en retard</div>
        <div class="stat-value"><?php echo $retards; ?></div>
      </article>
      <article class="stat">
        <div class="stat-title">Mes tournées en attente</div>
        <div class="stat-value"><?php echo $attente; ?></div>
      </article>
    </section>

    <!-- TABLEAU + MAP -->
    <section class="section-grid">
      <!-- COLONNE GAUCHE : TABLEAU -->
      <div class="table-column">
        <div class="table-scroll">
          <table>
            <thead>
              <tr>
                <th>Point de collecte</th>
                <th>Date de la tournée</th>
                <th>ADRESSE</th>
                <th>Statut</th>
                <th>ACtions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($tounees)): ?>
                <?php foreach ($tounees as $sig): ?>
                  <!-- <?php
                    $date = date('d/m/Y', strtotime($sig['date_signal']));
                    $heure = date('H:i', strtotime($sig['date_signal']));
                    $motif = trim($sig['motif']);
                    $class = "badge badge-red";
                    if ($motif === "Cassé") $class = "badge badge-red";
                    elseif ($motif === "Plein") $class = "badge badge-yellow";
                    elseif ($motif === "Renversé") $class = "badge badge-blue";
                    elseif ($motif === "Absent") $class = "badge badge-gray";
                  ?> -->
                  <tr>
                    <td><?= htmlspecialchars($sig['nom_pt']); ?></td>
                    <td><?= htmlspecialchars($sig['date_tour']); ?></td>
                    <td><?= htmlspecialchars($sig['lieu']); ?></td>
                    <td><?= htmlspecialchars($sig['statut']); ?></td>
                    <td>
                      <a href="upstatut.php?id_tour=<?=$sig['id_tour']?>"class="btn btn-info">fait</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="5">Aucun signalement trouvé.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!--  CARTE -->
      <div class="map-column">
        <h2>Carte avec mes points et itinéraires</h2>
        <div class="map-wrapper">
          <div id="map"></div>
        </div>
      </div>
    </section>
  </div>

<script>
  // Initialisation de la carte 
  var map = L.map('map').setView([4.05, 9.7], 13);

map.getContainer().style.backgroundColor = '#ffffff';
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  var points = [
    <?php foreach ($points as $point):?>
      {nom: "<?= $point['nom_pt']; ?>", lat: <?= $point['latitude'];?>, lon: <?= $point['longitude'];?>},
    <?php endforeach;?>
  ];

  var routingControl = null;

  function ajouterPoint(p) {
      var marker = L.marker([p.lat, p.lon]).addTo(map);
      var popupContent = `
        <div class="popup-content">
          <h4>${p.nom}</h4>
          <button class="btn-route" onclick="tracerItineraire(${p.lat}, ${p.lon})">
            Lancer votre Itinéraire
          </button>
        </div>`;
      marker.bindPopup(popupContent);
  }

  points.forEach(ajouterPoint);

  function tracerItineraire(destLat, destLon) {
      if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
              var userLat = position.coords.latitude;
              var userLon = position.coords.longitude;
              if (routingControl) map.removeControl(routingControl);
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
</body>
</html>