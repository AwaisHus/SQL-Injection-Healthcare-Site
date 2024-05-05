<?php
session_start();
include 'db_connection.php';

$message = ''; // To store messages about the operation

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
    $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $nhsNumber = filter_input(INPUT_POST, 'nhs_number', FILTER_SANITIZE_NUMBER_INT);
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);
    $roleId = filter_input(INPUT_POST, 'role_id', FILTER_DEFAULT);

     if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    } else {
        // Keep the old password if not changed
        $hashedPassword = $userData['Password']; 
    }

    // Insert into the database
    if ($firstName && $lastName && $email && $login && $password) {
        $insertQuery = "INSERT INTO `patient logins` (`First Name`, `Last Name`, `Email`, `NHS_#`, `Login`, `Password`, `Role ID`) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        if ($stmt) {
            $stmt->bind_param("sssissi", $firstName, $lastName, $email, $nhsNumber, $login, $hashedPassword, $roleId);
            if ($stmt->execute()) {
                $_SESSION['table_open'] = true;
                $message = 'User created successfully. Redirecting within 3 seconds...';
                header('Refresh:3; url=admin_panel.php?table=patient_logins');
            } else {
                $message = 'Error creating user: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = 'Error preparing statement: ' . $conn->error;
        }
    } else {
        $message = 'Please fill in all required fields.';
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
        <h2>Create New User</h2>

        <?php if (!empty($message)): ?>
            <div class="alert alert-info">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form action="create_user.php" method="post">
            <div class="mb-3">
                <label for="first_name" class="form-label">First Name:</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name:</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="login" class="form-label">Login:</label>
                <input type="text" class="form-control" id="login" name="login" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="nhs_number" class="form-label">NHS Number:</label>
                <input type="text" class="form-control" id="nhs_number" name="nhs_number" required>
            </div>
            <div class="mb-3">
            <label for="role_id" class="form-label">Role:</label>
                <select name="role_id" id="role_id" class="form-control">
                <option value="103">Patient</option>
                <option value="102">Doctor</option>    
                <option value="101">Admin</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Create User</button>
            <a href="admin_panel.php?table=patient_logins" class="btn btn-danger">Cancel</a>
        </form>
    </div>
</body>
</html>
