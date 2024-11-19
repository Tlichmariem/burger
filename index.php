<?php 
require 'db.php';
$db = Database::connect();
$query = "SELECT * FROM categories";
$parentCateg = $db->query($query)->fetchALL(PDO::FETCH_ASSOC);

$query2 = "SELECT * FROM items";
$parentProduit = $db->query($query2)->fetchALL(PDO::FETCH_ASSOC);

session_start();

if (!isset($_SESSION['userTemp']))
{
    $_SESSION['userTemp'] = uniqid();
} else {
    $_SESSION['userTemp'] = $_SESSION['userTemp'];
}

$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Burger Code</title>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
        
        <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <div class="container site">
           
            <div style="text-align:center; display:flex; justify-content:center; align-items:center" class="text-logo">
                <h1>Burger Doe</h1>
                <div class="icon-container">
                    <a href="panier.php" class="bi bi-basket3 cart-icon"></a>
                    <div class="dropdown">
                        <a href="#" class="bi bi-person-circle user-icon" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"></a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <?php if ($isLoggedIn): ?>
                                <li><a class="dropdown-item" href="suivi_commandes.php">Suivi de commande</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="deconnexion.php">Log out</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="inscription.php">Log in</a></li>
                            <?php endif; ?>
                        </ul>

                    </div>
                </div>
            </div>
            
            <nav>
                <ul class="nav nav-pills" role="tablist">
                    <?php foreach($parentCateg as $categ){?>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link <?= $categ["id"]==1 ? "active" : "" ?>" data-bs-toggle="pill" data-bs-target="#<?= $categ["id"]?>" role="tab"><?= $categ['name'];?></a>
                    </li>
                    <?php } ?>
                </ul>
            </nav>

            <div class="tab-content">
            <?php foreach($parentCateg as $categ){ ?>
                <div class="tab-pane <?= $categ["id"]==1 ? "active" : "" ?>" id="<?=$categ["id"]?>" role="tabpanel">
                    <div class="row">
                        <?php foreach($parentProduit as $produit) {
                            if($produit["category"] == $categ["id"]){
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="img-thumbnail">
                                <img src="images/<?= $produit["image"]?>" class="img-fluid" alt="<?= $produit["name"] ?>">
                                <div class="price"><?= $produit["price"] ?></div>
                                <div class="caption">
                                    <h4><?= $produit["name"] ?></h4>
                                    <p><?= $produit["description"] ?></p>
                                    <a href="addpanierREQ.php?id=<?php echo $produit["id"] ?>&prix=<?php echo $produit["price"] ?>" class="btn btn-order" role="button"><span class="bi-cart-fill"></span> Commander</a>
                                </div>
                            </div>
                        </div>
                        <?php }}?>
                    </div>
                </div>
            <?php } ?>
            </div>
        </div>
    </body>
</html>