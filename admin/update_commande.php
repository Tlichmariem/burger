<?php
require '../db.php';
session_start();



// Vérification des données envoyées
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commande_id'], $_POST['status'])) {
    $commandeId = intval($_POST['commande_id']);
    $status = $_POST['status'];

    if (in_array($status, ['En attente', 'Complétée', 'Annulée'])) {
        $db = Database::connect();
        $query = "UPDATE commandes SET status = :status WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->bindValue(':id', $commandeId, PDO::PARAM_INT);
        $stmt->execute();
        Database::disconnect();

        header('Location: gestion_commandes.php?success=1');
        exit();
    }
}

header('Location: gestion_commandes.php?error=1');
exit();
