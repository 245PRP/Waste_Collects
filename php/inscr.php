<?php
// paramètre de connexion
$host="localhost";
$dbname="waste_collect";
$username="root";
$password="";

try {
    // connexion avec PDO    
    $cnx = new PDO("mysql:host=$host; dbname=$dbname; charset=utf8",$username, $password);
    $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// vérification de la soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom       = $_POST['nom_user'];
    $email     = $_POST['email'];
    $telephone = $_POST['telephone'];
    $lieu      = $_POST['lieu'];
    $motdepasse= $_POST['motdepasse'];

    // rôle par défaut (car on a supprimé le champ <select>)
    $role = "citoyen";

    // vérifier unicité de l'email
    $check = $cnx->prepare("SELECT id_user FROM utilisateur WHERE email = :email");
    $check->execute([":email" => $email]);
    if ($check->fetch()) {
        die("⚠️ Cet email est déjà utilisé, veuillez en choisir un autre.");
    }

    // hachage du mot de passe
    $hashedPassword = password_hash($motdepasse, PASSWORD_DEFAULT);

    try {
        $stmt = $cnx->prepare('
            INSERT INTO utilisateur(nom_user, email, telephone, lieu, role, motdepasse)
            VALUES (:nom, :email, :telephone, :lieu, :role, :motdepasse)
        ');
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':lieu', $lieu);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':motdepasse', $hashedPassword);

        $stmt->execute();

        // redirection selon le rôle attribué
        if ($role === "administrateur") {
            header("Location: ../Pages/dashboard.php");
        } elseif ($role === "citoyen") {
            header("Location: ../Pages/signal.php");
        } elseif ($role === "chauffeur") {
            header("Location: ../Pages/dashboard.php");
        } else {
            echo "Rôle non reconnu";
        }
        exit();

    } catch (PDOException $e) {
        echo "Erreur d'insertion dans la base de donnée : " . $e->getMessage();
    }
}
echo "formulaire reçu";
?>





































































































































































































































/*
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
    $nom       = trim($_POST["nom"]);
    $email     = trim($_POST["email"]);
    $motdepasse = password_hash($_POST["motdepasse"], PASSWORD_DEFAULT);
    $role      = $_POST["role"]; // admin, citoyen ou chauffeur

    // Préparer les colonnes dynamiquement
    $colonnes = [
        "admin"    => null,
        "citoyen"  => null,
        "chauffeur"=> null
    ];

    if (array_key_exists($role, $colonnes)) {
        $colonnes[$role] = $email;
    } else {
        die("Rôle invalide !");
    }

    // Requête d'insertion
    $sql = "INSERT INTO utilisateur (nom, admin, citoyen, chauffeur, mot_de_passe) 
            VALUES (:nom, :admin, :citoyen, :chauffeur, :motdepasse)";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":nom"       => $nom,
        ":admin"     => $colonnes["admin"],
        ":citoyen"   => $colonnes["citoyen"],
        ":chauffeur" => $colonnes["chauffeur"],
        ":motdepasse"=> $motdepasse
    ]);

    echo "Inscription réussie !";
}
?>