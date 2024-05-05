<?php
    session_start();
    unset($_SESSION['username']);
    session_destroy();
    header("Location: index.php"); // Redirect to the home page after logout
    exit();
?>