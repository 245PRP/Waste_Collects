<?php 
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

//connexion à la base de donnée
try {
    $cnx= new PDO("mysql:host=localhost;dbname=waste_collect","root","");
}
catch(PDOException $e){
    echo"Erreur de connexion à la base de donnée veuillez réesayer plutard:".$e->getMessage();
}
// affichage des points
try{ 
$sql="SELECT id_pt, nom_pt FROM point_collecte";
$stmt=$cnx->prepare($sql);
if($stmt===false){
    throw new PDOException("Erreur lors de la preparation de la requete");
}
$stmt->execute();
$points=$stmt->fetchAll();
if($points===false){
    throw new PDOException("Erreur lors de la recuperation de la requete");
}

}
catch(PDOException $e){
    echo"Erreur:".$e->getMessage();
} 
// Récupérer tous les utilisateurs
    $stmt = $cnx->query("SELECT id_user, email FROM utilisateur WHERE role='administrateur'");
    $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ajouter un point
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $motif=$_POST['motif'];
    $date=$_POST['date_signal'];
    $description=$_POST['description'];
    $statut = "non-traite";
    $id_pt=$_POST['point'];
    $id=$_SESSION["id_user"];
    $mail = new PHPMailer(true);
    include '../php/signlmt_infos.php';

    $email_message= $nom_users['nom_user']. " vient d'éffectuer un signalement sur le " .$nom_point['nom_pt']. " avec pour motif " .$motif ."<br>" .$description;

    try {
        $stmt=$cnx->prepare('INSERT INTO signalement(motif,date_signal,description,statut,id_user,id_pt)VALUES (:motif, :date_signal, :description, :statut, :id_user, :id_pt)');
        $stmt->bindParam(':motif', $motif);
        $stmt->bindParam(':date_signal', $date);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':statut', $statut);
        $stmt->bindParam(':id_user', $id);
        $stmt->bindParam(':id_pt', $id_pt);
        $stmt->execute();

        foreach ($utilisateurs as $users) {
            try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'prunellendonkeu@gmail.com';
        $mail->Password   = 'immd pfrm cunu mjgl';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

      $mail->setFrom('prunellendonkeu@gmail.com', 'Waste_collect');

        $mail->addAddress($users['email']);

        $mail->isHTML(true);
        $mail->Subject = "Nouveau signalement";
        $mail->Body    = nl2br($email_message);

        $mail->send();
        $message = "✅ Mail envoyé avec succès à {$users['email']}";
    } catch (Exception $e) {
        $message = "❌ Erreur : {$mail->ErrorInfo}";
    }
  
        }
        //header('Location: ../Pages/signal.html');
    } catch (PDOException $e) {
        echo"Erreur d'insertion des signalements dans la base de donnée".$e->getMessage();
    }

}









?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formulaire de Signalement</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
    <div class="container">
        <div class="left-text">
            <h1>Effectuer Votre<br>Signalement</h1>
        </div>
        <div class="form-box">
            <h2>FORMULAIRE</h2>
            <form action="#" method="POST">
                <label>Type de Problème:</label>
                <select name="motif">
                    <option value="plein">Plein</option>
                    <option value="cassé">Cassé</option>
                    <option value="absent">Absent</option>
                    <option value="renversé">Renversé</option>
                </select>
                <label for="limit">Choisissez un point</label>
                        <select id="limit" name="point">
                            <?php foreach ($points as $point) : ?>
                        <option value=<?= $point['id_pt'] ?>><?= $point['nom_pt']?></option>
                        <?php endforeach; ?>
                        </select>
                <label>Entrer la date et l'heure de votre signalement:</label>
                <input type="datetime-local" name="date_signal"  value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                
               
                <label>Description:</label>
                <textarea name="description" placeholder="Entrer votre texte ici" required></textarea>

                <button type="submit">Signaler</button>
                
            </form>
        </div>
    </div>
</body>
</html>
<log class="php"><!-- <?php
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
$message = ""; // Pour stocker les messages d'alerte

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $motdepasse = trim($_POST["motdepasse"]);

    if (empty($email)) {
        $message = "Veuillez renseigner votre adresse email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "L'adresse email est mal renseignée.";
    } elseif (empty($motdepasse)) {
        $message = "Veuillez renseigner votre mot de passe.";
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
                    $message = "Rôle non reconnu.";
                }
            } else {
                $message = "Mot de passe incorrect.";
            }
        } else {
            $message = "Email ou mot de passe incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .alert {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 6px;
            font-size: 14px;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            animation: fadeIn 0.5s ease;
            z-index: 1000;
        }
        .alert-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
        }
        .alert .icon {
            font-weight: bold;
            margin-right: 10px;
            font-size: 18px;
        }
        .alert .close {
            margin-left: auto;
            cursor: pointer;
            font-weight: bold;
            border: none;
            background: none;
            font-size: 16px;
            color: inherit;
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-10px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>

<?php if (!empty($message)): ?>
    <div class="alert alert-warning" id="alertBox">
        <span class="icon">⚠️</span>
        <span><?php echo htmlspecialchars($message); ?></span>
        <button class="close" onclick="document.getElementById('alertBox').style.display='none';">&times;</button>
    </div>
    <script>
        // Disparition automatique au bout de 5 secondes
        setTimeout(() => {
            let alertBox = document.getElementById('alertBox');
            if(alertBox) alertBox.style.display = 'none';
        }, 5000);
    </script>
<?php endif; ?>

<!-- Formulaire sur la même page -->
<form method="POST" action="">
    <input type="email" name="email" placeholder="Email"><br><br>
    <input type="password" name="motdepasse" placeholder="Mot de passe"><br><br>
    <button type="submit">Connexion</button>
</form>

</body>
</html> -->
</log>