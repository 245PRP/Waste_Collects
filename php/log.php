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

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $motdepasse = trim($_POST["motdepasse"]);

    if (empty($email)) {
        header("Location: ../Pages/login.html?error=Veuillez+renseigner+votre+email");
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../Pages/login.html?error=L'adresse+email+est+mal+renseignée");
        exit();
    } elseif (empty($motdepasse)) {
        header("Location: ../Pages/login.html?error=Veuillez+renseigner+votre+mot+de+passe");
        exit();
    } else {
        $sql = "SELECT * FROM utilisateur WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([":email" => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($motdepasse, $user["motdepasse"])) {
                $_SESSION["id_user"]  = $user["id_user"];
                $_SESSION["nom_user"] = $user["nom_user"];
                $_SESSION["role"]     = $user["role"];

                $role = $_SESSION["role"];

                if ($role === "administrateur") {
                    header("Location: ../Pages/dashboard.php");
                    exit();
                } elseif ($role === "citoyen") {
                    header("Location: ../Pages/signal.php");
                    exit();
                } elseif ($role === "chauffeur") {
                    header("Location: ../php/phpdash.php");
                    exit();
                } else {
                    header("Location: ../Pages/login.html?error=Rôle+non+reconnu");
                    exit();
                }
            } else {
                header("Location: ../Pages/login.html?error=Mot+de+passe+incorrect");
                exit();
            }
        } else {
            header("Location: ../Pages/login.html?error=Email+ou+mot+de+passe+incorrect");
            exit();
        }
    }
}

























































