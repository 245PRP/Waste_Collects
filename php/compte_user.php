<?php
session_start();
try {
    $cnx = new PDO("mysql:host=localhost;dbname=waste_collect","root","");
    $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
       // $id = $_POST["id_user"];
        $nom = $_POST["nom_user"];
        $email = $_POST["email"];
        $tel = $_POST["telephone"];
        $role = $_POST["role"];
        $lieu = $_POST["lieu"];
        $permis = $_POST["permis"];

        $sql = "UPDATE utilisateur 
                SET nom_user = :nom, email = :email, telephone = :tel, role = :role, lieu = :lieu, permis = :permis 
                WHERE nom_user = :nom";

        $stmt = $cnx->prepare($sql);
        $stmt->execute([
            ":nom" => $nom,
            ":email" => $email,
            ":tel" => $tel,
            ":role" => $role,
            ":lieu" => $lieu,
            ":permis" => $permis
            //":id" => $id
        ]);

        // redirection pour recharger la liste
        header("Location: config.php?success=1");
        exit;
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}