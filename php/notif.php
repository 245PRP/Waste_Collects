<?php 
 session_start();
 //connexion à la base de donnée
try {
    $cnx= new PDO("mysql:host=localhost;dbname=waste_collect","root","");
}
catch(PDOException $e){
    echo"Erreur de connexion à la base de donnée veuillez réesayer plutard:".$e->getMessage();
}
// Récupération des signalements avec utilisateur + point_collecte
$sql = "SELECT * FROM signalement INNER JOIN utilisateur ON signalement.id_user = utilisateur.id_user INNER JOIN point_collecte ON signalement.id_pt = point_collecte.id_pt
    ORDER BY signalement.date_signal DESC
    LIMIT 10
";
$stmt = $cnx->query($sql);
$signalements = $stmt->fetchAll();

if(!$_SESSION['id_user']){
  header('Location:../Pages/login.html');


} 
$nom=$_SESSION["nom_user"];
$role=$_SESSION["role"];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>WASTE Collect</title>
  <link rel="stylesheet" href="../CSS/dashstyle.css" />
  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <style>
    .notif-container {
      max-width: 100%;
      background: #fff;
      border-radius: 12px;
      padding: 1rem 1.5rem;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .notif-title {
      font-size: 1.5rem;
      font-weight: bold;
      margin-bottom: 1rem;
      border-bottom: 2px solid #cfa13b;
      padding-bottom: 0.5rem;
      color: #333;
    }
    .notif-item {
      border: 1px solid #eee;
      border-left: 4px solid #cfa13b;
      border-radius: 8px;
      padding: 0.8rem 1rem;
      margin-bottom: 0.8rem;
      background: #fafafa;
    }
    .notif-text {
      font-size: 0.95rem;
      color: #444;
      margin-bottom: 0.3rem;
    }
    .notif-time {
      font-size: 0.8rem;
      color: gray;
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
      <a class="menu-item" href="../Pages/dashboard.php">
        <i class="fa-solid fa-house" style="color: #cfa13b"></i><span>Accueil</span>
      </a>
      <a class="menu-item" href="../Pages/point.php">
        <i class="fa-solid fa-calendar-check" style="color: #cfa13b"></i>
        <span>Gestion des Points de Collecte</span>
      </a>
      <a class="menu-item" href="#">
        <i class="fa-solid fa-truck" style="color: #cfa13b"></i>
        <span>Tournées de ramassage</span>
      </a>
      <?php if(($role==="administrateur")){?>


      <a class="menu-item" href="../php/signale.php">
        <i class="fa-solid fa-calendar-check" style="color: #cfa13b"></i>
        <span>Gestion des Signalements</span>
      </a>
       <a class="menu-item" href="../php/camion.php">
        <i class="fa-solid fa-truck" style="color: #cfa13b"></i>
        <span>Gestion des chauffeurs et camions</span>
      </a>
      <a class="menu-item" href="#">
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
        <div class="bell-wrap">
          <img src="../Images/notification.png" alt="Notifications" />
        </div>
        <div class="app-name"><?php echo $nom; ?></div>
      </div>
    </header>
    <body>
<!-- NOTIFICATIONS -->
    <div class="notif-container">
      <div class="notif-title"><i class="fa-solid fa-bell" style="color:#cfa13b"></i> Notifications</div>

      <?php if (count($signalements) > 0): ?>
        <?php foreach ($signalements as $s): ?>
          <div class="notif-item">
            <div class="notif-text">
              <strong><?= htmlspecialchars($s['nom_user']) ?></strong> 
              (<?= htmlspecialchars($s['role']) ?>)
              a signalé sur le point 
              <strong><?= htmlspecialchars($s['nom_pt']) ?></strong>.
            </div>
            <div class="notif-motif">Motif : <?= htmlspecialchars($s['motif']) ?></div>
            <div class="notif-desc"><?= htmlspecialchars($s['description']) ?></div>
            <div class="notif-text">Adresse : <?= htmlspecialchars($s['adresse']) ?></div>
            <div class="notif-time"><?= date("d/m/Y H:i", strtotime($s['date_signal'])) ?></div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Aucun signalement trouvé.</p>
      <?php endif; ?>
    </div>
  </div>
      </body>