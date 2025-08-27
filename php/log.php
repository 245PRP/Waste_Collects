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
    $email = $_POST["email"];
    $motdepasse = $_POST["motdepasse"];

    //  On récupère uniquement l’utilisateur par email
    $sql = "SELECT * FROM utilisateur WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([":email" => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        //  Vérification du mot de passe haché
        if (password_verify($motdepasse, $user["motdepasse"])) {

            // Démarrer la session
            session_start();
            $_SESSION["id_user"]  = $user["id_user"];
            $_SESSION["nom_user"] = $user["nom_user"];
            $_SESSION["role"]     = $user["role"];

            $role = $_SESSION["role"];

            //  Redirection selon le rôle
            if ($role === "administrateur") {
                header("Location: ../Pages/dashboard.php");
            } elseif ($role === "citoyen") {
                header("Location: ../Pages/signal.html");
            } elseif ($role === "chauffeur") {
                header("Location: ../Pages/dashboard.php");
            } else {
                echo "Rôle non reconnu.";
            }
            exit();

        } else {
            // Mot de passe incorrect
            echo " mot de passe incorrect.";
        }
    } else {
        echo "Email ou mot de passe incorrect.";
    }
}
?>
