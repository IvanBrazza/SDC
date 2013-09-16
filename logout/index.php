<?php
  require("../common.php");

  unset($_SESSION['user']);

  header("Location: ../login");
  die("Redirecting to login.php");
