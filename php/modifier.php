<?php
try {
    $cnx = new PDO('mysql:host=localhost;dbname=waste_collect', 'root', '');
    $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if (!isset($_GET['id_pt'])) {
    die("ID manquant !");
}

$id = $_GET['id_pt'];

// Récupération des données du point
$stmt = $cnx->prepare("SELECT * FROM point_collecte WHERE id_pt = :id_pt");
$stmt->bindParam(':id_pt', $id, PDO::PARAM_INT);
$stmt->execute();
$point = $stmt->fetch();

if (!$point) {
    die("Point introuvable !");
}

// Mise à jour  du formulaire



if (isset($_POST['submit'])) {
      $nom=$_POST['nom_pt'];
    $lieu=$_POST['lieu'];
    $capacite=$_POST['capacite'];
    $Etat=$_POST['Etat'];
     $date=$_POST['date_vidange'];

try{
    $update = $cnx->prepare("UPDATE point_collecte SET nom_pt = :nom_pt, lieu = :lieu, capacite = :capacite, Etat = :Etat, date_vidange = :date_vidange WHERE id_pt = :id_pt");
    $update->bindParam(':nom_pt', $nom);
    $update->bindParam(':lieu', $lieu);
    $update->bindParam(':capacite', $capacite);
    $update->bindParam(':Etat', $Etat);
    $update->bindParam(':date_vidange', $date);
    $update->bindParam(':id_pt', $id);
    $update->execute();

    header("Location: ../Pages/point.php");
} catch (PDOException $e) {
        echo"Erreur d'insertion des points".$e->getMessage();
    }

    
    exit;
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
    <meta charset="UTF-8">
    <title>Modifier un point</title>
    <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>WASTE Collect</title>
  <link rel="stylesheet" href="../CSS/dashstyle.css" />
  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
    
    .form-container {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.2);
      width: 600px;
      max-width: 100%;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px 30px;
    }
    .form-group {
      display: flex;
      flex-direction: column;
    }
    label {
      font-weight: bold;
      margin-bottom: 6px;
      color: #444;
    }
    input, select {
      padding: 8px 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
      outline: none;
    }
    input:focus, select:focus {
      border-color: #cfa13b;
      box-shadow: 0 0 4px rgba(207,161,59,0.6);
    }
    .btn {
      grid-column: 1 / span 2;
      justify-self: center;
      padding: 10px 25px;
      font-size: 16px;
      background: #2f4f4f;
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.3s;
    }
    .btn:hover {
      background: #2f4f4f;
    }

    /* Responsive mobile */
    @media (max-width: 768px) {
      .form-grid {
        grid-template-columns: 1fr;
      }
      .btn {
        grid-column: 1;
        width: 100%;
      }
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
      <?php if(($role==="chauffeur")){?>
      
      <a class="menu-item" href="../php/phpdash.php">
        <i class="fa-solid fa-house" style="color: #cfa13b"></i><span>MES TOURNEES DE RAMASSAGE</span>
      </a>
      
      <a class="menu-item" href="../php/info.php">
        <i class="fa-solid fa-truck" style="color: #cfa13b"></i>
        <span>MES INFORMATIONS</span>
      </a>
      <?php } ?>
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
      <div class="form-container">
    <h2>Modifier le point</h2>
    <form action="../php/point.php" method="POST"  class="form-grid">
        <div class="form-group">
        <label for="nom_pt">Nom du point</label>
        <input type="text" id="nom_pt" name="nom_pt" value="<?= htmlspecialchars($point['nom_pt']) ?>">
      </div>
                         <div class="form-group">
        <label for="capacite">Capacité</label>
        <input type="number" id="capacite" name="capacite" value="<?= htmlspecialchars($point['capacite']) ?>">
      </div>

      <div class="form-group">
        <label for="lieu">Lieu</label>
        <input type="text" id="lieu" name="lieu" value="<?= htmlspecialchars($point['lieu']) ?>">
      </div>

      <div class="form-group">
        <label for="Etat">État actuel</label>
        <select id="Etat" name="Etat">
          <option value="Vide" <?= $point['Etat']=='Vide'?'selected':'' ?>>Vide</option>
          <option value="Rempli" <?= $point['Etat']=='Rempli'?'selected':'' ?>>Rempli</option>
        </select>
      </div>

      <div class="form-group" style="grid-column: 1 / span 2;">
        <label for="date_vidange">Date de vidange</label>
        <input type="datetime-local" id="date_vidange" name="date_vidange" value="<?= htmlspecialchars($point['date_vidange']) ?>">
      </div>

      <button type="submit" class="btn" name="submit">Modifier</button>
    </form>
  </div>
</body>
</html>
