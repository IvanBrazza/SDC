<?php
  /**
    logout.php - log the user out of the system by unsetting the session.
  **/
  require("common.php");

  unset($_SESSION['user']);

  header("Location: ../login");
  die("Redirecting to login.php");
?>
