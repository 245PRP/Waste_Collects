<?php 
//connexion à la base de donnée
try {
    $cnx= new PDO("mysql:host=localhost;dbname=waste_collect","root","");
}
catch(PDOException $e){
    echo"Erreur de connexion à la base de donnée veuillez réesayer plutard:".$e->getMessage();
}
// ajouter un point
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $immatriculation=$_POST['immatriculation'];
    $id=$_POST['chauffeur'];
    
    
  

    try {
        $stmt=$cnx->prepare('INSERT INTO camion(immatriculation,id_user)VALUES (:immatriculation, :id_user)');
        $stmt->bindParam(':immatriculation', $immatriculation);
        $stmt->bindParam(':id_user', $id);
        $stmt->execute();
        //header('Location: ../Pages/signal.html');
    } catch (PDOException $e) {
        echo"Erreur d'insertion des camions dans la base de donnée".$e->getMessage();
    }

}
try{


$sql="SELECT * FROM camion,utilisateur WHERE camion.id_user=utilisateur.id_user" ;
$stmt=$cnx->prepare($sql);
if($stmt===false){
    throw new PDOException("Erreur lors de la preparation de la requete");
}
$stmt->execute();
$cam=$stmt->fetchAll();
if($cam===false){
    throw new PDOException("Erreur lors de la recuperation de la requete");
}

}
catch(PDOException $e){
    echo"Erreur:".$e->getMessage();
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
if (isset($_GET['supprimerid'])) {
        $id = $_GET['supprimerid'];
        // Supprimer l'enregistrement de la base de données
        $requte = $cnx->prepare("DELETE FROM camion WHERE id_cam = :id_cam");
        $requte->bindParam(':id_cam', $id);
        $requte->execute();
        header('Location: ../php/camion.php');
    
    
}


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
  <link rel="stylesheet" href="../CSS/camion.css" />
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

  <div class="form-contenu">
    <form action=../php/camion.php method="POST">
      <div class="form-group">
        <label for="immatriculation">Entrer l'immatriculation du camion</label>
        <input type="text" id="im" name="immatriculation" required>
      </div>
        <div class="form-group">
        <label for="limit">Choisissez un Chauffeur</label>
        <select id="limit" name="chauffeur">
            <?php foreach ($chauf as $chauff) : ?>
          <option value=<?= $chauff['id_user'] ?>><?= $chauff['nom_user'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
       <button type="submit">Ajouter</button>

    </form>
  </div>
  <table>
    <thead>
      <tr>
        <th></th>
        <th>Immatriculation</th>
        <th>Chauffeurs Assignés</th>
        <th></th>
      </tr>
    </thead>
    <tbody id="camionTable">
        <?php foreach ($cam as $camn) : ?>
      <tr>
                <td><input type="checkbox"><?= $camn['id_cam'] ?></td>
                <td><?= $camn['immatriculation'] ?></td>
                <td><?= $camn['nom_user'] ?></td>

                 <td>
            <a href="../php/modifcam.php?id=<?= $camn['id_cam'] ?>"><button class="btn btn-edit">✏️ Edit</button></a>
            <a href="camion.php?supprimerid=<?= $camn['id_cam'] ?>"onclick="return confirm('etes vous sur de vouloir supprimer cet element')"><button class="btn btn-delete">🗑️ Delete</button></a>
            </td>
            
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>























































































































































































































































































































































<!-- Requested Limit 
      <div class="form-group">
        <label for="limit">Requested Limit</label>
        <select id="limit">
          <option>$1,000</option>
          <option>$5,000</option>
          <option>$10,000</option>
        </select>
      </div>
        Account Type 
      <div class="form-group">
        <label>compacteur</label>
        <input type="radio" name="compacteur" id="oi"> <label for="Oui" style="display:inline;">Oui</label>
        <input type="radio" name="compacteur" id="nn" checked> <label for="Non" style="display:inline;">Non</label>
      </div>