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