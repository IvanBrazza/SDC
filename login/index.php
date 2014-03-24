<?php
  /**
    login/ - display a login form to the user so they can
    log into the site.
  **/
  require("../lib/common.php");
  $title                = "Log In";
  $page                 = "login";

  // If the user is already logged in, redirect them to the
  // homepage
  if (!empty($_SESSION['user']))
  {
    header("Location: ../home/");
    die();
  }

  // Use HTTPS since secure data is being transferred
  forceHTTPS();

  // Set the error text if the page has been redirected due
  // to an error
  if(!empty($_GET['e']))
  {
    if ($_GET['e'] === 'pao')
    {
      $display_message = "Please login/register to place an order";
    }
    else if ($_GET['e'] === 'yo')
    {
      $display_message = "Please login/register to view your orders";
    }
  }

  // Generate token
  $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");
?>
<?php include("../lib/header.php"); ?>
<div class="row">
  <div class="col-md-12">
    <h1>Login</h1>
  </div>
</div>
<div class="row">
  <div class="col-md-4"></div>
  <div class="col-md-4">
    <div class="alert alert-danger" id="error_message">
      <?php echo $display_message; ?>
    </div>
    <form action="index.php" method="post" id="login-form" role="form">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" class="form-control" name="username" id="username" onchange="validate.username()" placeholder="Enter Username">
      </div>
      <div id="username-error" class="validate-error"></div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" name="password" id="password" onchange="validate.password()" placeholder="Enter Password">
      </div>
      <div id="password-error" class="validate-error"></div>
      <a href="../forgot-password" class="forgot-password">Forgot Password</a>
      <br /><br />
      <?php if (!empty($_GET['redirect'])) : ?>
        <input type="hidden" value="<?php echo $_GET['redirect']; ?>" name="redirect">
      <?php endif; ?>
      <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token" id="token">
      <button type="submit" class="btn btn-default">Login</button>
    </form>
  </div>
  <div class="col-md-4"></div>
</div>
<?php include("../lib/footer.php");
