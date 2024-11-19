<?php
require 'db.php';
session_start();

$db = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code']) && isset($_POST['total'])) {
    $query = 'SELECT * FROM coupons WHERE code = :codeCoupon AND debut <= NOW() AND fin >= NOW()';
    $stmt = $db->prepare($query);
    $stmt->bindValue(':codeCoupon', $_POST['code'], PDO::PARAM_STR);
    $stmt->execute();
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);
   
    if (!empty($coupon)) { 
        $typeRemise = $coupon['type']; 
        $valeurRemise = $coupon['remise']; 
        $totalPanier = floatval($_POST['total']);

        if ($typeRemise == '%') { 
            $montantRemise = $totalPanier * $valeurRemise / 100; 
        } elseif ($typeRemise == 'euros') { 
            $montantRemise = $valeurRemise;
        } else {
            // Type de remise invalide
            header('Location: panier.php?error=2');
            exit();
        }

        $totalAvecRemise = max(0, $totalPanier - $montantRemise);

        // Sauvegarder les informations du coupon dans la session
        $_SESSION['coupon'] = [
            'code' => $coupon['code'],
            'remise' => $valeurRemise,
            'type' => $typeRemise,
            'montant' => $montantRemise
        ];

        // Rediriger vers panier.php avec le prix actualisé
        header('Location: panier.php?newPrice=' . number_format($totalAvecRemise, 2));
    } else {
        // Coupon invalide
        header('Location: panier.php?error=1');
    }
} else {
    // Requête invalide
    header('Location: panier.php?error=3');
}
exit();
