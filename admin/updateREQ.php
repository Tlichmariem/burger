<?php

require '../db.php';

$db = DataBase::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image = '';

    if (isset($_FILES['images']) && $_FILES['images']['size'] <= 1024 * 1024) {
        $extension = pathinfo($_FILES['images']['name'], PATHINFO_EXTENSION);
        $allowedExt = ['jpg', 'png', 'jpeg'];

        if (in_array($extension, $allowedExt)) {
            $image = uniqid('img') . '.' . $extension;
            move_uploaded_file($_FILES['images']['tmp_name'], '../images/' . $image);
        }
    } else {
        // Retrieve the existing image if no new file is uploaded
        $query = "SELECT image FROM items WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->execute([':id' => intval($_POST['id'])]);
        $image = $stmt->fetchColumn();
    }

    if (isset($_POST['name'], $_POST['description'], $_POST['price'], $_POST['category'], $_POST['id'])) {
        $name = htmlspecialchars($_POST['name']);
        $description = htmlspecialchars($_POST['description']);
        $price = htmlspecialchars($_POST['price']);
        $category = htmlspecialchars($_POST['category']);
        $id = intval($_POST['id']);

        $query = "UPDATE items SET name = ?, description = ?, price = ?, category = ?, image = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$name, $description, $price, $category, $image, $id]);

        header('Location: index.php');
        exit;
    }
}
