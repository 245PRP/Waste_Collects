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
    $stmt = $cnx->prepare("SELECT COUNT(*) FROM ramassage WHERE  id_user = :id AND statut = 'traitée'");
    $stmt->execute(['id' => $id]);
    $traitees = $stmt->fetchColumn();

    // Tournées futures (en attente ET date > aujourd’hui)
    $stmt = $cnx->prepare("SELECT COUNT(*) FROM ramassage WHERE  id_user = :id AND statut = 'en attente' AND date_tour > CURDATE()");
    $stmt->execute(['id' => $id]);
    $futures = $stmt->fetchColumn();

    // Tournées en attente (date <= aujourd’hui)
    $stmt = $cnx->prepare("SELECT COUNT(*) FROM ramassage WHERE  id_user = :id AND statut = 'en attente' AND date_tour < CURDATE()");
    $stmt->execute(['id' => $id]);
    $attente = $stmt->fetchColumn();

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
try {
    $sql = "
        SELECT 
            *
        FROM signalement
        INNER JOIN utilisateur 
            ON signalement.id_user = utilisateur.id_user
        ORDER BY signalement.date_signal DESC LIMIT 5
    ";

    $stmt = $cnx->prepare($sql);
    $stmt->execute();
    $signalements = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <!-- CSS Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <!-- CSS + JS du plugin Routing Machine -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
  <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

  <style>
    #map { 
      height: 500px; 
    }
    .popup-content { 
      text-align: center; 
    }
    .btn-route {
        display: inline-block;
        margin-top: 5px;
        padding: 6px 12px;
        background: #2f4f4f;;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        cursor: pointer;
    }
    .btn-route:hover {
        background: #758687;
    }
  </style>
</head>
<body>
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="brand">
      <!-- logo rond : remplace images/logo.png par ton image -->
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
      <style>
        /* --- header pour éviter que le menu soit coupé --- */
      .header, .header-right { position: relative; overflow: visible; z-index: 2; }

      /* --- dropdown --- */
      .dropdown { position: relative; display: inline-block; }
      .dropbtn { background: none; border: none; cursor: pointer; position: relative; }
      .dropbtn i { font-size: 22px; color: #cfa13b; }

      /* badge total */
      .badg {
        position: absolute; top: -6px; right: -8px;
        background: red; color: #fff; border-radius: 50%;
        padding: 2px 6px; font-size: 12px; font-weight: 700;
      }

      /* menu */
      .dropdown-content {
        display: none;
        position: absolute; right: 0; top: 32px;
        min-width: 240px; background: #fff; color: #fff;
        border-radius: 8px; box-shadow: 0 8px 16px rgba(0,0,0,.25);
        z-index: 9999; padding: 6px 0;
      }
      .dropdown-content.show { display: block; }  /* <= IMPORTANT */

      /* items */
      .dropdown-content a {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 14px; text-decoration: none; color: #000; font-size: 14px;
      }
      .dropdown-content a:hover { background: #ccc; }

      /* badge par ligne */
      .notif-count {
        background: red; color: #fff; border-radius: 12px;
        padding: 2px 8px; font-size: 12px; font-weight: 700;
      }


      </style>
      <script>
        
          function toggleDropdown(e) {
            if (e) e.stopPropagation(); // pour ne pas fermer immédiatement
            const dd = document.getElementById('notifDropdown');
            dd.classList.toggle('show');
          }

          // Fermer si on clique ailleurs
          document.addEventListener('click', function (ev) {
            const dd = document.getElementById('notifDropdown');
            if (!ev.target.closest('.dropdown')) dd.classList.remove('show');
          });

      </script>
    </header>
    <!-- STATS -->
    <section class="stats">
      <article class="stat">
        <div class="stat-title">Mes tournées Traitéées</div>
        <div class="stat-value"><?php echo $traitees; ?></div>
      </article>
      <article class="stat">
        <div class="stat-title">Mes tournées futures</div>
        <div class="stat-value"><?php echo $futures; ?></div>
      </article>
      <article class="stat">
        <div class="stat-title">Mes tournées en attente</div>
        <div class="stat-value"><?php echo $attente; ?></div>
      </article>
    </section>
     <!-- TABLEAU + MAP -->
    <section class="grid">
      <!-- TABLEAU -->
      <table>
  <thead>
    <tr>
      <th>NOM</th>
      <th>MOTIF DE SIGNALEMENT</th>
      <th>DATE ET HEURE</th>
      <th>ADRESSE</th>
      <th>DESCRIPTION</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($signalements)): ?>
      <?php foreach ($signalements as $sig): ?>
        <?php
          // Séparer la date et l’heure à partir du champ date_signal
          $date = date('d/m/Y', strtotime($sig['date_signal']));
          $heure = date('H:i', strtotime($sig['date_signal']));

          $motif = trim($sig['motif']);
          $motif = strtolower($sig['motif']);
          
          $class = "badge badge-red";
          if ($motif === "Cassé") {
            $class = "badge badge-red";
        }
          elseif ($motif === "Plein"){ 
            $class = "badge badge-yellow";
          } 
          elseif ($motif === "Renversé") {
            $class = "badge badge-blue";
        }
          elseif ($motif === "Absent") {
            $class = "badge badge-gray";
        }
        ?>
        <tr>
          <td><?= htmlspecialchars($sig['nom_user']); ?></td>
          <td><span class="<?= $class; ?>"><?= $motif; ?></span></td>
          <td><?= $date; ?></td>
          <td><?= htmlspecialchars($sig['lieu']); ?></td>
          <td><?= htmlspecialchars($sig['description']); ?></td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="5">Aucun signalement trouvé.</td></tr>
    <?php endif; ?>
  </tbody>
</table>


      <!-- MAP -->
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
  var points = [
    <?php foreach ($points as $point):?>

      {nom: "<?= $point['nom_pt']; ?>", lat:  <?= $point['latitude'] ;?>, lon: <?= $point['longitude'] ;?>},
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
  points.forEach(ajouterPoint);

 
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
 
</body>
</html>
  <!-- Leaflet -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="../Javascript/dashscript.js"></script>
</body>
</html>

