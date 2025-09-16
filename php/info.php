<?php
 session_start();
// connexion PDO
try {
    $cnx = new PDO("mysql:host=localhost;dbname=waste_collect", "root", "");
    $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur connexion : " . $e->getMessage());
}

// récupérer infos du chauffeur connecté
$id = $_SESSION['id_user'];
$stmt = $cnx->prepare("SELECT * FROM utilisateur WHERE id_user = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Utilisateur introuvable !");
}
try {
if ($_SERVER["REQUEST_METHOD"] === "POST") {
       // $id = $_POST["id_user"];
        $nom = $_POST["nom_user"];
        $email = $_POST["email"];
        $tel = $_POST["telephone"];
        $lieu = $_POST["lieu"];
        $permis = $_POST["permis"];

        $sql = "UPDATE utilisateur 
                SET nom_user = :nom, email = :email, telephone = :tel, lieu = :lieu, permis = :permis 
                WHERE nom_user = :nom";

        $stmt = $cnx->prepare($sql);
        $stmt->execute([
            ":nom" => $nom,
            ":email" => $email,
            ":tel" => $tel,
            ":lieu" => $lieu,
            ":permis" => $permis
            //":id" => $id
        ]);

        // redirection pour recharger la liste
        header("Location: info.php?success=1");
        exit;
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
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
      
      <a class="menu-item" href="../php/phpdash.php">
        <i class="fa-solid fa-house" style="color: #cfa13b"></i><span>Mes Tournées de Ramassage</span>
      </a>
       <a class="menu-item" href="../php/info.php">
        <i class="fa-solid fa-house" style="color: #cfa13b"></i><span>Mes Informations </span>
      </a>
      
      
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
      <style>
        /* --- header pour éviter que le menu soit coupé --- */
      .header, .header-right { position: relative; overflow: visible; z-index: 2; }

      /* --- dropdown --- */
      .dropdown { position: relative; display: inline-block; }
      .dropbtn { background: none; border: none; cursor: pointer; position: relative; }
      .dropbtn i { font-size: 22px; color: #cfa13b; }

      /* badge total */
      .badg {
        position: absolute; top: -6px; right: -8px;
        background: red; color: #fff; border-radius: 50%;
        padding: 2px 6px; font-size: 12px; font-weight: 700;
      }

      /* menu */
      .dropdown-content {
        display: none;
        position: absolute; right: 0; top: 32px;
        min-width: 240px; background: #fff; color: #fff;
        border-radius: 8px; box-shadow: 0 8px 16px rgba(0,0,0,.25);
        z-index: 9999; padding: 6px 0;
      }
      .dropdown-content.show { display: block; }  /* <= IMPORTANT */

      /* items */
      .dropdown-content a {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 14px; text-decoration: none; color: #000; font-size: 14px;
      }
      .dropdown-content a:hover { background: #ccc; }

      /* badge par ligne */
      .notif-count {
        background: red; color: #fff; border-radius: 12px;
        padding: 2px 8px; font-size: 12px; font-weight: 700;
      }


      </style>
      <script>
        
          function toggleDropdown(e) {
            if (e) e.stopPropagation(); // pour ne pas fermer immédiatement
            const dd = document.getElementById('notifDropdown');
            dd.classList.toggle('show');
          }

          // Fermer si on clique ailleurs
          document.addEventListener('click', function (ev) {
            const dd = document.getElementById('notifDropdown');
            if (!ev.target.closest('.dropdown')) dd.classList.remove('show');
          });

      </script>
    </header>
<style>
    .form-container {
      max-width: 100%;
      background: #f5f8fa;
      padding: 20px 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .form-container h2 {
      margin-bottom: 20px;
      color: #333;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group label {
      display: block;
      font-weight: 600;
      margin-bottom: 6px;
      color: #444;
    }
    .form-group input,
    .form-group select {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 15px;
    }
    .form-actions {
      margin-top: 20px;
    }
    .btn {
      padding: 10px 18px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 15px;
    }
    .btn-primary {
      background: #28a745;
      color: #fff;
    }
    .btn-secondary {
      background: #ccc;
      color: #000;
      margin-left: 8px;
    }
  </style>
</head>
<body>
  <div class="main">
    <div class="form-container">
      <h2>Gestion du Compte</h2>
      <form action="info.php" method="POST">
        <div class="form-group">
          <label for="nom">Nom</label>
          <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['nom_user']) ?>" required>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="form-group">
          <label for="telephone">Téléphone</label>
          <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>" required>
        </div>
        <div class="form-group">
          <label for="role">Rôle</label>
          <select id="role" name="role" disabled>
            <option value="Chauffeur" <?= $user['role'] === 'Chauffeur' ? 'selected' : '' ?>>Chauffeur</option>
            <option value="Administrateur" <?= $user['role'] === 'Administrateur' ? 'selected' : '' ?>>Administrateur</option>
            <option value="Utilisateur" <?= $user['role'] === 'Utilisateur' ? 'selected' : '' ?>>Utilisateur</option>
          </select>
        </div>
        <div class="form-group">
          <label for="domicile">Lieu de domicile</label>
          <input type="text" id="domicile" name="lieu" value="<?= htmlspecialchars($user['lieu']) ?>">
        </div>
        <div class="form-group">
          <label for="permis">Permis</label>
          <input type="text" id="permis" name="permis" value="<?= htmlspecialchars($user['permis']) ?>">
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary">Modifier</button>
          <button type="reset" class="btn btn-secondary">Annuler</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
