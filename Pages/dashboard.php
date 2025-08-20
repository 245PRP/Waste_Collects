<?php 
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
      <a class="menu-item" href="../Pages/point.html">
        <i class="fa-solid fa-calendar-check" style="color: #cfa13b"></i>
        <span>Gestion des Points de Collecte</span>
      </a>
      <a class="menu-item" href="#">
        <i class="fa-solid fa-truck" style="color: #cfa13b"></i>
        <span>Tournées de ramassage</span>
      </a>
      <?php if(($role==="administrateur")){?>
      
      
      <a class="menu-item" href="#">
        <i class="fa-solid fa-chart-column" style="color: #cfa13b"></i>
        <span>Analyse Statistiques</span>
      </a>
      <a class="menu-item" href="#">
        <i class="fa-solid fa-gears" style="color: #cfa13b"></i>
        <span>Configuration</span>
      </a>
      <a class="menu-item" href="#">
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
        <div class="stat-value">20</div>
      </article>
      <article class="stat">
        <div class="stat-title">Points de collecte Ajoutés</div>
        <div class="stat-value">+15</div>
      </article>
      <article class="stat">
        <div class="stat-title">Points de collecte Supprimés</div>
        <div class="stat-value">-5</div>
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

































































































































































































































