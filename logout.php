<?php
session_start();
session_unset();
session_destroy();
// Redirect back to login page after destroying the session
header("Location: login.php");
exit;
?>