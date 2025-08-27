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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un point</title>
</head>
<body>
    <h2>Modifier le pointt</h2>
    <form action="#" method="POST">
                        <input type="text" name="nom_pt" placeholder="Entrer le nom du point de collecte" value="<?= htmlspecialchars($point['nom_pt']) ?>">
                        <input type="float" name="capacite" placeholder="Entrer la capacité du point" value="<?= htmlspecialchars($point['capacite']) ?>">
                        <input type="text" name="lieu" placeholder="Entrer le lieu" value="<?= htmlspecialchars($point['lieu']) ?>">
                        <label>Etat actuel:</label>
                        <select name="Etat">
                            <option value="vide" <?= $point['Etat'] == 'Vide' ? 'selected' : '' ?>>Vide</option>
                            <option value="rempli" <?= $point['Etat'] == 'Rempli' ? 'selected' : '' ?>>Rempli</option>
                            
                        </select>
                        <input type="datetime-local" name="date_vidange"  value="<?= htmlspecialchars($point['date_vidange']) ?>">
                        <button type="submit" name="submit">Modifier</button>

</body>
</html>
