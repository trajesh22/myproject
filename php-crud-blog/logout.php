<?php
// Start or resume the session
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login
header("Location: login.php");
exit();
?>
