<?php

include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve values from the registration form
    $nhs_number = $_POST["nhs_number"];
    $email = $_POST["email"];
    $username = $_POST["new_username"];
    $roleId = filter_input(INPUT_POST, "role_id", FILTER_DEFAULT);

    // Check for duplicates
    $checkQuery = "SELECT * FROM `patient logins` WHERE `NHS_#` = '$nhs_number' OR `Email` = '$email' OR `Login` = '$username'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) == 0) {
        // Values do not exist, proceed with the insertion
        $first_name = $_POST["first_name"];
        $last_name = $_POST["last_name"];
        $password = $_POST["new_password"];

        // Hashing the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Fetch Role ID for 'patient' from the roles table
        $roleQuery = "SELECT `Role ID` FROM `patient logins` WHERE `Role ID` = '103'";
        $roleResult = mysqli_query($conn, $roleQuery);
        $roleData = mysqli_fetch_assoc($roleResult);
        $role_id = $roleData['Role ID'];

        // Perform the insertion into the database with role ID
        $insertQuery = "INSERT INTO `patient logins` (`NHS_#`, `First Name`, `Last Name`, `Email`, `Login`, `Password`, `Role ID`)
                       VALUES ('$nhs_number', '$first_name', '$last_name', '$email', '$username', '$hashedPassword', '$role_id')";
        $insertResult = mysqli_query($conn, $insertQuery);

        // Check for errors
        if (!$insertResult) {
            echo "Error: " . mysqli_error($conn);
        } else {
            echo "Registration successful! Redirecting to login page...";
            // JavaScript redirection after 3 seconds
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'accountindex.php';
                    }, 3000);
                  </script>";
        }
    } else {
        echo "Duplicate values detected. Entry not added. Redirecting to registration page...";
        // JavaScript redirection after 3 seconds
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'accountindex.php';
                }, 3000);
              </script>";
    }
    include 'db_close.php';
}
?>
