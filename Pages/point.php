
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
    $nom=$_POST['nom_pt'];
    $lieu=$_POST['lieu'];
    $capacite=$_POST['capacite'];
    $Etat=$_POST['Etat'];
     $date=$_POST['date_vidange'];

    try {
        $stmt=$cnx->prepare('INSERT INTO point_collecte(nom_pt,lieu,capacite,Etat,date_vidange)VALUES (:nom_pt, :lieu, :capacite, :Etat, :date_vidange)');
        $stmt->bindParam(':nom_pt', $nom);
        $stmt->bindParam(':lieu', $lieu);
        $stmt->bindParam(':capacite', $capacite);
        $stmt->bindParam(':Etat', $Etat);
        $stmt->bindParam(':date_vidange', $date);
        $stmt->execute();
       // header('Location: ../Pages/point.php');
    } catch (PDOException $e) {
        echo"Erreur d'insertion des points dans la base de donnée".$e->getMessage();
    }

}
try{


$sql="SELECT * FROM point_collecte";
$stmt=$cnx->prepare($sql);
if($stmt===false){
    throw new PDOException("Erreur lors de la preparation de la requete");
}
$stmt->execute();
$points=$stmt->fetchAll();
if($points===false){
    throw new PDOException("Erreur lors de la recuperation de la requete");
}

}
catch(PDOException $e){
    echo"Erreur:".$e->getMessage();
} 


if (isset($_GET['supprimerid_pt'])) {
        $id = $_GET['supprimerid_pt'];
        // Supprimer l'enregistrement du point de la base de données
        $requte = $cnx->prepare("DELETE FROM point_collecte WHERE id_pt = :id_pt");
        $requte->bindParam(':id_pt', $id);
        $requte->execute();
        header('Location: ../Pages/point.php');
    
    
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
  <link rel="stylesheet" href="../CSS/pointstyl.css" />
  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  
    <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f6fa;
      margin: 20px;
    }
    h2 {
      color: #333;
    }
    .btn {
      padding: 8px 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
    }

    .containr-btn {
      display: flex;
      flex-direction: row-reverse;
    }
    .btn-info {
      background: #2f4f4f;
      color: white;
      width: 10%;
      display: flex;
      flex-direction: row-reverse;
    }
    .btn-info:hover {
      background: #3a6161;
    }
    .btn-success {
      background: #28a745;
      color: white;
    }
    .btn-success:hover {
      background: #218838;
    }
    .btn-edit {
      background: #3a6161;
      color: white;
      font-size: 12px;
      padding: 5px 10px;
      margin-right: 5px;
      width: 50%;
    }
    .btn-edit:hover {
      background: #0056b3;
    }
    .btn-delete {
      background: #dc3545;
      color: white;
      font-size: 12px;
      padding: 5px 10px;
      width: 50%;
    }
    .btn-delete:hover {
      background: #c82333;
    }
    #formContainer {
      display: none;
      margin-top: 15px;
      padding: 15px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    #formContainer input {
      padding: 8px;
      margin: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
      width: 180px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background: #fff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }
    th {
      background: #f0f0f0;
      text-transform: uppercase;
      font-size: 13px;
      color: #666;
    }
    tr:hover {
      background: #f9f9f9;
    }
    * {box-sizing: border-box}

/* Set a style for all buttons */
button {
  background-color: #04AA6D;
  color: white;
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  cursor: pointer;
  opacity: 0.9;
}

button:hover {
  opacity:1;
}

/* Float cancel and delete buttons and add an equal width */
.cancelbtn, .deletebtn {
  float: left;
  width: 50%;
}

/* Add a color to the cancel button */
.cancelbtn {
  background-color: #ccc;
  color: black;
}

/* Add a color to the delete button */
.deletebtn {
  background-color: #f44336;
}

/* Add padding and center-align text to the container */
.container {
  padding: 16px;
  text-align: center;
}

/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: #474e5d;
  padding-top: 50px;
}

/* Modal Content/Box */
.modal-content {
  background-color: #fefefe;
  margin: 5% auto 15% auto; /* 5% from the top, 15% from the bottom and centered */
  border: 1px solid #888;
  width: 80%; /* Could be more or less, depending on screen size */
}

/* Style the horizontal ruler */
hr {
  border: 1px solid #f1f1f1;
  margin-bottom: 25px;
}

/* The Modal Close Button (x) */
.close {
  position: absolute;
  right: 35px;
  top: 15px;
  font-size: 40px;
  font-weight: bold;
  color: #f1f1f1;
}

.close:hover,
.close:focus {
  color: #f44336;
  cursor: pointer;
}

/* Clear floats */
.clearfix::after {
  content: "";
  clear: both;
  display: table;
}

/* Change styles for cancel button and delete button on extra small screens */
@media screen and (max-width: 300px) {
  .cancelbtn, .deletebtn {
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
      <a class="menu-item" href="../Pages/dashboard.php">
        <i class="fa-solid fa-house" style="color: #cfa13b"></i><span>Accueil</span>
      </a>
      <a class="menu-item" href="../Pages/point.html">
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
    <!---->
    <div id="ibtn" class="modal">
            <div class="container">
                <div class="form-box">
                    <h2>FORMULAIRE</h2>
                    <form action="#" method="POST">
                        <input type="text" name="nom_pt" placeholder="Entrer le nom du point de collecte" >
                        <input type="float" name="capacite" placeholder="Entrer la capacité du point">
                        <input type="text" name="lieu" placeholder="Entrer le lieu">
                        <label>Etat actuel:</label>
                        <select name="Etat">
                            <option value="vide">Vide</option>
                            <option value="rempli">Rempli</option>
                            
                        </select>
                        <input type="datetime-local" name="date_vidange"  >
                        <button type="submit">Ajouter</button>
                    </form>
                  </div>
              </div>
     </div>
    <body>
  <h2>POINTS DE COLLECTE</h2>
  <div class="containr-btn">
    <button id="Btn" class="btn btn-info ajt" onclick="document.getElementById('id01').style.display='block'">+ Ajouter un point</button>
  </div>

  
<div id="id01" class="modal">
  <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">×</span>
  <form class="modal-content" action="#" method="POST">
    <div class="container">
      <div class="container">
                <div class="form-box">
                    <h2>FORMULAIRE</h2>
                        <input type="text" name="nom_pt" placeholder="Entrer le nom du point de collecte" >
                        <input type="float" name="capacite" placeholder="Entrer la capacité du point">
                        <input type="text" name="lieu" placeholder="Entrer le lieu">
                        <label>Etat actuel:</label>
                        <select name="Etat">
                            <option value="vide">Vide</option>
                            <option value="rempli">Rempli</option>
                            
                        </select>
                        <input type="datetime-local" name="date_vidange"  value="<?php echo date('Y-m-d\TH:i'); ?>" >
                        <button type="submit">Ajouter</button>
                  </div>
              </div>
              
    </div>
  </form>
</div>

  <!-- Tableau -->
  <table>
    <thead>
      <tr>
        <th></th>
        <th>Nom du point</th>
        <th>Lieu</th>
        <th>Capacite</th>
        <th>Etat</th>
        <th>Date de vidange</th>
        <th></th>
      </tr>
    </thead>
    <tbody id="pointsTable">
        <?php foreach ($points as $point) : ?>
      <tr>
                <td><input type="checkbox"><?= $point['id_pt'] ?></td>
                <td><?= $point['nom_pt'] ?></td>
                <td><?= $point['lieu'] ?></td>
                <td><?= $point['capacite'] ?></td>
                <td><?= $point['Etat'] ?></td>
                <td><?= $point['date_vidange'] ?></td>
            
            <td>
            <a href="../php/modifier.php?id_pt=<?= $point['id_pt'] ?>"><button class="btn btn-edit">✏️ Edit</button></a>
            <a href="point.php?supprimerid_pt=<?= $point['id_pt'] ?>"onclick="return confirm('etes vous sur de vouloir supprimer cet element')"><button class="btn btn-delete">🗑️ Delete</button></a>
            </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
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

    
    
