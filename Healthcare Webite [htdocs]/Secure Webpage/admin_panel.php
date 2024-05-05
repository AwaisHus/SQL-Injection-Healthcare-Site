<?php
session_start();
include 'db_connection.php';
include 'delete_user.php';

// Check if the user is authenticated as an admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) { // Check if is_admin is not set or false
    header("Location: accountindex.php"); // Redirect non-admin users
    exit();
}

$tables = []; // Array to store table names
$columns = []; // Array to store column names
$message = ""; //Holds Messages

// Fetch all table names from the database for admin to select from
$tablesQuery = "SHOW TABLES";
$tablesResult = mysqli_query($conn, $tablesQuery);

while ($table = mysqli_fetch_row($tablesResult)) {
    $tables[] = $table[0];
}
mysqli_free_result($tablesResult);

$patients = [];

if (isset($_POST['selected_table'])) {
    $selectedTable = $_POST['selected_table'];

    if ($selectedTable == 'patient logins') {
        // Fetch all data from the selected table including the role name
        $query = "SELECT pl.*, ur.Role AS 'Role Name' FROM `patient logins` pl LEFT JOIN `user roles` ur ON pl.`Role ID` = ur.`Role ID`";
    } else {
        // Fetch all data from the selected table
        $query = "SELECT * FROM `$selectedTable`";
    }
    
    // Fetch column details from the selected table
    $columnsQuery = "SHOW COLUMNS FROM `$selectedTable`";
    $columnsResult = mysqli_query($conn, $columnsQuery);
    while ($column = mysqli_fetch_assoc($columnsResult)) {
        $columns[] = $column['Field']; // This assumes 'Field' is the name of the column in your result that contains the column name
    }
    mysqli_free_result($columnsResult);

    $result = mysqli_query($conn, $query);
    if ($result) {
        $patients = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_free_result($result);
        if ($selectedTable == 'patient logins') {
            $columns = array_keys(current($patients)); // Update columns to include 'RoleName' if not present
        }
    } else {
        $message = "Error: Could not fetch data from `$selectedTable`.";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - View Tables</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<div class="table-responsive">
    <div class="d-flex justify-content-between align-items-center bg-light p-3 mb-2">
        <form action="admin_panel.php" method="post" class="d-flex justify-content-start">
            <select name="selected_table" class="form-select me-2">
                <?php foreach ($tables as $table): ?>
                    <option value="<?php echo htmlspecialchars($table); ?>" <?php if (isset($selectedTable) && $selectedTable === $table) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($table); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="Confirm" class="btn btn-primary"/>
        </form>
        <div class="d-flex justify-content-between mb-2">
            <a href="homepage.php" class="btn btn-primary me-2">Back to Portal</a>
            <a href="create_user.php" class="btn btn-success">Create User</a> <!-- 'me-2' adds margin to the right -->    
        </div>
        </div>
    </div>
    </table>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <?php foreach ($columns as $columnName): ?>
                        <th><?php echo htmlspecialchars($columnName); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <?php foreach ($patient as $key => $value): ?>
                            <td><?php echo htmlspecialchars($value); ?></td>
                        <?php endforeach; ?>
                        <td>
                            <?php if ($selectedTable !== 'user roles'): ?>
                                <a href="edit_user.php?id=<?php echo htmlspecialchars($patient['ID']); ?>" class="btn btn-primary btn-sm">Edit</a>
                                <form action="delete_user.php" method="post" onsubmit="return confirm('Are you sure you want to delete this user?');" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($patient['ID']); ?>">
                                <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                            </form>

                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    
    
    <script>
    $(document).ready(function() {
        $('.delete-user').on('click', function(e) {
            e.preventDefault();

            if (!confirm('Are you sure you want to delete this user?')) {
                return false;
            }

            var userId = $(this).data('user-id');
            var row = $(this).closest('tr');
            
            // Correct the syntax by removing the extra parenthesis after function
            $.post('delete_user.php', { id: userId, delete: 'yes' }, function(response) {
                // Check the response, if success, remove the row from the table
                if (response.success) {
                    row.fadeOut(400, function() {
                        $(this).remove();
                        location.reload();
                    });
                } else {
                    alert("Error: Could not delete user.");
                }
            }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            });
        });
    });
    </script>

    <script>
        //Debugging to see if all jQuery operations are functional.
        $(document).ready(function() {
            console.log("jQuery is loaded");
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            if (window.location.search.indexOf('table=') > -1) {
                // Submit the form automatically to load the table data
                document.querySelector('form').submit();
            }
        });
    </script>

</body>
</html>
