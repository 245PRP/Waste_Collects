<?php
// Connexion à la base de données
$host = "localhost";
$dbname = "waste_collect";
$username = "root";  
$password = "";      

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérifier si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $motdepasse = $_POST["motdepasse"];

    // recherche de l’utilisateur par email dans les 3 colonnes
    $sql = "SELECT * FROM user 
            WHERE admin = :email OR citoyen = :email OR chauffeur = :email
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([":email" => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($motdepasse, $user["mot_de_passe"])) {
        // Déterminer le rôle
        if (!empty($user["admin"]) && $user["admin"] === $email) {
            $role = "admin";
        } elseif (!empty($user["citoyen"]) && $user["citoyen"] === $email) {
            $role = "citoyen";
        } elseif (!empty($user["chauffeur"]) && $user["chauffeur"] === $email) {
            $role = "chauffeur";
        } else {
            $role = "inconnu";
        }

        // Démarrer la session
        session_start();
        $_SESSION["id_user"] = $user["id_user"];
        $_SESSION["nom"]     = $user["nom"];
        $_SESSION["role"]    = $role;

        // Redirection selon le rôle
        if ($role === "admin") {
            header("Location: admin_dashboard.php");
        } elseif ($role === "citoyen") {
            header("Location: citoyen_trouv.php");
        } elseif ($role === "chauffeur") {
            header("Location: chauffeur_dashboard.php");
        } else {
            echo "Rôle non reconnu.";
        }
        exit();
    } else {
        echo "Email ou mot de passe incorrect.";
    }
}
?>