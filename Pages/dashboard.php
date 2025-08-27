<?php 
//connexion à la base de donnée
try {
    $cnx= new PDO("mysql:host=localhost;dbname=waste_collect","root","");
}
catch(PDOException $e){
    echo"Erreur de connexion à la base de donnée veuillez réesayer plutard:".$e->getMessage();
}

//récupérer le nombre de points de collecte saturés
$sql_satures="SELECT COUNT(*)   AS total FROM point_collecte WHERE Etat = 'rempli'";
$stmt = $cnx->query($sql_satures);
    if ($stmt === false) {
        throw new Exception("Erreur dans la requête : $sql_satures");
    }
    $row_satures = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_satures = $row_satures['total'];

// récupérer les point de collecte Vide
$sql_vides="SELECT COUNT(*)   AS total FROM point_collecte WHERE Etat = 'vide'";
$stmt = $cnx->query($sql_vides);
    if ($stmt === false) {
        throw new Exception("Erreur dans la requête : $sql_vides");
    }
    $row_vides = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_vides = $row_vides['total'];


//récupérer le nombre de signalement
$sql_signalements = "SELECT COUNT(*) AS total FROM signalement";
$stmt = $cnx->query($sql_signalements);
if ($stmt === false) {
    throw new Exception("Erreur dans la requête : $sql_signalements");
}
$row_signalements = $stmt->fetch(PDO::FETCH_ASSOC);
$total_signalements = $row_signalements['total'];


//récupérer le nombre de camion disponible
$sql_camion = "SELECT COUNT(*) AS total FROM camion";
$stmt = $cnx->query($sql_camion);
if ($stmt === false) {
    throw new Exception("Erreur dans la requête : $sql_camion");
}
$row_camion = $stmt->fetch(PDO::FETCH_ASSOC);
$total_camion = $row_camion['total'];


//récupérer le nombre de points de collecte ajoutés
$sql_vidanges = "SELECT COUNT(*) AS total FROM point_collecte WHERE date_vidange >= CURDATE() AND date_vidange < CURDATE() + INTERVAL 1 DAY";

$stmt = $cnx->query($sql_vidanges);
$total_vidanges = $stmt->fetch(PDO::FETCH_ASSOC)['total'];






// récupérer le nombre de chauffeurs total
$sql_chauffeur="SELECT COUNT(*)   AS total FROM utilisateur WHERE role = 'chauffeur'";
$stmt = $cnx->query($sql_chauffeur);
    if ($stmt === false) {
        throw new Exception("Erreur dans la requête : $sql_chauffeur");
    }
    $row_chauffeur = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_chauffeur = $row_chauffeur['total'];





 session_start();
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
      <a class="menu-item" href="../php/tourner.php">
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
      <a class="menu-item" href="#">
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

    <!-- STATS -->
    <section class="stats">
      <article class="stat">
        <div class="stat-title">Points de collecte Saturés</div>
        <div class="stat-value"><?php echo $total_satures; ?></div>
      </article>
      <article class="stat">
        <div class="stat-title">Points de collecte Vides</div>
        <div class="stat-value"><?php echo $total_vides; ?></div>
      </article>
      <article class="stat">
        <div class="stat-title">Nombre de Signalements</div>
        <div class="stat-value"><?php echo $total_signalements; ?></div>
      </article>
      <article class="stat">
        <div class="stat-title">Nombre de Camion</div>
        <div class="stat-value"><?php echo $total_camion; ?></div>
      </article>
      <article class="stat">
        <div class="stat-title">Points de collecte Ajoutés</div>
        <div class="stat-value"><?php echo $total_vidanges; ?></div>
      </article>
      <article class="stat">
        <div class="stat-title">Nombres de Chauffeurs</div>
        <div class="stat-value"><?php echo $total_chauffeur; ?></div>
      </article>
    </section>

    <!-- TABLE + MAP -->
    <section class="grid">
      <!-- TABLE -->
      <div class="card table-card">
        <div class="table-scroll">
          <table>
            <thead>
              <tr>
                <th>
                  NOM
                </th>
                <th>
                  MOTIF DE SIGNALEMENT
                </th>
                <th>
                  LIEUX
                </th>
                <th>
                  DATE
                </th>
                <th>
                 HEURE
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>NOUFEYA VICTOIRE</td>
                <td><span class="badge badge-casse">Cassé</span></td>
                <td>Yassa</td>
                <td>14/08/2025</td>
                <td>10:32</td>
              </tr>
              <tr>
                <td>MBOUTOU KAREYCE</td>
                <td><span class="badge badge-plein">Plein</span></td>
                <td>Marché Central</td>
                <td>14/08/2025</td>
                <td>09:18</td>
              </tr>
              <tr>
                <td>MBOENE VANELLE</td>
                <td><span class="badge badge-absent">Absent</span></td>
                <td>Dakar</td>
                <td>13/08/2025</td>
                <td>16:47</td>
              </tr>
              <tr>
                <td>SCHEMIMA "P"</td>
                <td><span class="badge badge-plein">Plein</span></td>
                <td>Bonamoussadi</td>
                <td>13/08/2025</td>
                <td>11:06</td>
              </tr>
              <tr>
                <td>NYA JACQUES</td>
                <td><span class="badge badge-renverse">Renversé</span></td>
                <td>Yassa</td>
                <td>12/08/2025</td>
                <td>18:25</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- MAP -->
      <div class="card map-card">
        <div class="map-head">
          <div class="map-title">Rechercher</div>
          <img src="../Images/rechercher.png" alt="" class="map-gear" />
        </div>
        <div id="map"></div>
      </div>
    </section>
  </div>

  <!-- Leaflet -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="../Javascript/dashscript.js"></script>
</body>
</html>

































































































































































































































