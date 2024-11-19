<?php
require 'db.php';
session_start();

// Connexion à la base de données
$db = Database::connect();

// Récupérer les paramètres d'entrée
$itemId = isset($_POST['id']) ? intval($_POST['id']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

// Identifier l'utilisateur (temporaire ou connecté)
$userIdentifier = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : (isset($_COOKIE['userTemp']) ? $_COOKIE['userTemp'] : null);

if ($itemId && $userIdentifier) {
    // Récupérer l'élément du panier pour vérifier la quantité actuelle
    $query = "SELECT qte FROM panier WHERE id_item = :id_item AND userTemp = :userTemp";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':id_item', $itemId, PDO::PARAM_INT);
    $stmt->bindValue(':userTemp', $userIdentifier, PDO::PARAM_STR);
    $stmt->execute();

    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        $currentQty = $item['qte'];

        if ($action == 'increase') {
            $newQty = $currentQty + 1;
        } elseif ($action == 'decrease' && $currentQty > 1) {
            $newQty = $currentQty - 1;
        } else {
            $newQty = $currentQty;
        }

        // Mettre à jour la quantité dans la base de données
        $query = "UPDATE panier SET qte = :newQty WHERE id_item = :id_item AND userTemp = :userTemp";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':newQty', $newQty, PDO::PARAM_INT);
        $stmt->bindValue(':id_item', $itemId, PDO::PARAM_INT);
        $stmt->bindValue(':userTemp', $userIdentifier, PDO::PARAM_STR);
        $stmt->execute();

        // Calculer le prix total de l'article après la mise à jour de la quantité
        $query = "SELECT prix FROM panier WHERE id_item = :id_item AND userTemp = :userTemp";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id_item', $itemId, PDO::PARAM_INT);
        $stmt->bindValue(':userTemp', $userIdentifier, PDO::PARAM_STR);
        $stmt->execute();
        
        $itemPrice = $stmt->fetch(PDO::FETCH_ASSOC)['prix'];
        $newItemTotal = $itemPrice * $newQty;

        // Retourner la nouvelle quantité et le total mis à jour
        echo json_encode([
            'success' => true,
            'newQuantity' => $newQty,
            'newTotal' => number_format($newItemTotal, 2)
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}

Database::disconnect();
?>
