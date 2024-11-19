<?php
require '../db.php';

$db = DataBase::connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
        $id = intval($_POST['id']); // Secure against SQL injection

        $query = "DELETE FROM items WHERE id = ?";
        $stmt = $db->prepare($query);

        try {
            $stmt->execute([$id]);
            header('Location: index.php?deleted=true');
        } catch (PDOException $e) {
            header('Location: index.php?error=deletion_failed');
        }
    } else {
        header('Location: index.php?error=invalid_id');
    }
    exit;
}
