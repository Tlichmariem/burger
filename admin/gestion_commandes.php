<?php
require '../db.php';
session_start();



$db = Database::connect();

// Récupérer toutes les commandes avec les détails de l'utilisateur
$query = "SELECT c.id, c.total, c.status, c.created_at, u.nom , u.email 
          FROM commandes c 
          JOIN users u ON c.user_id = u.id 
          ORDER BY c.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

Database::disconnect();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des Commandes</title>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container">
    <h1 class="mt-4">Gestion des Commandes</h1>
    <table class="table table-bordered mt-4">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nom Utilisateur</th>
            <th>Email</th>
            <th>Total</th>
            <th>Statut</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($commandes as $commande): ?>
            <tr>
                <td><?= htmlspecialchars($commande['id']) ?></td>
                <td><?= htmlspecialchars($commande['nom']) ?></td>
                <td><?= htmlspecialchars($commande['email']) ?></td>
                <td><?= number_format($commande['total'], 2) ?> €</td>
                <td>
                    <?php if ($commande['status'] === 'En attente'): ?>
                        <span class="badge bg-warning">En attente</span>
                    <?php elseif ($commande['status'] === 'Complétée'): ?>
                        <span class="badge bg-success">Complétée</span>
                    <?php else: ?>
                        <span class="badge bg-secondary"><?= htmlspecialchars($commande['status']) ?></span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($commande['created_at']) ?></td>
                <td>
                    <form method="post" action="update_commande.php" class="d-inline">
                        <input type="hidden" name="commande_id" value="<?= htmlspecialchars($commande['id']) ?>">
                        <select name="status" class="form-select form-select-sm d-inline w-auto">
                            <option value="En attente" <?= $commande['status'] === 'En attente' ? 'selected' : '' ?>>En attente</option>
                            <option value="Complétée" <?= $commande['status'] === 'Complétée' ? 'selected' : '' ?>>Complétée</option>
                            <option value="Annulée" <?= $commande['status'] === 'Annulée' ? 'selected' : '' ?>>Annulée</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Mettre à jour</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
