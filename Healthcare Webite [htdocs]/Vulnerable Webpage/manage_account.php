<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: accountindex.php");
    exit();
}

$message = '';
$userData = [];

// Fetch user's data based on session username
$username = $_SESSION['username'];
$query = "SELECT * FROM `patient logins` WHERE `Login` = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $userData = $result->fetch_assoc();
} else {
    $message = 'User data not found.';
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($password)) {
        // Hash new password if it's not empty
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    } else {
        // Use old password if no new password is entered
        $hashedPassword = $userData['Password'];
    }

    // Update query with all the fields
    $updateQuery = "UPDATE `patient logins` SET `First Name` = ?, `Last Name` = ?, `Email` = ?, `Password` = ? WHERE `Login` = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssss", $firstName, $lastName, $email, $hashedPassword, $username);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $message = 'Account updated successfully.';
    } else {
        $message = 'No changes made or error updating account.';
    }
    $stmt->close();

    // Refresh the session data
    $_SESSION['username'] = $username;
    header('Location: homepage.php'); // Redirect back to the homepage or refresh the page
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Account</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Manage Account</h2>
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="manage_account.php" method="post">
            <div class="mb-3">
                <label for="first_name" class="form-label">First Name:</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required value="<?php echo htmlspecialchars($userData['First Name'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name:</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required value="<?php echo htmlspecialchars($userData['Last Name'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($userData['Email'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required value="<?php echo htmlspecialchars($userData['Password'] ?? ''); ?>">
            </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="homepage.php" class="btn btn-danger">Cancel</a>
            </form>
            </div>

</body>
</html>
