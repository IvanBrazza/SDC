<?php
  /**
    logout.php - log the user out of the system by unsetting the session.
  **/
  require("common.php");

  // Unset the session
  unset($_SESSION['user']);

  // Redirect to the login page
  header("Location: ../login");
  die("Redirecting to login.php");
?>
