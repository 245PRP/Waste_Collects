<?php
//connexion à la base de donnée
try {
    $cnx= new PDO("mysql:host=localhost;dbname=waste_collect","root","");
}
catch(PDOException $e){
    echo"Erreur de connexion à la base de donnée veuillez réesayer plutard:".$e->getMessage();
}
if(isset($_GET['id_tour'])){
    $id_tour=$_GET['id_tour'];
    echo $id_tour;

  try {
    $stmt = $cnx->prepare("UPDATE `ramassage` SET `statut` = 'traitee' WHERE `ramassage`.`id_tour` = :id_tour");
    $stmt->execute(['id_tour' => $id_tour]);

    header('Location:phpdash.php');
    } catch (PDOException $e) {
      echo "Erreur de changement de statut : " . $e->getMessage();
    }

  }


?>