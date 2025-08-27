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
    $id=$_SESSION["id_user"];
    
  

    try {
        $stmt=$cnx->prepare('INSERT INTO signalement(motif,adresse,date_signal,description,id_user)VALUES (:motif, :adresse, :date_signal, :description, :id_user)');
        $stmt->bindParam(':motif', $motif);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':date_signal', $date);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id_user', $id);
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
<!-- Tableau -->
  <table>
    <thead>
      <tr>
        <th>Nom</th>
        <th>Motif</th>
        <th>Adresse</th>
        <th>Date et Heure</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody id="signalTable">
        <?php foreach ($sign as $signal) : ?>
      <tr>
                <td><?= $signal['nom_user'] ?></td>
                <td><?= $signal['motif'] ?></td>
                <td><?= $signal['adresse'] ?></td>
                <td><?= $signal['date_signal'] ?></td>
                <td><?= $signal['description'] ?></td>
            
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>