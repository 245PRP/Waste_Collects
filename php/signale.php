<?php
 session_start();
//connexion à la base de donnée
try {
    $cnx= new PDO("mysql:host=localhost;dbname=waste_collect","root","");
}
catch(PDOException $e){
    echo"Erreur de connexion à la base de donnée veuillez réesayer plutard:".$e->getMessage();
}
// ajouter un point
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $motif=$_POST['motif'];
    $adresse=$_POST['adresse'];
    $date=$_POST['date_signal'];
    $description=$_POST['description'];
    $id_pt=$_POST['point'];
    $id=$_SESSION["id_user"];
    
  

    try {
        $stmt=$cnx->prepare('INSERT INTO signalement(motif,adresse,date_signal,description,id_user,id_pt)VALUES (:motif, :adresse, :date_signal, :description, :id_user, :id_pt)');
        $stmt->bindParam(':motif', $motif);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':date_signal', $date);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id_user', $id);
        $stmt->bindParam(':id_pt', $id_pt);
        $stmt->execute();
        //header('Location: ../Pages/signal.html');
    } catch (PDOException $e) {
        echo"Erreur d'insertion des signalements dans la base de donnée".$e->getMessage();
    }

}
try{


$sql="SELECT * FROM signalement,utilisateur WHERE signalement.id_user=utilisateur.id_user" ;
$stmt=$cnx->prepare($sql);
if($stmt===false){
    throw new PDOException("Erreur lors de la preparation de la requete");
}
$stmt->execute();
$sign=$stmt->fetchAll();
if($sign===false){
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
  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" href="../CSS/signal.css">

  <!-- ===== DataTables et jQuery (HEAD) ===== -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

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
<!-- Tableau -->
  <h2 class="signal-title">Liste des signalements</h2>

<!-- Tableau transformé en DataTable -->
<table id="signalTable" class="display">
  <thead>
    <tr>
      <th>Nom</th>
      <th>Motif</th>
      <th>Adresse</th>
      <th>Date et Heure</th>
      <th>Description</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($sign as $signal) : ?>
      <?php
        // formattage date/heure si champ DATETIME
        $dateHeure = $signal['date_signal'];
        // Mon NB: pour formater  on fait : $formattedDate = date('d/m/Y H:i', strtotime($dateHeure));

        $formattedDate = date('d/m/Y H:i', strtotime($dateHeure));
        // badge selon motif 
        $motif = htmlspecialchars($signal['motif']);
        $badgeClass = 'badge-autre';
        if (stripos($motif, 'Plein') !== false) $badgeClass = 'badge-plein';
        if (stripos($motif, 'Cass') !== false) $badgeClass = 'badge-casse';
        if (stripos($motif, 'Renvers') !== false) $badgeClass = 'badge-renverse';
        if (stripos($motif, 'absent') !== false) $badgeClass = 'badge-absent';
      ?>
    <tr>
      <td><?= htmlspecialchars($signal['nom_user']) ?></td>
      <td><span class="badge <?= $badgeClass ?>"><?= $motif ?></span></td>
      <td><?= htmlspecialchars($signal['adresse'] ?? '') ?></td>
      <td><?= $formattedDate ?></td>
      <td><?= nl2br(htmlspecialchars($signal['description'] ?? '')) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<script>
  $(document).ready(function() {
    $('#signalTable').DataTable({
      "pageLength": 8,
      "lengthMenu": [5, 8, 10, 20, 40],
      "order": [[3, "desc"]], // tri par date 
      "language": {
        "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
      },
      "columnDefs": [
        { "type": "date", "targets": 3 } // aide si DataTables doit trier les dates
      ]
    });
  });
</script>
