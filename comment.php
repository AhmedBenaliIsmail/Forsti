<?php
// Vérifie si un commentaire a été envoyé
if (isset($_POST['comment'])) {
    $comment = htmlspecialchars($_POST['comment']); // Sécurisation du commentaire
    $timestamp = date("Y-m-d H:i:s"); // Timestamp du commentaire
    
    // Exemple d'enregistrement dans un fichier (vous pouvez utiliser une base de données ici)
    $file = fopen("comments.txt", "a");
    fwrite($file, $timestamp . " - " . $comment . "\n");
    fclose($file);
    
    // Retourner le commentaire et l'heure pour affichage
    echo json_encode(array("comment" => $comment, "timestamp" => $timestamp));
}
?>
