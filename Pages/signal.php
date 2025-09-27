<?php 
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

try {
    $cnx= new PDO("mysql:host=localhost;dbname=waste_collect","root","");
}
catch(PDOException $e){
    echo"Erreur de connexion à la base de donnée veuillez réesayer plutard:".$e->getMessage();
}

try{ 
$sql="SELECT id_pt, nom_pt FROM point_collecte";
$stmt=$cnx->prepare($sql);
$stmt->execute();
$points=$stmt->fetchAll();
}
catch(PDOException $e){
    echo"Erreur:".$e->getMessage();
} 

$stmt = $cnx->query("SELECT id_user, email FROM utilisateur WHERE role='administrateur'");
$utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success_message = ""; // ✅ pour stocker le message

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
            } catch (Exception $e) {}
        }

        // ✅ Définir le message de succès
        $success_message = "Votre signalement a bien été pris en compte ✅";

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
    <style>
        /* ✅ Style de l'alerte */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        .toast {
            display: flex;
            align-items: center;
            background: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
            padding: 12px 18px;
            margin-bottom: 10px;
            border-radius: 4px;
            min-width: 280px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            animation: slideIn 1s, fadeOut 1s 3.5s forwards;
        }
        .icon {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            background-color: #3c763d;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 10px;
        }
        .toast .close {
            margin-left: auto;
            background: none;
            border: none;
            color: #3c763d;
            font-weight: bold;
            cursor: pointer;
            font-size: 18px;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(100%); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeOut {
            to { opacity: 0; transform: translateX(100%); }
        }
    </style>
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
                        <option value="<?= $point['id_pt'] ?>"><?= $point['nom_pt']?></option>
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

    <!-- ✅ Conteneur toast -->
    <div class="toast-container" id="toastContainer"></div>

    <?php if (!empty($success_message)) : ?>
    <script>
        function showSuccess(message) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.classList.add('toast');
            toast.innerHTML = `
                <div class="icon">✔</div>
                <div class="toast-message">${message}</div>
                <button class="close" onclick="this.parentElement.remove()">×</button>
            `;
            container.appendChild(toast);
            setTimeout(() => { toast.remove(); }, 4000);
        }
        // ✅ Appeler la fonction avec le message PHP
        showSuccess("<?= $success_message ?>");
    </script>
    <?php endif; ?>
</body>
</html>
