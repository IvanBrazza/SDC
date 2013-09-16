<?php
  require("../common.php");
  $title = "Home";

  if (!empty($_SESSION['user']))
  {
    header("Location: ../private");
    die();
  } 

  include("../header.php");
  include("../footer.php");
?>
