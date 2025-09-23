<?php
session_start();

// Connexion à la base de donnée
try {
    $cnx = new PDO("mysql:host=localhost;dbname=waste_collect","root","");
} catch(PDOException $e){
    echo "Erreur de connexion à la base de donnée : ".$e->getMessage();
}

// Récupération des utilisateurs
$sql = "SELECT id_user, nom_user, role, email, telephone, lieu, permis FROM utilisateur";
$stmt = $cnx->query($sql);
$utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le dernier utilisateur inscrit
$sqlDernier = "SELECT * FROM utilisateur ORDER BY id_user DESC LIMIT 1";
$stmtDernier = $cnx->query($sqlDernier);
$dernierUser = $stmtDernier->fetch(PDO::FETCH_ASSOC);

if (isset($_GET['supprimer'])) {
    $nom = $_GET['supprimer'];
    $requte = $cnx->prepare("DELETE FROM utilisateur WHERE nom_user = :nom_user");
    $requte->bindParam(':nom_user', $nom);
    $requte->execute();
    header('Location: ../php/config.php');
}

if(!$_SESSION['id_user']){
    header('Location:../Pages/login.html');
} 
$nom = $_SESSION["nom_user"];
$role = $_SESSION["role"];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>WASTE Collect</title>
  <link rel="stylesheet" href="../CSS/dashstyle.css" />
  <link rel="stylesheet" href="../CSS/configs.css" />

  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <style>
    /* Masquer la recherche native DataTables */
    .dataTables_filter {
      display: none !important;
    }
    /* Harmonisation DataTables */
    table.dataTable thead {
      background: #cfa13b;
      color: white;
    }
    table.dataTable tbody tr:hover {
      background: #f3e6c0;
    }
    .dataTables_paginate .paginate_button.current {
      background: #cfa13b !important;
      color: white !important;
      border-radius: 4px;
    }
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_info {
      margin: 10px 0;
    }
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
        <div class="app-name"><?php echo $nom; ?></div>
      </div>
    </header>

    <!-- Onglets -->
    <div class="tabs">
      <button class="tab-btn active" data-target="utilisateurs">Utilisateurs</button>
      <button class="tab-btn" data-target="compte">Compte</button>
      <button class="tab-btn" data-target="connexions">Connexions</button>
    </div>

    <!-- Section Tableau -->
    <div id="utilisateurs" class="content-section active">
      <div class="card">
        <h2>Liste des Utilisateurs</h2>
        <table id="usersTable" class="display">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Rôle</th>
              <th>Email</th>
              <th>Téléphone</th>
              <th>Lieu</th>
              <th>Permis</th>
              <th style="text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($utilisateurs)): ?>
            <?php foreach ($utilisateurs as $user): ?>
              <tr class="user-row"
                data-id="<?= htmlspecialchars($user['id_user']) ?>"
                data-nom="<?= htmlspecialchars($user['nom_user']) ?>"
                data-role="<?= htmlspecialchars($user['role']) ?>"
                data-email="<?= htmlspecialchars($user['email']) ?>"
                data-telephone="<?= htmlspecialchars($user['telephone']) ?>"
                data-lieu="<?= htmlspecialchars($user['lieu']) ?>"
                data-permis="<?= htmlspecialchars($user['permis']) ?>">
                <td><?= htmlspecialchars($user['nom_user']); ?></td>
                <td><?= htmlspecialchars($user['role']); ?></td>
                <td><?= htmlspecialchars($user['email']); ?></td>
                <td><?= htmlspecialchars($user['telephone']); ?></td>
                <td><?= htmlspecialchars($user['lieu']); ?></td>
                <td><?= htmlspecialchars($user['permis']); ?></td>
                <td class="actions">
                  <div class="dropdown">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                    <div class="dropdown-content">
                      <a href="#" class="edit-btn"
                        data-nom="<?= htmlspecialchars($user['nom_user']) ?>"
                        data-email="<?= htmlspecialchars($user['email']) ?>"
                        data-telephone="<?= htmlspecialchars($user['telephone']) ?>"
                        data-lieu="<?= htmlspecialchars($user['lieu']) ?>"
                        data-permis="<?= htmlspecialchars($user['permis']) ?>">
                        Modifier
                      </a>
                      <a href="config.php?supprimer=<?= $user['nom_user'] ?>"
                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
                    </div>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7">Aucun utilisateur trouvé.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Section Compte -->
    <div id="compte" class="content-section">
      <h2>Gestion du Compte</h2>
      <form method="post" action="compte_user.php" id="compteForm">
        <input type="hidden" name="id_user" id="compte_id" value="<?= $dernierUser['id_user'] ?? '' ?>">
        <div class="form-group"><label>Nom</label>
          <input type="text" name="nom_user" id="compte_nom" value="<?= htmlspecialchars($dernierUser['nom_user'] ?? '') ?>" required>
        </div>
        <div class="form-group"><label>Email</label>
          <input type="email" name="email" id="compte_email" value="<?= htmlspecialchars($dernierUser['email'] ?? '') ?>" required>
        </div>
        <div class="form-group"><label>Téléphone</label>
          <input type="text" name="telephone" id="compte_tel" value="<?= htmlspecialchars($dernierUser['telephone'] ?? '') ?>" required>
        </div>
        <div class="form-group"><label>Rôle</label>
          <select name="role" id="compte_role" required>
            <option value="citoyen" <?= (($dernierUser['role'] ?? '')==='citoyen')?'selected':'' ?>>Citoyen</option>
            <option value="chauffeur" <?= (($dernierUser['role'] ?? '')==='chauffeur')?'selected':'' ?>>Chauffeur</option>
            <option value="administrateur" <?= (($dernierUser['role'] ?? '')==='administrateur')?'selected':'' ?>>Administrateur</option>
          </select>
        </div>
        <div class="form-group"><label>Lieu de domicile</label>
          <input type="text" name="lieu" id="compte_lieu" value="<?= htmlspecialchars($dernierUser['lieu'] ?? '') ?>" required>
        </div>
        <div class="form-group"><label>Permis</label>
          <input type="text" name="permis" id="compte_permis" value="<?= htmlspecialchars($dernierUser['permis'] ?? '') ?>">
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <button type="reset" class="btn btn-secondary">Annuler</button>
      </form>
    </div>

    <!-- Section Connexions -->
    <div id="connexions" class="content-section">
      <h2>Historique des Connexions</h2>
      <p>(À implémenter…)</p>
    </div>
  </div>

  <!-- Modal Modifier -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Modifier l’utilisateur</h2>
      <form id="editForm" method="post" action="modif_user.php">
        <input type="hidden" name="id_user" id="edit_id">
        <div class="form-group"><label>Nom</label>
          <input type="text" name="nom_user" id="edit_nom" required>
        </div>
        <div class="form-group"><label>Email</label>
          <input type="email" name="email" id="edit_email" required>
        </div>
        <div class="form-group"><label>Téléphone</label>
          <input type="text" name="telephone" id="edit_tel" required>
        </div>
        <div class="form-group"><label>Lieu de domicile</label>
          <input type="text" name="lieu" id="edit_lieu" required>
        </div>
        <div class="form-group"><label>Permis</label>
          <input type="text" name="permis" id="edit_permis" required>
        </div>
        <button type="submit" class="btn btn-primary">Mettre à jour</button>
        <button type="button" class="btn btn-secondary close">Annuler</button>
      </form>
    </div>
  </div>

  <script>
    // Tabs
    const buttons = document.querySelectorAll(".tab-btn");
    const sections = document.querySelectorAll(".content-section");
    buttons.forEach(btn => {
      btn.addEventListener("click", () => {
        buttons.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
        sections.forEach(sec => sec.classList.remove("active"));
        document.getElementById(btn.dataset.target).classList.add("active");
      });
    });

    // Charger un utilisateur dans le formulaire "Compte"
    document.querySelectorAll(".user-row").forEach(row => {
      row.addEventListener("click", () => {
        document.getElementById("compte_id").value = row.dataset.id;
        document.getElementById("compte_nom").value = row.dataset.nom;
        document.getElementById("compte_email").value = row.dataset.email;
        document.getElementById("compte_tel").value = row.dataset.telephone;
        document.getElementById("compte_role").value = row.dataset.role;
        document.getElementById("compte_lieu").value = row.dataset.lieu;
        document.getElementById("compte_permis").value = row.dataset.permis;
        document.querySelector(".tab-btn[data-target='compte']").click();
      });
    });

    // Modal Modifier
    document.querySelectorAll(".edit-btn").forEach(btn => {
      btn.addEventListener("click", e => {
        e.preventDefault();
        document.getElementById("edit_nom").value = btn.dataset.nom;
        document.getElementById("edit_email").value = btn.dataset.email;
        document.getElementById("edit_tel").value = btn.dataset.telephone;
        document.getElementById("edit_lieu").value = btn.dataset.lieu;
        document.getElementById("edit_permis").value = btn.dataset.permis;
        document.getElementById("editModal").style.display = "block";
      });
    });
    document.querySelectorAll(".close").forEach(el => {
      el.addEventListener("click", () => {
        document.getElementById("editModal").style.display = "none";
      });
    });
    window.onclick = function(event) {
      const modal = document.getElementById("editModal");
      if (event.target === modal) modal.style.display = "none";
    };

    // Initialiser DataTable
    $(document).ready(function() {
      const table = $('#usersTable').DataTable({
        language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json" }
      });

      // Relier la barre de recherche du header
      $('.header input[type="search"]').on('keyup', function(){
        table.search(this.value).draw();
      });
    });
  </script>
</body>
</html>
