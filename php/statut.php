<?php 
session_start();

// Connexion à la base de donnée
try {
    $cnx = new PDO("mysql:host=localhost;dbname=waste_collect", "root", "");
    $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("Erreur BD : " . $e->getMessage());
}

// ✅ Vérification session
if(!isset($_SESSION['id_user'])){
  header('Location:../Pages/login.html');
  exit;
} 

$nom  = $_SESSION["nom_user"];
$role = $_SESSION["role"];
//récupère les signalements non traités
$non_traites = $cnx->query("SELECT * FROM signalement WHERE statut='non_traite' ORDER BY date_signal DESC")->fetchAll(PDO::FETCH_ASSOC);

// ✅ Mettre à jour un signalement en "traite"
if (isset($_POST['id_sign'])) {
    $id_sign = intval($_POST['id_sign']);
    $update = $cnx->prepare("UPDATE signalement SET statut='traite' WHERE id_sign=?");
    $update->execute([$id_sign]);
}

// ✅ Récupérer les signalements non traités + infos utilisateur + point collecte
$non_traites = $cnx->query("
    SELECT s.*, u.nom_user, u.role, p.nom_pt 
    FROM signalement s
    JOIN utilisateur u ON s.id_user = u.id_user
    JOIN point_collecte p ON s.id_pt = p.id_pt
    WHERE s.statut='non_traite'
    ORDER BY s.date_signal DESC
")->fetchAll(PDO::FETCH_ASSOC);

// ✅ Récupérer les signalements traités
$traites = $cnx->query("
    SELECT s.*, u.nom_user, u.role, p.nom_pt 
    FROM signalement s
    JOIN utilisateur u ON s.id_user = u.id_user
    JOIN point_collecte p ON s.id_pt = p.id_pt
    WHERE s.statut='traite'
    ORDER BY s.date_signal DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>WASTE Collect</title>
  <link rel="stylesheet" href="../CSS/dashstyle.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <style>
    .tabs { display: flex; gap: 20px; margin-bottom: 10px; }
    .tab-btn { cursor:pointer; padding:10px; font-weight:bold; border-bottom:2px solid transparent; }
    .tab-btn.active { border-color:#cfa13b; color:#cfa13b; }
    .notif-item { background:#f8f9fa; padding:10px; margin:5px 0; border-radius:5px; }
    .notif-date { font-size:12px; color:gray; }
    button.statut-btn { background:#cfa13b; border:none; padding:5px 10px; border-radius:5px; color:white; cursor:pointer; }
  </style>
</head>
<body>
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="brand">
      <div class="logo-circle">
        <img src="../Images/1.png" alt="WASTE Collect" />
      </div>
    </div>
    <nav class="menu">
      <a class="menu-item" href="../php/phpdash.php">
        <i class="fa-solid fa-house" style="color: #cfa13b"></i><span>Mes Tournées de Ramassage</span>
      </a>
      <a class="menu-item" href="../php/info.php">
        <i class="fa-solid fa-user" style="color: #cfa13b"></i><span>Mes Informations </span>
      </a>
      <a class="menu-item" href="../php/statut.php">
        <i class="fa-solid fa-list" style="color: #cfa13b"></i><span>Statuts de mes Tournées </span>
      </a>
      <a class="menu-item" href="../php/logout.php">
        <i class="fa-solid fa-arrow-right-from-bracket" style="color: #cfa13b"></i><span>Déconnexion</span>
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

    <h2>Suivi des signalements</h2>

    <!-- Onglets -->
    <div class="tabs">
      <div class="tab-btn active" onclick="showTab('non')">Signalements non traités</div>
      <div class="tab-btn" onclick="showTab('oui')">Signalements traités</div>
    </div>

    <!-- Signalements non traités -->
    <div id="tab-non">
      <?php if (!empty($non_traites)): ?>
        <?php foreach($non_traites as $s): ?>
          <div class="notif-item">
            <strong><?= htmlspecialchars($s['nom_user']) ?></strong> (<?= htmlspecialchars($s['role']) ?>) 
            <strong><?= htmlspecialchars($s['nom_pt']) ?></strong><br>
            <span class="notif-date"><?= date("d/m/Y H:i", strtotime($s['date_signal'])) ?></span><br>
            <form method="post" style="display:inline;">
              <input type="hidden" name="id_sign" value="<?= $s['id_sign'] ?>">
              <button type="submit" class="statut-btn">Non traité</button>
            </form>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Aucun signalement non traité.</p>
      <?php endif; ?>
    </div>

    <!-- Signalements traités -->
    <div id="tab-oui" style="display:none;">
      <?php if (!empty($traites)): ?>
        <?php foreach($traites as $s): ?>
          <div class="notif-item">
            <strong><?= htmlspecialchars($s['nom_user']) ?></strong> (<?= htmlspecialchars($s['role']) ?>) → 
            <strong><?= htmlspecialchars($s['nom_pt']) ?></strong><br>
            <span class="notif-date"><?= date("d/m/Y H:i", strtotime($s['date_signal'])) ?></span><br>
            ✅ <em>Traité</em>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Aucun signalement traité.</p>
      <?php endif; ?>
    </div>
  </div>

<script>
function showTab(type){
  document.getElementById('tab-non').style.display = (type==='non') ? 'block':'none';
  document.getElementById('tab-oui').style.display = (type==='oui') ? 'block':'none';
  document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
  document.querySelector(`.tab-btn:nth-child(${type==='non'?1:2})`).classList.add('active');
}
</script>
</body>
</html>
