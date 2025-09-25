<?php
//récuperer les utilisateurs effectuant un signalement
$sql=$cnx->query("SELECT nom_user FROM utilisateur WHERE id_user=$id");
$nom_users = $sql->fetch(PDO::FETCH_ASSOC);


// récuperer les noms des points sur lequel un signalement a été effectué
$sql1=$cnx->query("SELECT nom_pt FROM point_collecte WHERE id_pt=$id_pt");
$nom_point = $sql1->fetch(PDO::FETCH_ASSOC);
?>