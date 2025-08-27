<?php
try {
    $cnx = new PDO('mysql:host=localhost;dbname=waste_collect', 'root', '');
    $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if (!isset($_GET['id'])) {
    die("ID manquant !");
}

$id = $_GET['id'];

// Récupération des camions
$stmt = $cnx->prepare("SELECT * FROM camion WHERE id_cam = :id_cam");
$stmt->bindParam(':id_cam', $id, PDO::PARAM_INT);
$stmt->execute();
$camn = $stmt->fetch();

if (!$camn) {
    die("camion introuvable !");
}

// Mise à jour  du formulaire



if (isset($_POST['submit'])) {
     $immatriculation=$_POST['immatriculation'];
    $id=$_POST['chauffeurs'];

try{
    $update = $cnx->prepare("UPDATE camion SET immatriculation = :immatriculation, id_user = :id_user,  WHERE id_cam = :id_cam");
    $update->bindParam(':immatriculation', $immatriculation);
    $update->bindParam(':id_cam', $id);
    $update->execute();

    header("Location: ../Pages/camion.php");
} catch (PDOException $e) {
        echo"Erreur d'insertion ".$e->getMessage();
    }

    
    exit;

}
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
      <link rel="stylesheet" href="../CSS/dashstyle.css" />
  <link rel="stylesheet" href="../CSS/camion.css" />
    <title>Modifier un point</title>
</head>
<body>
    <h2>Modifier le  camion enregistré</h2>
    <form action="#" method="POST">
        <div class="form-group">
                        <label  for="immatriculation">Entrer votre l'immatriculation du camion
                        <input type="text" id="im" name="immatriculation" value="<?= htmlspecialchars($camn['immatriculation']) ?>">
                        </div>

                        <div class="form-group">
                        <label for="limit">Choisissez un Chauffeur</label>
                        <select id="limit" name="chauffeur">
                            
                             <?php foreach ($chauf as $chauff) : ?>
                                <option value=<?= $chauff['id_user'] ?> <?php if ($chauff['id_user'] === $camn['id_user'] ) echo 'selected';?>><?= $chauff['nom_user'] ?></option>
                                <?php endforeach; ?>
                            
                        </select>
                        <button type="submit" name="submit">Modifier</button>

</body>
</html>
