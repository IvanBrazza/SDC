<?php
  require("../lib/common.php");
  $title = "Private";

  if (empty($_SESSION['user']))
  {
    header("Location: ../login");
    die("Redirecting to login.php");
  }
?>
<?php include("../lib/header.php"); ?>
  <h1>Welcome, <?php echo htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8'); ?>, secret content!</h1>
<?php include("../lib/footer.php"); ?>
