<?php
include 'db_connection.php';

if (isset($_POST['delete'])) {
    $id = $_POST['id']; // Get the ID of the user to delete

    // First, check if the user is an admin
    $roleCheckStmt = $conn->prepare("SELECT `Role ID` FROM `patient logins` WHERE ID = ?");
    $roleCheckStmt->bind_param("i", $id);
    $roleCheckStmt->execute();
    $roleCheckStmt->store_result();
    $roleCheckStmt->bind_result($roleId);
    $roleCheckStmt->fetch();

    if ($roleId == 101) { // Assuming 101 is the admin Role ID
        echo "Error: Cannot delete admin user. Redirecting...";

        echo '<script>
            setTimeout(function() {
                window.location.href = "admin_panel.php"; // Redirect to the admin panel or another appropriate page
            }, 3000);
          </script>';

        exit();
    }

    $roleCheckStmt->close();

    // Prepare a DELETE statement to prevent SQL injection if not admin
    $stmt = $conn->prepare("DELETE FROM `patient logins` WHERE ID = ?");
    $stmt->bind_param("i", $id); // "i" for integer type
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // If the delete was successful, redirect back to the admin panel
        header('Location: admin_panel.php?table=patient logins&msg=delete_success');
        exit();
    } else {
        // If the delete was not successful, output an error message
        echo "Error: Could not delete user.";
    }
    $stmt->close();
}
?>
