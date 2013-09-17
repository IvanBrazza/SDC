<?php
  require("../lib/common.php");
  $title = "Home";

  if (!empty($_SESSION['user']))
  {
    header("Location: ../private");
    die();
  } 

  include("../lib/header.php");
  include("../lib/footer.php");
?>
