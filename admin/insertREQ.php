<?php

require '../db.php';

$db = DataBase::connect();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1MO → 1024 * 1024
    // 1GO → 1024 * 1024 * 1024
    if (isset($_FILES['image']) && $_FILES['image']['size'] <= 1024 * 1024) {

        if (isset($_POST['name']) && isset($_POST['description']) && isset($_POST['price']) && isset($_POST['category'])) {

            $extention = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $allowedExt = ['jpg', 'png', 'jpeg'];

            if (in_array($extention, $allowedExt)) {

                $newName = uniqid('img') . '.' . $extention;
                move_uploaded_file($_FILES['image']['tmp_name'], '../images/' . $newName);

                $nom = htmlspecialchars($_POST['name']);
                $description = htmlspecialchars($_POST['description']);
                $price = htmlspecialchars($_POST['price']);
                $category = htmlspecialchars($_POST['category']);

                $query = "INSERT INTO items (name, description, price, category, image) VALUES (?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$nom, $description, $price, $category, $newName]);

                header('Location: index.php');
            }
        }
    }
}

$db = DataBase::disconnect();