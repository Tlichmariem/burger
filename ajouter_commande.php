<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: inscription.php');  // Rediriger l'utilisateur vers la page de connexion s'il n'est pas connecté
    exit();
}

$userId = $_SESSION['user_id'];
$db = Database::connect();

// Récupérer les articles du panier de l'utilisateur
$query = "SELECT * FROM panier WHERE userTemp = :userTemp";
$stmt = $db->prepare($query);
$stmt->bindValue(':userTemp', $userId, PDO::PARAM_STR);
$stmt->execute();
$articlesPanier = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si le panier est vide, rediriger
if (empty($articlesPanier)) {
    header('Location: panier.php');
    exit();
}

// Calculer le total de la commande
$totalCommande = 0;
foreach ($articlesPanier as $article) {
    $totalCommande += $article['prix'] * $article['qte'];
}

// Ajouter la commande dans la table `commandes`
$query = "INSERT INTO commandes (user_id, total) VALUES (:user_id, :total)";
$stmt = $db->prepare($query);
$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$stmt->bindValue(':total', $totalCommande, PDO::PARAM_STR);
$stmt->execute();

// Récupérer l'ID de la commande créée
$commandeId = $db->lastInsertId();

// Ajouter les articles de panier à la table `commande_items` (une nouvelle table pour les détails de la commande)
$query = "INSERT INTO commande_items (commande_id, item_id, quantity, prix) VALUES (:commande_id, :item_id, :quantity, :prix)";
$stmt = $db->prepare($query);

foreach ($articlesPanier as $article) {
    $stmt->bindValue(':commande_id', $commandeId, PDO::PARAM_INT);
    $stmt->bindValue(':item_id', $article['id_item'], PDO::PARAM_INT);
    $stmt->bindValue(':quantity', $article['qte'], PDO::PARAM_INT);
    $stmt->bindValue(':prix', $article['prix'], PDO::PARAM_STR);
    $stmt->execute();
}

// Vider le panier après avoir ajouté la commande
$query = "DELETE FROM panier WHERE userTemp = :userTemp";
$stmt = $db->prepare($query);
$stmt->bindValue(':userTemp', $userId, PDO::PARAM_STR);
$stmt->execute();

Database::disconnect();

// Rediriger l'utilisateur vers la page de confirmation de commande
header('Location: confirmation_commande.php?commandeId=' . $commandeId);
exit();
