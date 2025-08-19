<?php
//paramètre de connexion
$host="localhost";
$dbname="waste_collect";
$username="root";
$password="";
try{
    //connexion avec PDO    
    $cnx= new PDO("mysql:host=$host; dbname=$dbname; charset=utf8",$username, $password);
    $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

    // vérification de la soumission du formulaire
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $nom=$_POST['nom_user'];
        $email=$_POST['email'];
        $telephone=$_POST['telephone'];
        $lieu=$_POST['lieu'];
        $role=$_POST['role'];
        $motdepasse=$_POST['motdepasse'];

        $hashedPassword=password_hash($motdepasse, PASSWORD_DEFAULT);
    }
     try {
            $stmt=$cnx->prepare('INSERT INTO utilisateur(nom_user, email, telephone, lieu,role,motdepasse)VALUES (:nom, :email, :telephone, :lieu, :role, :motdepasse)');
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->bindParam(':lieu', $lieu);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':motdepasse', $hashedPassword);
        
        $stmt->execute();
    } catch (PDOException $e) {
        echo"Erreur d'insertion dans la base de donnée".$e->getMessage();
    }
    //redirection selon le role choisie

    if($role==="administrateur"){
        header("Location: ../Pages/dashboard.php");
    }elseif ($role==="citoyen") {
        header("Location: ../Pages/trouv.html");
    }elseif ($role==="chauffeur") {
        header("Location: ../Pages/dashboard.php");
    }else{
        echo "role non reconnu";
    }
        exit();
        //recupère les informations
    $sql="SELECT * FROM utilisateur";
    $stmt=$cnx->prepare($sql);
    if($stmt===false){
        throw new PDOException("Erreur lors de la preparation de la requete");
    }
    $stmt->execute();
    $inscr=$stmt->fetchAll();
    if($inscr===false){
        throw new PDOException("Erreur lors de la recuperation de la requete");
    }
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