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

      $mail->setFrom('prunellendonkeu@gmail.com', 'MAP App');

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
