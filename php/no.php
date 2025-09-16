<?php
 session_start();

if(!$_SESSION['id_user']){
  header('Location:../Pages/login.html');
  exit;
} 

$nom=$_SESSION["nom_user"];
$role=$_SESSION["role"];

// Connexion à la base
try {
    $cnx= new PDO("mysql:host=localhost;dbname=waste_collect","root","");
    $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e){
    echo"Erreur de connexion :".$e->getMessage();
}

// Récupération des stats par mois
$sql = "SELECT YEAR(date_signal) AS annee, MONTH(date_signal) AS mois, COUNT(id_sign) AS total_signalements
        FROM signalement
        WHERE YEAR(date_signal) >= YEAR(CURDATE())
        GROUP BY annee, mois
        ORDER BY annee ASC, mois ASC";

$stmt = $cnx->query($sql);
$resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tableau des noms de mois
$moisNoms = [
    1 => "Janvier", 2 => "Février", 3 => "Mars", 4 => "Avril",
    5 => "Mai", 6 => "Juin", 7 => "Juillet", 8 => "Août",
    9 => "Septembre", 10 => "Octobre", 11 => "Novembre", 12 => "Décembre"
];

$labels = [];
$values = [];

$anneeCourante = date("Y");

// On prépare un tableau mois => 0 par défaut
$moisSignalements = [];
for ($m = 1; $m <= 12; $m++) {
    $moisSignalements[$m] = 0;
}

// On remplit avec les vrais résultats SQL
foreach ($resultats as $row) {
    if ($row['annee'] == $anneeCourante) { 
        $moisSignalements[$row['mois']] = $row['total_signalements'];
    }
}

// Construire les labels et valeurs
foreach ($moisSignalements as $m => $total) {
    $labels[] = $moisNoms[$m] . " " . $anneeCourante;
    $values[] = $total;
}

// Récupérer l’année sélectionnée via GET (par défaut année courante)
$anneeSelect = isset($_GET['annee']) ? (int)$_GET['annee'] : date("Y");

// Liste des années disponibles dans signalement
$sqlYears = "SELECT DISTINCT YEAR(date_signal) as annee FROM signalement ORDER BY annee DESC";
$yearsStmt = $cnx->query($sqlYears);
$years = $yearsStmt->fetchAll(PDO::FETCH_COLUMN);

// --- Points de collecte les plus fréquentés (TOP 1 par mois) ---
$sqlTopPoints = "
    SELECT mois, nom_pt, total FROM (
        SELECT 
            MONTH(signalement.date_signal) AS mois, 
            point_collecte.nom_pt, 
            COUNT(signalement.id_sign) AS total,
            ROW_NUMBER() OVER (PARTITION BY MONTH(signalement.date_signal) ORDER BY COUNT(signalement.id_sign) DESC) 
        FROM signalement
        JOIN point_collecte ON signalement.id_pt = point_collecte.id_pt
        WHERE YEAR(signalement.date_signal) = :annee
        GROUP BY mois, point_collecte.nom_pt
    ) 
   
    ORDER BY t.mois;
";

$stmtTop = $cnx->prepare($sqlTopPoints);
$stmtTop->execute(['annee' => $anneeSelect]);
$resTop = $stmtTop->fetchAll(PDO::FETCH_ASSOC);

// Organiser les données pour Chart.js
$labelsTop = [];
$valuesTop = [];
$nomsPtTop = [];

for ($m = 1; $m <= 12; $m++) {
    $labelsTop[$m] = $moisNoms[$m];  
    $valuesTop[$m] = 0;              
    $nomsPtTop[$m] = "";             
}

foreach ($resTop as $row) {
    $labelsTop[$row['mois']] = $moisNoms[$row['mois']];
    $valuesTop[$row['mois']] = $row['total'];
    $nomsPtTop[$row['mois']] = $row['nom_pt'];
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>WASTE Collect - Statistiques</title>
  <link rel="stylesheet" href="../CSS/dashstyle.css" />
  <link rel="stylesheet" href="../CSS/configs.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
      <div class="header-right">
        <div class="app-name"><?php echo $nom; ?></div>
      </div>
    </header>
<div class="charts-intro">
  <p><strong>Introduction :</strong> Le premier graphique ci-dessous montre l’évolution des signalements par mois sur l’année en cours.  
  Le second graphique affiche le point de collecte le plus fréquemment signalé pour chaque mois de l’année choisie.</p>
</div>
    <div class="charts-container">
    <!-- CONTENU -->
    <div class="card">
  <h3>Signalements par mois (année en cours)</h3>
  <canvas id="signalementsMoisChart"></canvas>
</div>

<script>
  const ctxMois = document.getElementById('signalementsMoisChart').getContext('2d');
  const signalementsMoisChart = new Chart(ctxMois, {
      type: 'line',
      data: {
          labels: <?php echo json_encode($labels); ?>,
          datasets: [{
              label: 'Nombre de signalements',
              data: <?php echo json_encode($values); ?>,
              backgroundColor: '#cfa13b',
              borderColor: '#cfa13b',
              fill: false
          }]
      },
      options: {
          responsive: true,
          plugins: {
              legend: {
                  display: false
              },
              title: {
                  display: true,
                  text: 'Signalements par mois'
              }
          },
          scales: {
              y: {
                  beginAtZero: true,
                  ticks: {
                      stepSize: 10
                  }
              }
          }
      }
  });
</script>

<style>
.card {
  background: #fff;
  border-radius: 10px;
  padding: 20px;
  margin: 20px auto;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  width: 90%;
  max-width: 1000px;
}

.card h3 {
  margin-bottom: 20px;
  color: #123;
}

#signalementsChart {
  width: 100% !important;
  height: 400px !important;
}
.charts-intro {
  width: 90%;
  margin: 20px auto;
  font-size: 15px;
  color: #333;
  background: #f8f9fa;
  border-left: 4px solid #cfa13b;
  padding: 10px 15px;
  border-radius: 6px;
}

.charts-container {
  display: flex;
  justify-content: space-between;
  gap: 20px;
  flex-wrap: wrap;
  width: 90%;
  margin: 0 auto;
}

.charts-container .card {
  flex: 1 1 48%;
  max-width: 48%;
}

@media (max-width: 900px) {
  .charts-container .card {
    flex: 1 1 100%;
    max-width: 100%;
  }
}

.intro-text {
  font-size: 14px;
  margin-bottom: 10px;
  color: #555;
}
</style>

<div class="card">
  <h3>Point de collecte le plus fréquenté par mois</h3>
  <form method="get" onchange="this.submit()">
    <label for="annee">Année :</label>
    <select name="annee" id="annee">
      <?php foreach ($years as $year): ?>
        <option value="<?= $year ?>" <?= ($year == $anneeSelect) ? "selected" : "" ?>>
          <?= $year ?>
        </option>
      <?php endforeach; ?>
    </select>
  </form>
  <canvas id="topPointsChart"></canvas>
</div>

<script>
  const ctxTop = document.getElementById('topPointsChart').getContext('2d');
  new Chart(ctxTop, {
    type: 'bar',
    data: {
      labels: <?= json_encode(array_values($labelsTop)) ?>,
      datasets: [{
        label: 'Signalements (point le plus fréquenté)',
        data: <?= json_encode(array_values($valuesTop)) ?>,
        backgroundColor: '#cfa13b' // ✅ une seule couleur
      }]
    },
    options: {
      responsive: true,
      plugins: {
        tooltip: {
          callbacks: {
            label: function(context) {
              let mois = context.label;
              let valeur = context.formattedValue;
              let noms = <?= json_encode(array_values($nomsPtTop)) ?>;
              return mois + ' : ' + valeur + ' signalements (' + noms[context.dataIndex] + ')';
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1
          }
        }
      }
    }
  });
</script>
</div>
</body>
</html>