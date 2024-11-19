<?php 
require '../db.php';
// require 'verifRole.php';
$db = DataBase::connect();

$query = 'SELECT items.*, categories.name AS category_name FROM items
    JOIN categories
    ON items.category = categories.id
    WHERE items.id = :id';

    $stmt = $db->prepare($query);
    $stmt->execute(['id' => $_GET['id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);





?>
<!DOCTYPE html>
<html>
    <head>
    <title>Burger Code</title>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <link href="	https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="	https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
        <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
        <link rel="stylesheet" href="../styles.css">
    </head>

    <body>
        <h1 class="text-logo"> Burger Code </h1>
        <div class="container admin">
            <div class="row">
                <div class="col-md-6">
                    <h1><strong>Modifier un item</strong></h1>
                    <br>
                    <form class="form" action="updateREQ.php" role="form" method="post" enctype="multipart/form-data" >
                        <br>
                        <div>
                            <label class="form-label" for="name">Nom:</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nom" value="<?php echo $product['name']; ?>">
                            <span class="help-inline"></span>
                        </div>
                        <br>
                        <div>
                            <label class="form-label" for="description">Description:</label>
                            <input type="text" class="form-control" id="description" name="description" placeholder="Description" value="<?= $product['description']; ?>">
                            <span class="help-inline"></span>
                        </div>
                        <br>
                        <div>
                        <label class="form-label" for="price">Prix: (en €)</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="Prix" value="<?= $product['price']; ?>">
                            <span class="help-inline"></span>
                        </div>
                        <br>
                        <div>
                            <label class="form-label" for="category">Catégorie:</label>
                            <select class="form-control" id="category" name="category">
                            <?php foreach ($db->query("SELECT * FROM categories") as $categ) { ?>

                            <option value='<?= $categ['id'] ?>' <?= $categ['id'] == $product["category"] ? "selected" : "" ?>><?= $categ['name'] ?></option>

                                <?php } ?>

                            </select>
                            <span class="help-inline"></span>
                        </div>
                        <br>
                        <div>
                            <label class="form-label" for="image">Image:</label>
                            <p><?= $product['image'] ?></p>
                            <div class="help-inline"><img src="../images/<?= $product['image'] ?>" alt="" style="width: 300px;">
                            <?= $product['image'] ?></div>
                            <label for="image">Sélectionner une nouvelle image:</label>
                            <input type="file" id="image" name="image"> 
                        </div>
                        <input type="hidden" value="<?= $product['id'] ?>" name="id">
                        <br>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-success"><span class="bi-pencil"></span> Modifier</button>
                            <a class="btn btn-primary" href="index.html"><span class="bi-arrow-left"></span> Retour</a> 
                       </div>
                    </form>
                </div>
                
            </div>
        </div>   
    </body>
</html>
