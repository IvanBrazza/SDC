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

  // Set the error text if the page has been redirected due
  // to an error
  if(!empty($_GET['e']))
  {
    if ($_GET['e'] === 'pao')
    {
      $display_message = "Please login/register to place an order";
    }
  }

  // Generate token
  $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");
?>
<?php include("../lib/header.php"); ?>
  <div class="error">
    <span class="error_message" id="error_message">
      <?php echo $display_message; ?>
    </span>
  </div>
  <div class="form">
    <h1>Login</h1> 
    <form action="index.php" method="post" id="login-form"> 
      <div>
        <label for="username">Username</label>
        <input type="text" name="username" id="username" onchange="validateUsername()"/>
      </div>
      <div id="username-error" class="validate-error"></div>
      <div>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" onchange="validatePassword()" />
      </div>
      <div id="password-error" class="validate-error"></div>
      <a href="../forgot-password" class="forgot-password">Forgot Password</a>
      <br /><br />
      <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
      <input type="submit" value="Login" />
      <span class="ajax-load"></span>
    </form> 
  </div>
<?php include("../lib/footer.php");
