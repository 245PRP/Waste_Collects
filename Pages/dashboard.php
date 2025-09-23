<?php 
//connexion à la base de donnée
try {
    $cnx= new PDO("mysql:host=localhost;dbname=waste_collect","root","");
    $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e){
    echo "Erreur de connexion à la base de donnée veuillez réessayer plus tard: " . $e->getMessage();
    exit;
}

/* --- Stats existantes --- */
$sql_satures="SELECT COUNT(*) AS total FROM point_collecte WHERE Etat = 'rempli'";
$total_satures = $cnx->query($sql_satures)->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$sql_vides="SELECT COUNT(*) AS total FROM point_collecte WHERE Etat = 'vide'";
$total_vides = $cnx->query($sql_vides)->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$sql_signalements_total = "SELECT COUNT(*) AS total FROM signalement";
$total_signalements = $cnx->query($sql_signalements_total)->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$sql_camion = "SELECT COUNT(*) AS total FROM camion";
$total_camion = $cnx->query($sql_camion)->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$sql_vidanges = "SELECT COUNT(*) AS total FROM point_collecte WHERE date_vidange >= CURDATE() AND date_vidange < CURDATE() + INTERVAL 1 DAY";
$total_vidanges = $cnx->query($sql_vidanges)->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$sql_chauffeur="SELECT COUNT(*) AS total FROM utilisateur WHERE role = 'chauffeur'";
$total_chauffeur = $cnx->query($sql_chauffeur)->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$sqlPoints = "SELECT COUNT(*) AS total FROM point_collecte WHERE Etat='rempli'";
$nbPoints = $cnx->query($sqlPoints)->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$sqlSignalements = "SELECT COUNT(*) AS total FROM signalement";
$nbSignalements = $cnx->query($sqlSignalements)->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$totalNotif = $nbPoints + $nbSignalements;

/* --- Session utilisateur --- */
session_start();
if(!isset($_SESSION['id_user']) || !$_SESSION['id_user']){
  header('Location:../Pages/login.html');
  exit;
} 
$nom=$_SESSION["nom_user"];
$role=$_SESSION["role"];

/* --- Récupérer les points pour la carte --- */
$stmt = $cnx->prepare("SELECT * FROM point_collecte");
$stmt->execute();
$points = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* --- Récupérer les signalements pour le tableau --- */
$sql_signals = "
  SELECT s.id_sign, u.nom_user, s.motif, p.nom_pt, s.date_signal
  FROM signalement s
  LEFT JOIN utilisateur u ON s.id_user = u.id_user
  LEFT JOIN point_collecte p ON s.id_pt = p.id_pt
  ORDER BY s.date_signal DESC
";
$signalements = $cnx->query($sql_signals)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>WASTE Collect</title>
  <link rel="stylesheet" href="../CSS/dashstyle.css" />

  <!-- Font Awesome (icônes sidebar) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <!-- Routing -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
  <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <style>
    .grid {
      display: grid;
      grid-template-columns: 1fr 560px; 
      gap: 20px;
      align-items: start;
      margin-top: 20px;
    }

    /* Conteneur tableau */
    .table-card { background: #fff; padding: 12px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); }
    .table-card table { width: 100%; border-collapse: collapse; }
    .table-card th, .table-card td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 14px; }
    .table-card thead th { background: #f7f7f7; text-transform: uppercase; font-size: 12px; color: #666; }

    /* Conteneur carte */
    .carte-container { background: #fff; border-radius: 12px; padding: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    #map { height: 500px; width: 100%; border-radius: 10px; }

    /* Masquer la recherche native DataTables */
    .dataTables_filter { display: none !important; }

    /* Badges motifs */
    .badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; color: #fff; font-weight: 700; display:inline-block; }
    .badge-plein { background: #d9534f; }
    .badge-casse { background: #f0ad4e; }
    .badge-renverse { background: #6f42c1; }
    .badge-absent { background: #6c757d; }
    .badge-autre { background: #0275d8; }

    @media (max-width: 980px) {
      .grid { grid-template-columns: 1fr; }
      #map { height: 360px; margin-top: 12px; }
    }
    
  </style>
</head>
<body>
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="brand"><div class="logo-circle"><img src="../Images/1.png" alt="WASTE Collect" /></div></div>
    <nav class="menu">
      <?php if(($role==="chauffeur")){?>
        <a class="menu-item" href="../php/phpdash.php"><i class="fa-solid fa-house"></i><span>MES TOURNEES</span></a>
        <a class="menu-item" href="../php/info.php"><i class="fa-solid fa-truck"></i><span>MES INFORMATIONS</span></a>
      <?php } ?>
      <?php if(($role==="administrateur")){?>
        <a class="menu-item" href="../Pages/dashboard.php"><i class="fa-solid fa-house"style="color: #cfa13b"></i><span>Accueil</span></a>
        <a class="menu-item" href="../Pages/point.php"><i class="fa-solid fa-calendar-check"style="color: #cfa13b"></i><span>Gestion Points de collecte</span></a>
        <a class="menu-item" href="../php/tourner.php"><i class="fa-solid fa-truck"style="color: #cfa13b"></i><span>Tournées</span></a>
        <a class="menu-item" href="../php/signale.php"><i class="fa-solid fa-calendar-check"style="color: #cfa13b"></i><span> Gestion des Signalements</span></a>
        <a class="menu-item" href="../php/camion.php"><i class="fa-solid fa-truck"style="color: #cfa13b"></i><span> Gestion des Chauffeurs & Camions</span></a>
        <a class="menu-item" href="../php/stat.php"><i class="fa-solid fa-chart-column"style="color: #cfa13b"></i><span>Statistiques</span></a>
        <a class="menu-item" href="../php/config.php"><i class="fa-solid fa-gears"style="color: #cfa13b"></i><span>Configuration</span></a>
        <a class="menu-item" href="../php/notif.php"><i class="fa-solid fa-bell"style="color: #cfa13b"></i><span>Notifications</span></a>
      <?php } ?>
      <a class="menu-item" href="../php/logout.php"><i class="fa-solid fa-arrow-right-from-bracket"style="color: #cfa13b"></i><span>Déconnexion</span></a>
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
       <div class="dropdown">
              <button onclick="toggleDropdown()" class="dropbtn">
                <i class="fa-solid fa-bell"></i>
                <span class="badg"><?php echo $totalNotif; ?></span>
              </button>
              <div id="notifDropdown" class="dropdown-content">
                    
                    <strong><a href="../php/notif.php?type=points">
                      Points Collecte Saturés <span class="notif-count"><?php echo $nbPoints; ?></span>
                    </a></strong>
                    <strong><a href="../php/notif.php?type=signalements">
                      Signalements <span class="notif-count"><?php echo $nbSignalements; ?></span>
                    </a></strong>
              </div>
            </div>
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
      <article class="stat"><div class="stat-title">Saturés</div><div class="stat-value"><?php echo $total_satures; ?></div></article>
      <article class="stat"><div class="stat-title">Vides</div><div class="stat-value"><?php echo $total_vides; ?></div></article>
      <article class="stat"><div class="stat-title">Signalements</div><div class="stat-value"><?php echo $total_signalements; ?></div></article>
      <article class="stat"><div class="stat-title">Camions</div><div class="stat-value"><?php echo $total_camion; ?></div></article>
      <article class="stat"><div class="stat-title">Ajoutés</div><div class="stat-value"><?php echo $total_vidanges; ?></div></article>
      <article class="stat"><div class="stat-title">Chauffeurs</div><div class="stat-value"><?php echo $total_chauffeur; ?></div></article>
    </section>

    <!-- TABLE + MAP -->
    <section class="grid">
      <!-- TABLE -->
      <div class="table-card">
        <h3>Signalements récents</h3>
        <table id="signalementsTable" class="display">
          <thead><tr><th>NOM</th><th>MOTIF</th><th>LIEU</th><th>DATE</th><th>HEURE</th></tr></thead>
          <tbody>
            <?php foreach($signalements as $s): 
              $date = $heure = ''; if (!empty($s['date_signal'])) { $dt = new DateTime($s['date_signal']); $date = $dt->format('d/m/Y'); $heure = $dt->format('H:i'); }
              $motif = htmlspecialchars($s['motif'] ?? ''); $motif_lower = mb_strtolower($motif); $badgeClass = 'badge-autre';
              if (strpos($motif_lower,'plein') !== false) $badgeClass = 'badge-plein';
              elseif (strpos($motif_lower,'cass') !== false) $badgeClass = 'badge-casse';
              elseif (strpos($motif_lower,'renvers') !== false) $badgeClass = 'badge-renverse';
              elseif (strpos($motif_lower,'absent') !== false) $badgeClass = 'badge-absent';
            ?>
            <tr>
              <td><?= htmlspecialchars($s['nom_user'] ?? 'Anonyme') ?></td>
              <td><span class="badge <?= $badgeClass ?>"><?= $motif ?: '—' ?></span></td>
              <td><?= htmlspecialchars($s['nom_pt'] ?? '—') ?></td>
              <td><?= $date ?></td>
              <td><?= $heure ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- CARTE -->
      <div class="carte-container">
        <h3>Carte avec mes points</h3>
        <div id="map"></div>
      </div>
    </section>
  </div>

<script>
  function toggleDropdown(e) {
    if (e) e.stopPropagation();
    document.getElementById('notifDropdown').classList.toggle('show');
  }
  document.addEventListener('click', e => { if (!e.target.closest('.dropdown')) document.getElementById('notifDropdown').classList.remove('show'); });

  // DataTable
  $(document).ready(function() {
    var table = $('#signalementsTable').DataTable({
      pageLength: 7,
      language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json" }
    });
    $('.header input[type="search"]').on('keyup', function(){ table.search(this.value).draw(); });
  });

  // Leaflet Map
  var map = L.map('map').setView([4.05, 9.7], 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

  var points = [
    <?php foreach ($points as $point): ?>
      {nom: <?= json_encode($point['nom_pt']) ?>, lat: <?= (float)$point['latitude'] ?>, lon: <?= (float)$point['longitude'] ?>},
    <?php endforeach; ?>
  ];

  var routingControl = null;
  function ajouterPoint(p) {
    var marker = L.marker([p.lat, p.lon]).addTo(map);
    marker.bindPopup(`<h4>${p.nom}</h4><button onclick="tracerItineraire(${p.lat}, ${p.lon})">Itinéraire</button>`);
  }
  points.forEach(ajouterPoint);

  function tracerItineraire(destLat, destLon) {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(pos) {
        if (routingControl) map.removeControl(routingControl);
        routingControl = L.Routing.control({
          waypoints: [L.latLng(pos.coords.latitude, pos.coords.longitude), L.latLng(destLat, destLon)],
          language: 'fr'
        }).addTo(map);
      }, () => alert("Position non trouvée."));
    } else { alert("Géolocalisation non supportée."); }
  }
</script>
</body>
</html>
