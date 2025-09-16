<?php 
 session_start();
 //connexion à la base de donnée
try {
    $cnx= new PDO("mysql:host=localhost;dbname=waste_collect","root","");
}
catch(PDOException $e){
    echo"Erreur de connexion à la base de donnée veuillez réesayer plutard:".$e->getMessage();
}
/* Récupération des signalements avec utilisateur + point_collecte
$sql = "SELECT * FROM signalement INNER JOIN utilisateur ON signalement.id_user = utilisateur.id_user INNER JOIN point_collecte ON signalement.id_pt = point_collecte.id_pt
    ORDER BY signalement.date_signal DESC
    LIMIT 10
";
$stmt = $cnx->query($sql);
$signalements = $stmt->fetchAll();*/



if(!$_SESSION['id_user']){
  header('Location:../Pages/login.html');


} 
$nom=$_SESSION["nom_user"];
$role=$_SESSION["role"];


// Marquer comme lu si on clique (Ajax)
if(isset($_GET['read_id'])){
    $id = intval($_GET['read_id']);
    $cnx->prepare("UPDATE signalement SET lu=1 WHERE id_sign=?")->execute([$id]);
    exit;
}

// Supprimer en masse
if(isset($_POST['delete_ids'])){
    $ids = $_POST['delete_ids'];
    if(!empty($ids)){
        $in = str_repeat('?,', count($ids)-1) . '?';
        $stmt = $cnx->prepare("DELETE FROM signalement WHERE id_sign IN ($in)");
        $stmt->execute($ids);
    }
    header("Location: notif.php?tab=lues");
    exit;
}

// Onglet actif
$tab = isset($_GET['tab']) && $_GET['tab']=="lues" ? "lues" : "nonlues";

if($tab=="lues"){
    $sql = "SELECT * FROM signalement 
            INNER JOIN utilisateur ON signalement.id_user = utilisateur.id_user 
            INNER JOIN point_collecte ON signalement.id_pt = point_collecte.id_pt
            WHERE lu=1
            ORDER BY signalement.date_signal DESC";
}else{
    $sql = "SELECT * FROM signalement 
            INNER JOIN utilisateur ON signalement.id_user = utilisateur.id_user 
            INNER JOIN point_collecte ON signalement.id_pt = point_collecte.id_pt
            WHERE lu=0
            ORDER BY signalement.date_signal DESC";
}
$stmt = $cnx->query($sql);
$signalements = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <style>
    /* Style des onglets façon Configuration */
    .tabs {
      display: flex;
      border-bottom: 2px solid #ddd;
      margin-bottom: 1rem;
    }
    .tab-link {
      padding: 0.6rem 1.2rem;
      text-decoration: none;
      color: #333;
      font-weight: bold;
      border-bottom: 3px solid transparent;
      transition: 0.3s;
    }
    .tab-link:hover {color: #cfa13b;}
    .tab-link.active {
      border-bottom: 3px solid #cfa13b;
      color: #cfa13b;
    }

    .notif-item {
      border:1px solid #ddd; border-left:4px solid #cfa13b;
      padding:0.8rem; margin-bottom:0.5rem; border-radius:6px; cursor:pointer;
      background:#fafafa;
    }
    .notif-item:hover{background:#f0f0f0;}
    .notif-check {margin-right:10px;}

    /* MODAL */
    .modal {display:none; position:fixed; top:0; left:0; width:100%; height:100%;
      background:rgba(0,0,0,0.6); align-items:center; justify-content:center;}
    .modal-content {
      background:white; padding:1rem 1.5rem; border-radius:8px;
      width:400px; max-width:90%;
    }
    .modal-close {float:right; cursor:pointer; font-size:18px;}
    .actions {margin-top:1rem;}
    .btn {padding:0.4rem 0.8rem; border:none; cursor:pointer; border-radius:4px;}
    .btn-danger {background:red; color:white;}
    .btn-select {background:#444; color:white;}
  </style>
      <!-- Onglets -->
  <div class="tabs">
    <a href="notif.php?tab=nonlues" class="tab-link <?= $tab=="nonlues"?'active':'' ?>">Notifications non lues</a>
    <a href="notif.php?tab=lues" class="tab-link <?= $tab=="lues"?'active':'' ?>">Notifications lues</a>
  </div>

  <?php if($tab=="lues"): ?>
  <form method="post">
  <?php endif; ?>

  <?php if(count($signalements)>0): ?>
    <?php foreach($signalements as $s): ?>
      <div class="notif-item" data-id="<?= $s['id_sign'] ?>" 
           data-motif="<?= htmlspecialchars($s['motif']) ?>" 
           data-desc="<?= htmlspecialchars($s['description']) ?>" 
           data-adr="<?= htmlspecialchars($s['adresse']) ?>" 
           data-date="<?= date("d/m/Y H:i", strtotime($s['date_signal'])) ?>" 
           data-user="<?= htmlspecialchars($s['nom_user']).' ('.$s['role'].')' ?>" 
           data-pt="<?= htmlspecialchars($s['nom_pt']) ?>">
        <?php if($tab=="lues"): ?>
          <input type="checkbox" class="notif-check" name="delete_ids[]" value="<?= $s['id_sign'] ?>">
        <?php endif; ?>
        <strong><?= htmlspecialchars($s['nom_user']) ?></strong> (<?= $s['role'] ?>) → 
        <strong><?= htmlspecialchars($s['nom_pt']) ?></strong>
        <div style="font-size:12px;color:gray;"><?= date("d/m/Y H:i", strtotime($s['date_signal'])) ?></div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>Aucune notification trouvée.</p>
  <?php endif; ?>

  <?php if($tab=="lues"): ?>
    <div class="actions">
      <button type="button" class="btn btn-select" onclick="toggleAll()">Tout sélectionner</button>
      <button type="submit" class="btn btn-danger">Supprimer</button>
    </div>
  </form>
  <?php endif; ?>
</div>

<!-- MODAL -->
<div class="modal" id="notifModal">
  <div class="modal-content">
    <span class="modal-close" onclick="closeModal()">&times;</span>
    <h3>Détails</h3>
    <p id="mUser"></p>
    <p id="mPt"></p>
    <p><strong>Motif:</strong> <span id="mMotif"></span></p>
    <p><strong>Description:</strong> <span id="mDesc"></span></p>
    <p><strong>Adresse:</strong> <span id="mAdr"></span></p>
    <p><strong>Date:</strong> <span id="mDate"></span></p>
  </div>
</div>

<script>
function closeModal(){document.getElementById("notifModal").style.display="none";}
document.querySelectorAll(".notif-item").forEach(el=>{
  el.addEventListener("click",function(e){
    if(e.target.type==="checkbox") return; // éviter conflit avec case
    document.getElementById("mUser").textContent=this.dataset.user;
    document.getElementById("mPt").textContent="Point: "+this.dataset.pt;
    document.getElementById("mMotif").textContent=this.dataset.motif;
    document.getElementById("mDesc").textContent=this.dataset.desc;
    document.getElementById("mAdr").textContent=this.dataset.adr;
    document.getElementById("mDate").textContent=this.dataset.date;
    document.getElementById("notifModal").style.display="flex";
    // marquer comme lu via Ajax
    fetch("notif.php?read_id="+this.dataset.id);
    this.style.opacity="0.5";
  });
});

function toggleAll(){
  document.querySelectorAll(".notif-check").forEach(c=>c.checked=!c.checked);
}
</script>
</body>
</html>