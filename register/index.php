<?php
  /**
   register/ - display a form to the user so they can
   register to use the site.
  **/
  require("../lib/common.php");
  $title  = "Register";
  $page   = "register";

  // Use HTTPS since secure data is being transferred
  forceHTTPS();

  // Generate token
  $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");
?>
<?php include("../lib/header.php"); ?>
<div class="row">
  <div class="col-md-3"></div>
  <div class="col-md-6">
    <h1>Register</h1>
    <script type="text/javascript">
      var RecaptchaOptions = {
        theme : 'clean'
      };
    </script>
    <form action="index.php" method="post" id="register-form" class="form-horizontal" role="form">
      <div class="form-group">
        <label for="username" class="col-sm-4 control-label">Username</label>
        <div class="col-sm-1"></div>
        <div class="col-sm-7">
          <input type="text" class="form-control" name="username" id="username" onchange="validate.username()">
          <div id="username-error" class="validate-error"></div>
        </div>
      </div>
      <div class="form-group">
        <label for="password" class="col-sm-4 control-label">Password</label>
        <div class="col-sm-1"></div>
        <div class="col-sm-7">
          <input type="password" class="form-control" name="password" id="password" onchange="validate.password()">
          <div id="password-error" class="validate-error"></div>
        </div>
      </div>
      <div class="form-group">
        <label for="password2" class="col-sm-4 control-label">Reenter Password</label>
        <div class="col-sm-1"></div>
        <div class="col-sm-7">
          <input type="password" class="form-control" name="password2" id="password2" onchange="validate.password2()">
          <div id="password2-error" class="validate-error"></div>
        </div>
      </div>
      <div class="form-group">
        <label for="email" class="col-sm-4 control-label">EMail</label>
        <div class="col-sm-1"></div>
        <div class="col-sm-7">
          <input type="text" class="form-control" name="email" id="email" onchange="validate.email()">
          <div id="email-error" class="validate-error"></div>
        </div>
      </div>
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
      <button type="submit" name="submit" class="btn btn-default">Register</button>
    </form>
  </div>
  <div class="col-md-3"></div>
</div>
<?php include("../lib/footer.php"); ?>
