<?php
header('Content-Type: application/json');
$cnx = new PDO("mysql:host=localhost;dbname=waste_collect","root","");
$sql = "SELECT ramassage.date_tour, utilisateur.nom_user, point_collecte.nom_pt FROM ramassage  JOIN utilisateur  ON ramassage.id_user = utilisateur.id_user JOIN point_collecte  ON ramassage.id_pt = point_collecte.id_pt";
$stmt = $cnx->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$events = [];
foreach ($rows as $row) {
    $events[] = [
        'title' => $row['nom_user']." - ".$row['nom_pt'],
        'start' => $row['date_tour'],
        'chauffeur' => $row['nom_user'],
        'point' => $row['nom_pt']
        
    ];
}
echo json_encode($events);
?>