<?php 

    session_start();
    include 'db_connection.php';

    if (!isset($_SESSION['username'])) {
        // Redirect to the login page if not logged in
        header("Location: accountindex.php");
        exit();
    }

    $username = $_SESSION['username']; // Get the username from the session
    $query = "SELECT `First Name`, `Last Name`, `NHS_#`, `Role ID` FROM `patient logins` WHERE `Login` = '" . $username . "'";
    
    $isAdmin = strtolower($username) === 'admin';

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $firstName = $user['First Name'];
        $lastName = $user['Last Name'];
        $nhsNumber = $user['NHS_#'];
        $_SESSION['role_id'] = $user['Role ID'];

        // Set boolean session variables based on the user's role ID
        $_SESSION['is_admin'] = ($_SESSION['role_id'] == 101); // Assuming 101 is the Role ID for admin
        $_SESSION['is_doctor'] = ($_SESSION['role_id'] == 102); // Assuming 102 is the Role ID for doctor
        $_SESSION['is_patient'] = ($_SESSION['role_id'] == 103); // Assuming 103 is the Role ID for patient
    
    } else {
        echo "User not found.";
        exit();
    }

    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NHS Healthcare Portal - Homepage</title>
    <link rel="stylesheet" href="styles/homepage.css">
</head>
<body>
    <header class="site-header">
        <div class="logo">NHS Portal
        </div>
        <div class="account-nav">
            <form id="logoutForm" action="logout.php" method="post" style="display: none;"></form>
            <button id="logoutButton" onclick="document.getElementById('logoutForm').submit();">Log Out</button>
        </div>
    </header>

    <div class="container">
        <div id="leftPanel">
            <h2>Welcome Back, <?php echo htmlspecialchars($firstName) . ' ' . htmlspecialchars($lastName); ?></h2>
            <p>NHS Number: <?php echo htmlspecialchars($nhsNumber); ?></p>
            <a href="manage_account.php" class="button" id="manageAccount">Manage Account</a>
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                <!-- Manage Users button will only appear for admin -->
                <a href="admin_panel.php" class="button" id="adminPanel">Admin Panel</a>
            <?php endif; ?>
        </div>
        <div id="rightPanel">
            <?php if ($_SESSION['is_doctor']): ?>
                <!-- Request Medicine button will only appear for doctors -->
                <a href="request_medicine.php" class="button">Request Medicine</a>
            <?php endif; ?>
            <a href="patchs.php" class="button">PATCHS</a>
            <a href="appointments.php" class="button">Appointments</a>
            <a href="medical_records.php" class="button">Medical Records</a>
            <a href="diagnosis.php" class="button">Diagnosis</a>
        </div>        
    </div>

    <footer class="site-footer">
        © 2024 Healthcare Portal
    </footer>

</body>
</html>
