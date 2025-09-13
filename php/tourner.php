 <?php
 session_start();
 //connexion à la base de donnée
try {
    $cnx= new PDO("mysql:host=localhost;dbname=waste_collect","root","");
}
catch(PDOException $e){
    echo"Erreur de connexion à la base de donnée veuillez réesayer plutard:".$e->getMessage();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_user = $_POST['chauffeur'];
    $point   = $_POST['point'];
    $date    = $_POST['date_tour'];

    try {
        $stmt = $cnx->prepare("INSERT INTO ramassage (id_user, id_pt, date_tour) VALUES (:id_user, :id_pt, :date_tour)");
        $stmt->bindParam(':id_user', $id_user);
        $stmt->bindParam(':id_pt', $point);
        $stmt->bindParam(':date_tour', $date);
        $stmt->execute();

        
        header("Location: tourner.php");
        exit();
    } catch (PDOException $e) {
        echo "Erreur d'insertion: " . $e->getMessage();
    }
}

// affichage des chaufffeurs
try{ 
$sql2="SELECT * FROM utilisateur WHERE role = 'chauffeur'";
$stmt2=$cnx->prepare($sql2);
if($stmt2===false){
    throw new PDOException("Erreur lors de la preparation de la requete");
}
$stmt2->execute();
$chauf=$stmt2->fetchAll();
if($chauf===false){
    throw new PDOException("Erreur lors de la recuperation de la requete");
}

}
catch(PDOException $e){
    echo"Erreur:".$e->getMessage();
} 

// affichage des points collecte
try{ 
$sql3="SELECT id_pt, nom_pt FROM point_collecte ";
$stmt3=$cnx->prepare($sql3);
if($stmt3===false){
    throw new PDOException("Erreur lors de la preparation de la requete");
}
$stmt3->execute();
$points=$stmt3->fetchAll();
if($points===false){
    throw new PDOException("Erreur lors de la recuperation de la requete");
}

}
catch(PDOException $e){
    echo"Erreur:".$e->getMessage();
} 







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
  <link rel="stylesheet" href="../CSS/tourne.css" />
  
  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
   <script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>
  
  <!--<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>-->
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
        <div class="bell-wrap">
          <img src="../Images/notification.png" alt="Notifications" />
        </div>
        <div class="app-name"><?php echo $nom; ?></div>
      </div>
    </header>
    <body>
        <div class="containr-btn">
       <button id="Btn" class="btn " onclick="document.getElementById('id01').style.display='block'">+ Planifier une tournée de Ramassage</button>
      </div>

    <div id="calendar"></div>
    <style>
  /* Style de l’infobulle */
  
    #tooltip {
  display: none;
  position: absolute;
  background: #fff;
  border: 1px solid #ccc;
  padding: 20px;
  border-radius: 10px;
  font-size: 14px;
  box-shadow: 0px 4px 12px rgba(0,0,0,0.15);
  width: 500px;
  z-index: 1000;
}

#tooltip label {
  display: block;
  font-weight: bold;
  margin-bottom: 5px;
}

#tooltip input {
  width: 100%;
  padding: 6px;
  margin-bottom: 10px;
  border: 1px solid #ddd;
  border-radius: 6px;
  background: #f9f9f9;
  pointer-events: none; /* rend le champ non modifiable */
}
</style>
<div id="tooltip">
  <label>Chauffeur</label>
  <input type="text" id="tooltipChauffeur" readonly>

  <label>Point de Collecte</label>
  <input type="text" id="tooltipPoint" readonly>
</div>

<div id="tooltip" style="display:none; position:absolute; background:#333; color:#fff; padding:6px 10px; border-radius:5px; font-size:14px; z-index:1000;"></div>
    <script>
  document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      events: '../php/getEvents.php',  //   on récupère les tournées-->
      
      eventClick: function(info) {
        // Récupérer les infos
        const chauffeur = info.event.extendedProps.chauffeur;
        const point = info.event.extendedProps.point;
        
        // Remplir le contenu du tooltip
        document.getElementById('tooltipChauffeur').value = chauffeur;
        document.getElementById('tooltipPoint').value = point;

        // Récupération de la div tooltip
        const tooltip = document.getElementById('tooltip');
        tooltip.style.display = "block";

        // Positionner le tooltip à droite de l’événement cliqué
        const rect = info.el.getBoundingClientRect();
        tooltip.style.left = (rect.right + window.scrollX + 10) + "px";
        tooltip.style.top = (rect.top + window.scrollY) + "px";
      }
    });

    calendar.render();

    // Masquer le tooltip quand on clique ailleurs
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.fc-event') && !e.target.closest('#tooltip')) {
        document.getElementById('tooltip').style.display = 'none';
      }
    });
  });
</script>


<div id="id01" class="modal">
  <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">×</span>
  <div class="form-contenu">
      
            
                <h2>Planifier une tournée</h2>
<form class="modal-content" action="#" method="POST">
    <div class="form-group">
        <label for="chauffeur">Chauffeur Assignés :</label>
            <select id="limit" name="chauffeur">
            <?php foreach ($chauf as $chauff) : ?>
            <option value=<?= $chauff['id_user'] ?>><?= $chauff['nom_user'] ?></option>
            <?php endforeach; ?>   
            </select>
     </div>

     <div class="form-group">
        <label for="point">Point de Collecte choisi :</label>
        <select id="limit" name="point">
            <?php foreach ($points as $point) : ?>
            <option value=<?= $point['id_pt'] ?>><?= $point['nom_pt'] ?></option>
            <?php endforeach; ?>   
            </select>
        
     </div>

     <div class="form-group">
      <label for="date">Date de tournée à planifier:</label>
      <input type="date" name="date_tour" id="date" required>
     </div>

      <button type="submit" class="btn">Enregistrer</button>
    </form>
        
        </div>
  </div>
<script>
// Get the modal
var modal = document.getElementById('id01');

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>
       <!-- <script src="../Javascript/tourn.js"></script>-->
    </body>
    