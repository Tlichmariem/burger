<?php
require 'db.php';
session_start();

$db = Database::connect();
$userId = $_SESSION['user_id'];  // ID de l'utilisateur connecté

// Récupérer toutes les commandes de l'utilisateur
$query = "SELECT * FROM commandes WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();

$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

Database::disconnect();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi de Commande</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Suivi de vos commandes</h1>

        <?php if (empty($commandes)): ?>
            <p>Vous n'avez pas encore passé de commande.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Commande</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $commande): ?>
                        <tr>
                            <td><a href="commande_detail.php?id=<?= $commande['id'] ?>"><?= $commande['id'] ?></a></td>
                            <td><?= number_format($commande['total'], 2) ?> €</td>
                            <td><?= $commande['created_at'] ?></td>
                            <td><?= htmlspecialchars($commande['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
