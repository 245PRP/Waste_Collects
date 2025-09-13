<?php
try {
    $cnx = new PDO('mysql:host=localhost;dbname=waste_collect', 'root', '');
    $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $nom = $_POST["nom_user"];
    $email = $_POST["email"];
    $telephone = $_POST["telephone"];
    $lieu = $_POST["lieu"];
    $permis = $_POST["permis"];
    $role = $_POST["role"]; 

    $sql = "UPDATE utilisateur 
            SET nom_user = :nom, email = :email, telephone = :tel, lieu = :lieu, permis = :permis
            WHERE nom_user = :nom";

    $stmt = $cnx->prepare($sql);
    $stmt->execute([
        ":nom" => $nom,
        ":email" => $email,
        ":tel" => $telephone,
        ":lieu" => $lieu,
        ":permis" => $permis,
       
    ]);

    header("Location: config.php"); // retour sur le tableau de bord
    exit();
}

?>