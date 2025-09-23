<?php
//connexion à la base de donnée
try {
    $cnx= new PDO("mysql:host=localhost;dbname=waste_collect","root","");
}
catch(PDOException $e){
    echo"Erreur de connexion à la base de donnée veuillez réesayer plus tard:".$e->getMessage();
}

// Ajouter un point
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $nom=$_POST['nom_pt'];
    $lieu=$_POST['lieu'];
    $capacite=$_POST['capacite'];
    $Etat=$_POST['Etat'];
    $date=$_POST['date_vidange'];
    $lat = (float) $_POST['latitude'];
    $lon = (float) $_POST['longitude'];

    try {
        $stmt=$cnx->prepare('INSERT INTO point_collecte(nom_pt,lieu,capacite,Etat,date_vidange,latitude,longitude) VALUES (:nom_pt,:lieu,:capacite,:Etat,:date_vidange,:latitude,:longitude)');
        $stmt->bindParam(':nom_pt', $nom);
        $stmt->bindParam(':lieu', $lieu);
        $stmt->bindParam(':capacite', $capacite);
        $stmt->bindParam(':Etat', $Etat);
        $stmt->bindParam(':date_vidange', $date);
        $stmt->bindParam(':latitude', $lat);
        $stmt->bindParam(':longitude', $lon);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Erreur d'insertion des points : ".$e->getMessage();
    }
}

// Récupérer tous les points
try{
    $sql="SELECT * FROM point_collecte";
    $stmt=$cnx->prepare($sql);
    $stmt->execute();
    $points=$stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e){
    echo"Erreur: ".$e->getMessage();
}

// Supprimer un point
if (isset($_GET['supprimerid_pt'])) {
    $id = $_GET['supprimerid_pt'];
    $requte = $cnx->prepare("DELETE FROM point_collecte WHERE id_pt = :id_pt");
    $requte->bindParam(':id_pt', $id);
    $requte->execute();
    header('Location: point.php');
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
  <link rel="stylesheet" href="../CSS/camion.css" />
  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
  <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <style>
    body { font-family: Arial, sans-serif; background: #f5f6fa; margin: 0; padding: 0; }
    h2 { text-align: center; color: #333; }
    .btn { padding: 8px 15px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; transition: 0.3s; }
    .btn-info { background: #2f4f4f; color: white; }
    .btn-info:hover { background: #3a6161; }
    .btn-edit { background: #2f4f4f; color: white; font-size: 12px; padding: 6px 12px; margin-right: 5px; border-radius: 4px; }
    .btn-edit:hover { background: #2f4f4f; }
    .btn-delete { background: #dc3545; color: white; font-size: 12px; padding: 6px 12px; border-radius: 4px; }
    .btn-delete:hover { background: #c82333; }
    .containr-btn { display: flex; justify-content: flex-end; margin: 15px; }
    .modal { position: fixed; z-index: 10; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); padding-top: 60px; display:none; }
    .modal-content { background: #fff; margin: auto; padding: 25px; border-radius: 10px; width: 600px; max-width: 95%; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
    .close { float: right; font-size: 28px; font-weight: bold; color: #333; cursor: pointer; }
    .close:hover { color: #dc3545; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px 30px; margin-top: 20px; }
    .form-group { display: flex; flex-direction: column; }
    label { font-weight: bold; margin-bottom: 6px; color: #444; }
    input, select { padding: 8px 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
    input:focus, select:focus { border-color: #cfa13b; box-shadow: 0 0 4px rgba(207,161,59,0.6); }
    .form-actions { grid-column: 1 / span 2; text-align: center; }
    .form-actions button { background: #2f4f4f; color: white; padding: 10px 20px; font-size: 15px; border: none; border-radius: 6px; }
    .form-actions button:hover { background: #2f4f4f; }

    table { width: 95%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.1); margin: auto; }
    th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
    th { background: #f4f4f4; text-transform: uppercase; font-size: 13px; color: #666; }
    tr:hover { background: #f9f9f9; }

    .etat-vide { color: green; font-weight: bold; }
    .etat-rempli { color: red; font-weight: bold; }

    /* Responsive */
    @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } .form-actions { grid-column: 1; } }

    /* Masquer la recherche native DataTables */
    .dataTables_filter { display: none !important; }

    /* Carte */
    #mapSelect { height:300px; border:1px solid #ccc; border-radius:6px; }
    .btn-route { display: inline-block; margin-top: 5px; padding: 6px 12px; background: #2f4f4f; color: white; text-decoration: none; border-radius: 6px; cursor: pointer; }
    .btn-route:hover { background: #758687; }
  </style>
</head>
<body>
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="brand">
      <div class="logo-circle"><img src="../Images/1.png" alt="WASTE Collect" /></div>
    </div>
    <nav class="menu">
      <a class="menu-item" href="../Pages/dashboard.php"><i class="fa-solid fa-house" style="color: #cfa13b"></i><span>Accueil</span></a>
      <a class="menu-item" href="../Pages/point.php"><i class="fa-solid fa-calendar-check" style="color: #cfa13b"></i><span>Gestion des Points de Collecte</span></a>
      <a class="menu-item" href="../php/tourner.php"><i class="fa-solid fa-truck" style="color: #cfa13b"></i><span>Tournées de ramassage</span></a>
      <?php if($role==="administrateur"){?>
      <a class="menu-item" href="../php/signale.php"><i class="fa-solid fa-calendar-check" style="color: #cfa13b"></i><span>Gestion des Signalements</span></a>
      <a class="menu-item" href="../php/camion.php"><i class="fa-solid fa-truck" style="color: #cfa13b"></i><span>Gestion des chauffeurs et camions</span></a>
      <a class="menu-item" href="../php/stat.php"><i class="fa-solid fa-chart-column" style="color: #cfa13b"></i><span>Analyse Statistiques</span></a>
      <a class="menu-item" href="../php/config.php"><i class="fa-solid fa-gears" style="color: #cfa13b"></i><span>Configuration</span></a>
      <a class="menu-item" href="../php/notif.php"><i class="fa-solid fa-bell" style="color: #cfa13b"></i><span>Notifications</span></a>
      <?php } ?>
      <a class="menu-item" href="../php/logout.php"><i class="fa-solid fa-arrow-right-from-bracket" style="color: #cfa13b"></i><span>Déconnexion</span></a>
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
        <div class="bell-wrap"><img src="../Images/notification.png" alt="Notifications" /></div>
        <div class="app-name"><?php echo $nom; ?></div>
      </div>
    </header>

    <div>
      <h2>POINTS DE COLLECTE</h2>
      <div class="containr-btn">
        <button id="Btn" class="btn btn-info ajt" onclick="document.getElementById('id01').style.display='block'">+ Ajouter un point</button>
      </div>

      <!-- Modal Ajouter -->
      <div id="id01" class="modal">
        <div class="modal-content">
          <span class="close" onclick="document.getElementById('id01').style.display='none'">&times;</span>
          <h3>Ajouter un point</h3>
          <form action="#" method="POST" class="form-grid">
            <div class="form-group"><label>Nom du point</label><input type="text" name="nom_pt" required></div>
            <div class="form-group"><label>Capacité</label><input type="number" name="capacite" required></div>
            <div class="form-group"><label>Lieu</label><input type="text" name="lieu" required></div>
            <div class="form-group"><label>État actuel</label>
              <select name="Etat"><option value="vide">Vide</option><option value="rempli">Rempli</option></select>
            </div>
            <div class="form-group" style="grid-column:1/span 2;">
              <label>Date de vidange</label>
              <input type="datetime-local" name="date_vidange" value="<?php echo date('Y-m-d\TH:i'); ?>">
            </div>
            <div class="form-group"><label>Latitude</label><input type="text" name="latitude" id="latitude" placeholder="Cliquez sur la carte" readonly required></div>
            <div class="form-group"><label>Longitude</label><input type="text" name="longitude" id="longitude" placeholder="Cliquez sur la carte" readonly required></div>
            <div class="form-group" style="grid-column:1/span 2;"><label>Choisissez la position du point sur la carte</label><div id="mapSelect"></div></div>
            <div class="form-actions"><button type="submit">Ajouter</button></div>
          </form>
        </div>
      </div>

      <!-- Tableau Points -->
      <table id="pointsTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nom du point</th>
            <th>Lieu</th>
            <th>Capacité</th>
            <th>État</th>
            <th>Date de vidange</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($points as $point): ?>
            <tr>
              <td><?= $point['id_pt'] ?></td>
              <td><?= $point['nom_pt'] ?></td>
              <td><?= $point['lieu'] ?></td>
              <td><?= $point['capacite'] ?></td>
              <td class="<?= $point['Etat']=='vide'?'etat-vide':'etat-rempli' ?>"><?= $point['Etat'] ?></td>
              <td><?= $point['date_vidange'] ?></td>
              <td>
                <a href="../php/modifier.php?id_pt=<?= $point['id_pt'] ?>"><button class="btn btn-edit">✏️ Edit</button></a>
                <a href="point.php?supprimerid_pt=<?= $point['id_pt'] ?>" onclick="return confirm('etes vous sur de vouloir supprimer cet element')"><button class="btn btn-delete">🗑️ Delete</button></a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Scripts -->
  <script>
    // Modal
    var modal = document.getElementById('id01');
    window.onclick = function(event) {
      if (event.target == modal) modal.style.display = "none";
    }

    // Carte pour sélectionner latitude/longitude
    var mapSelect = L.map('mapSelect').setView([4.05, 9.7], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(mapSelect);
    var marker;
    mapSelect.on('click', function(e){
      var lat = e.latlng.lat;
      var lon = e.latlng.lng;
      document.getElementById('latitude').value = lat;
      document.getElementById('longitude').value = lon;
      if(marker) mapSelect.removeLayer(marker);
      marker = L.marker([lat,lon]).addTo(mapSelect);
    });

    // DataTable + recherche header
    $(document).ready(function(){
      var tablePoints = $('#pointsTable').DataTable({
        language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json" }
      });
      $('.header input[type="search"]').on('keyup', function(){
        tablePoints.search(this.value).draw();
      });
    });
  </script>
</body>
</html>
