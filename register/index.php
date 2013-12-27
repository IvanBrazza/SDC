<?php
  /**
   register/ - display a form to the user so they can
   register to use the site.
  **/
  require("../lib/common.php");
  $title  = "Register";
  $page   = "register";

  // Generate token
  $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");
?>
<?php include("../lib/header.php"); ?>
  <div class="form">
    <h1>Register</h1>
    <script type="text/javascript">
      var RecaptchaOptions = {
        theme : 'clean'
      };
    </script>
    <form action="index.php" method="post" id="register-form">
      <div>
        <label for="username">Username</label>
        <input type="text" name="username" id="username" onchange="validate.username()">
      </div>
      <div id="username-error" class="validate-error"></div>
      <div>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" onchange="validate.password()">
      </div>
      <div id="password-error" class="validate-error"></div>
      <div>
        <label for="password2">Reenter Password</label>
        <input type="password" name="password2" id="password2" onchange="validate.password2()">
      </div>
      <div id="password2-error" class="validate-error"></div>
      <div>
        <label for="email">EMail</label>
        <input type="text" name="email" id="email" onchange="validate.email()">
      </div>
      <div id="email-error" class="validate-error"></div>
      <div class="error">
        <span class="error_message" id="error_message">
          <?php echo $display_message; ?>
        </span>
      </div>
      <?php
        require_once("../lib/recaptchalib.php");
        $publickey = "6LePfucSAAAAAKlUO3GQKgfXCd7SvIhtFjBH5F9Z";
        echo recaptcha_get_html($publickey, null, true);
      ?>
      <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
      <input type="submit" value="Register" name="submit" />
    </form>
  </div>
<?php include("../lib/footer.php"); ?>
