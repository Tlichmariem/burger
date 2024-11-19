<?php
require 'db.php';
session_start();

if (!isset($_GET['commandeId'])) {
    header('Location: index.php');
    exit();
}

$commandeId = $_GET['commandeId'];
$db = Database::connect();

// Récupérer les détails de la commande
$query = "SELECT * FROM commandes WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindValue(':id', $commandeId, PDO::PARAM_INT);
$stmt->execute();
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$commande) {
    // Si la commande n'existe pas
    header('Location: index.php');
    exit();
}

// Récupérer les articles de la commande
$query = "SELECT ci.*, i.name FROM commande_items ci INNER JOIN items i ON ci.item_id = i.id WHERE ci.commande_id = :commande_id";
$stmt = $db->prepare($query);
$stmt->bindValue(':commande_id', $commandeId, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

Database::disconnect();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Commande</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Votre commande a été validée</h1>
        <p>Merci pour votre commande. Voici les détails :</p>

        <table class="table">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td><?= htmlspecialchars($article['name']) ?></td>
                        <td><?= $article['quantity'] ?></td>
                        <td><?= number_format($article['prix'], 2) ?> €</td>
                        <td><?= number_format($article['prix'] * $article['quantity'], 2) ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p><strong>Total Commande : <?= number_format($commande['total'], 2) ?> €</strong></p>
        <a href="suivi_commandes.php" class="btn btn-primary">Voir mes commandes</a>
    </div>
</body>
</html>
