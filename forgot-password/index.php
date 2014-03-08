<?php
  /**
    forgot-password/ - display a page to the user to email them if
    they have forgotten their password.
  **/
  require("../lib/common.php");
  $title = "Forgot Password";

  forceHTTPS();

  // Generate token
  $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");
?>
<?php include("../lib/header.php"); ?>
  <?php if ($_POST) : ?>
    <p>We've sent you a new password to your email address <?php echo $_POST['email']; ?>.</p>
  <?php else : ?>
    <div class="form">
      <h1>Forgot Password</h1>
      <p>Enter the email you registered with below and we'll send you an email containing your new password.</p>
      <div class="error">
        <span class="error_message" id="error_message">
          <?php echo $display_message; ?>
        </span>
      </div>
      <div class="success">
        <span class="success_message" id="success_message"></span>
      </div>
      <form action="index.php" method="POST" id="forgot-password-form">
        <div>
          <label for="email">EMail</label>
          <input type="text" name="email" id="email" onchange="validate.email()">
        </div>
        <div id="email-error" class="validate-error"></div>
        <input type="hidden" value="<?php echo $_SESSION['token']; ?>" id="token" name="token">
        <input type="submit" value="Reset Password" name="submit">
      </form>
    </div>
  <?php endif; ?>
<?php include("../lib/footer.php"); ?>
