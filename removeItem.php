<?php
require 'db.php'; 
session_start();

// Determine the user identifier
if (isset($_SESSION['user_id'])) {
    $userIdentifier = $_SESSION['user_id'];
} elseif (isset($_COOKIE['userTemp'])) {
    $userIdentifier = $_COOKIE['userTemp'];
} else {
    // If there's no user identifier, redirect to the cart page with an error
    header("Location: panier.php?error=No%20user%20identifier%20found");
    exit();
}

// Check if the item ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $itemId = intval($_GET['id']); // Ensure the ID is an integer

    try {
        $db = Database::connect(); // Connect to the database

        // Prepare the DELETE query
        $query = "DELETE FROM panier WHERE id_item = :id_item AND userTemp = :userTemp";
        $stmt = $db->prepare($query);

        // Bind parameters
        $stmt->bindParam(':id_item', $itemId, PDO::PARAM_INT);
        $stmt->bindParam(':userTemp', $userIdentifier, PDO::PARAM_STR);

       
        if ($stmt->execute()) {
            // Redirect to the cart page with a success message
            header("Location: panier.php?message=Item%20removed%20successfully");
            exit();
        } else {
            // Redirect to the cart page with an error message
            header("Location: panier.php?error=Failed%20to%20remove%20item");
            exit();
        }
    } catch (Exception $e) { 
        // Handle database errors
        error_log($e->getMessage());
        header("Location: panier.php?error=An%20unexpected%20error%20occurred");
        exit();
    } finally {
        Database::disconnect(); // Ensure the database connection is closed
    }
} else {
    // Redirect if no ID is provided
    header("Location: panier.php?error=Invalid%20item%20ID");
    exit();
}