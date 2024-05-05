<?php
session_start();

include 'db_connection.php';

// Define the role constants
define('ROLE_ADMIN', 101);
define('ROLE_DOCTOR', 102);
define('ROLE_PATIENT', 103);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $action = $_POST['action']; // This determines the Login or Test Login action

        // Prepare statement to avoid SQL injection
        $stmt = $conn->prepare("SELECT * FROM `patient logins` WHERE `Login` = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $_SESSION['executedSQL'] = "SELECT * FROM `patient logins` WHERE `Login` = ?"; // Store prepared SQL for display
        
        if ($result->num_rows > 0) {
            while ($userRecord = $result->fetch_assoc()) {
                // Setup session variables and log the user in
                $_SESSION['username'] = $userRecord['Login'];
                $_SESSION['role_id'] = $userRecord['Role ID'];
                $_SESSION['is_admin'] = ($userRecord['Role ID'] == ROLE_ADMIN);
                $_SESSION['is_doctor'] = ($userRecord['Role ID'] == ROLE_DOCTOR);
                $_SESSION['is_patient'] = ($userRecord['Role ID'] == ROLE_PATIENT);
                $_SESSION['message'] = "Login Successful, Redirecting...";

                if ($action === 'login') {
                    header("Location: homepage.php");
                    exit();
                } else {
                    header("Location: accountindex.php"); // Stay on accountindex.php for login test
                    exit();
                }
            }
        } else {
            $_SESSION['errorMsg'] = "Unsuccessful login. Please check your username and password.";
            header("Location: accountindex.php");
            exit();
        }
        $stmt->close();
    }
} catch (mysqli_sql_exception $exception) {
    $_SESSION['errorMsg'] = "SQL Error: " . $exception->getMessage();
    header("Location: accountindex.php");
    exit();
}
?>
