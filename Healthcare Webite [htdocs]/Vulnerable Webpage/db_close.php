<?php
// Check if the $conn variable is set and not null before trying to close it
global $conn;
if (isset($conn) && $conn instanceof mysqli && $conn->ping()) {
    $conn->close();
}
?>
