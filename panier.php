<?php
require 'db.php';
session_start();

$db = Database::connect();

// Déterminer l'identifiant de l'utilisateur (session ou cookie temporaire)
$userIdentifier = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : (isset($_COOKIE['userTemp']) ? $_COOKIE['userTemp'] : null);

// Vérifier si l'utilisateur est identifié
if (!$userIdentifier) {
    $prodCart = [];
} else {
    // Récupérer les produits du panier
    $query = "SELECT p.*, i.name, i.image FROM panier p
              JOIN items i ON p.id_item = i.id 
              WHERE p.userTemp = :userIdentifier";

    $stmt = $db->prepare($query);
    $stmt->execute([":userIdentifier" => $userIdentifier]);
    $prodCart = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Calculer le prix HT du panier
$subtotalHT = array_sum(array_map(function($item) {
    return $item['prix'] * $item['qte'];
}, $prodCart));

// Calculer la TVA (exemple : 10% de TVA)
$TVA = $subtotalHT * 0.1;  // TVA à 10%
$totalTTC = $subtotalHT + $TVA;  // Total TTC sans remise

// Appliquer le coupon si défini
$totalAvecRemise = $totalTTC;
if (isset($_SESSION['coupon'])) {
    $coupon = $_SESSION['coupon'];
    if ($coupon['type'] == '%') {
        $remise = $totalTTC * $coupon['remise'] / 100;
    } elseif ($coupon['type'] == 'euros') {
        $remise = $coupon['remise'];
    }
    $totalAvecRemise = $totalTTC - $remise;  // Total après remise
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Panier</h1>
        
        <!-- Bouton retour -->
        <a href="index.php" class="btn btn-secondary mb-3">Retour à l'accueil</a>

        <?php if (empty($prodCart)): ?>
            <div class="alert alert-danger" role="alert">
                Votre panier est vide !
            </div>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Produit</th>
                        <th>Prix</th>
                        <th>Quantité</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prodCart as $prod): ?>
                        <tr data-id="<?= $prod['id_item'] ?>">
                            <td><img src="images/<?= $prod['image'] ?>" alt="<?= $prod['name'] ?>" style="width:100px"></td>
                            <td><?= $prod['name'] ?></td>
                            <td class="price"><?= number_format($prod['prix'], 2) ?> €</td>
                            <td>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary decrease-qty" type="button">-</button>
                                    <input type="text" class="form-control text-center item-qty" value="<?= $prod['qte'] ?>" readonly>
                                    <button class="btn btn-outline-secondary increase-qty" type="button">+</button>
                                </div>
                            </td>
                            <td class="item-total"><?= number_format($prod['prix'] * $prod['qte'], 2) ?> €</td>
                            <td>
                                <a href="removeItem.php?id=<?= $prod['id_item'] ?>" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Etes vous sur de vouloir supprimer ce produit ?')">
                                    Supprimer
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Formulaire pour appliquer un coupon -->
            <div class="row">
                <div class="col-md-6">
                    <h3>Appliquez un coupon</h3>
                    <form action="couponREQ.php" method="POST">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="code" placeholder="Entrez votre code coupon" aria-label="Coupon">
                            <input type="hidden" name="total" value="<?= number_format($totalTTC, 2) ?>">
                            <button class="btn btn-primary" type="submit">Appliquer</button>
                        </div>
                    </form>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?php
                            if ($_GET['error'] == 1) {
                                echo "Coupon invalide.";
                            } elseif ($_GET['error'] == 2) {
                                echo "Type de remise incorrect.";
                            } elseif ($_GET['error'] == 3) {
                                echo "Requête invalide.";
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6 text-end">
                    <h3>Détails du panier :</h3>
                    <p><strong>Prix HT :</strong> <?= number_format($subtotalHT, 2) ?> €</p>
                    <p><strong>TVA (10%) :</strong> <?= number_format($TVA, 2) ?> €</p>
                    <p><strong>Total TTC :</strong> <?= number_format($totalTTC, 2) ?> €</p>
                    <?php if (isset($remise)): ?>
                        <p><strong>Remise appliquée :</strong> -<?= number_format($remise, 2) ?> €</p>
                        <p><strong>Total avec remise :</strong> <?= number_format($totalAvecRemise, 2) ?> €</p>
                    <?php else: ?>
                        <p><strong>Total sans remise :</strong> <?= number_format($totalTTC, 2) ?> €</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        $(document).ready(function() {
            // Fonction d'update de la quantité d'article
            $('.increase-qty').click(function() {
                var itemId = $(this).closest('tr').data('id');
                updateQuantity(itemId, 'increase');
            });

            $('.decrease-qty').click(function() {
                var itemId = $(this).closest('tr').data('id');
                updateQuantity(itemId, 'decrease');
            });

            function updateQuantity(itemId, action) {
                $.ajax({
                    url: 'updateQTE.php',
                    method: 'POST',
                    data: { id: itemId, action: action },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.success) {
                            var row = $('tr[data-id="' + itemId + '"]');
                            var newQty = data.newQuantity;
                            row.find('.item-qty').val(newQty);
                            row.find('.item-total').text(data.newTotal + ' €');

                            var total = 0;
                            $('.item-total').each(function() {
                                total += parseFloat($(this).text());
                            });

                            $('#total').text(total.toFixed(2) + ' €');
                        }
                    }
                });
            }
        });
    </script>
<form action="ajouter_commande.php" method="POST">
    <button type="submit" class="btn btn-success">Finaliser ma commande</button>
</form>

</body>
</html>
