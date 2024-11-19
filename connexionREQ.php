<?php
require 'db.php'; 
session_start();  // Start the session

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get user input from POST request
    $mail = trim($_POST['mail']);
    $mdp = $_POST['mdp']; // Password entered by the user

    // Validate the input
    if (!empty($mail) && !empty($mdp)) {

        // Create the database connection
        $db = Database::connect();

        // Prepare the query to find the user by email
        $query = "SELECT * FROM users WHERE email = :mail";
        $stmt = $db->prepare($query);
        $stmt->execute([':mail' => $mail]);

        // Fetch the user data
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the user exists
        if ($user) {
            // Verify the entered password with the stored hash
            if (password_verify($mdp, $user['password'])) {
                // Password is correct, start the session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                // Update the cart to use userId instead of userTemp
                $query = "UPDATE panier SET userTemp = :userId WHERE userTemp = :userTemp";
                $stmt = $db->prepare($query);
                $stmt->execute([':userId' => $_SESSION['user_id'], ':userTemp' => $_COOKIE['userTemp']]);

                // Redirect to a protected page, like the user's profile or home page
                header('Location: index.php');
                exit; // Make sure to stop further script execution after redirection
            } else {
                // Password is incorrect
                $error = "Mot de passe incorrect";
            }
        } else {
            // User does not exist
            $error = "Aucun utilisateur trouvé avec cet email";
        }

        // Disconnect from the database
        Database::disconnect();

    } else {
        // Missing email or password
        $error = "Veuillez remplir tous les champs";
    }

    // If there was an error, redirect with the error message
    if (isset($error)) {
        header('Location: inscription.php?incorrect=' . urlencode($error));
        exit;
    }
}
?>