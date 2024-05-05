<?php
session_start();
include 'db_connection.php';

$message = '';
$userData = [];

if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    $query = "SELECT * FROM `patient logins` WHERE ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $userData = $result->fetch_assoc();
    } else {
        $message = 'No user found with that ID.';
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $login = $_POST['login'];
    $password = $_POST['password'];
    $roleId = $_POST['role_id'];

    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    } else {
        // Keep the old password if not changed
        $hashedPassword = $userData['Password']; 
    }
    
    // Update query with all the fields
    $updateQuery = "UPDATE `patient logins` SET `First Name` = ?, `Last Name` = ?, `Email` = ?, `Login` = ?, `Password` = ?, `Role ID` = ? WHERE `ID` = ?";
    $stmt = $conn->prepare($updateQuery);
    echo "Role ID being updated: $roleId";
    $stmt->bind_param("sssssii", $firstName, $lastName, $email, $login, $hashedPassword, $roleId, $userId);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $message = 'User updated successfully.';
    } else {
        $message = 'Error updating user or no changes made.';
    }
    $stmt->close();
    
    // Redirect back to the admin panel or refresh to show updated data
    header('Location: admin_panel.php?table=patient_logins');
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
    <h2>Edit User</h2>
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="edit_user.php?id=<?php echo htmlspecialchars($userId); ?>" method="post">
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
                <label for="login" class="form-label">Login:</label>
                <input type="text" class="form-control" id="login" name="login" required value="<?php echo htmlspecialchars($userData['Login'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required value="<?php echo htmlspecialchars($userData['Password'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label for="user_id" class="form-label">User ID:</label>
                <input type="text" class="form-control" id="user_id" name="user_id" value="<?php echo htmlspecialchars($userData['ID'] ?? ''); ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="nhs_number" class="form-label">NHS Number:</label>
                <input type="text" class="form-control" id="nhs_number" name="nhs_number" value="<?php echo htmlspecialchars($userData['NHS_#'] ?? ''); ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="role_id" id="role_id" class="form-label">Role:</label>
                <select name="role_id" id="role_id" class="form-control">
                    <option value="103" <?php echo ($userData['Role ID'] == 103) ? 'selected' : ''; ?>>Patient</option>
                    <option value="102" <?php echo ($userData['Role ID'] == 102) ? 'selected' : ''; ?>>Doctor</option>
                    <option value="101" <?php echo ($userData['Role ID'] == 101) ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="admin_panel.php?table=patient_logins" class="btn btn-danger">Cancel</a>
        </form>
    </div>
</body>
</html>
