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
<div class="row">
  <div class="col-md-3"></div>
  <div class="col-md-6">
    <h1>Register</h1>
    <form action="index.php" method="post" id="register-form" class="form-horizontal" role="form">
      <div class="form-group">
        <label for="username" class="col-sm-4 control-label">Username</label>
        <div class="col-sm-1"></div>
        <div class="col-sm-7">
          <div class="input-group">
            <input type="text" class="form-control" name="username" id="username" onchange="validate.username()" placeholder="Must be at least 3 characters">
            <span class="input-group-addon"><i class="fa fa-user"></i></span>
          </div>
          <div id="username-error" class="validate-error"></div>
        </div>
      </div>
      <div class="form-group">
        <label for="password" class="col-sm-4 control-label">Password</label>
        <div class="col-sm-1"></div>
        <div class="col-sm-7">
          <div class="input-group">
            <input type="password" class="form-control" name="password" id="password" onchange="validate.password()" placeholder="Must be at least 5 characters">
            <span class="input-group-addon">...</span>
          </div>
          <div id="password-error" class="validate-error"></div>
        </div>
      </div>
      <div class="form-group">
        <label for="password2" class="col-sm-4 control-label">Reenter Password</label>
        <div class="col-sm-1"></div>
        <div class="col-sm-7">
          <div class="input-group">
            <input type="password" class="form-control" name="password2" id="password2" onchange="validate.password2()" placeholder="Repeat password">
            <span class="input-group-addon">...</span>
          </div>
          <div id="password2-error" class="validate-error"></div>
        </div>
      </div>
      <div class="form-group">
        <label for="email" class="col-sm-4 control-label">Email</label>
        <div class="col-sm-1"></div>
        <div class="col-sm-7">
          <div class="input-group">
            <input type="email" class="form-control" name="email" id="email" onchange="validate.email()" placeholder="Enter a valid email">
            <span class="input-group-addon">@</span>
          </div>
          <div id="email-error" class="validate-error"></div>
        </div>
      </div>
      <div class="error">
        <span class="error_message" id="error_message">
          <?php echo $display_message; ?>
        </span>
      </div>
      <script type="text/javascript">
        var RecaptchaOptions = {
          theme : 'custom',
          custom_theme_widget: 'recaptcha_widget'
        };
      </script>
      <div id="recaptcha_widget" class="form-group">
        <label class="recaptcha_only_if_image col-sm-4 control-label" for="recaptcha_response_field">Enter the words in the image:</label>
        <label class="recaptcha_only_if_audio col-sm-4 control-label" for="recaptcha_response_field">Enter the numbers you hear:</label>
        <div class="col-sm-1"></div>
        <div class="recaptcha_widget col-sm-7 pull-right" style="max-width: 310px; margin-right: 15px;">
          <div id="recaptcha_image"></div>
          <div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect. Please try again.</div>
          <div class="recaptcha_input">
            <input type="text" id="recaptcha_response_field" name="recaptcha_response_field">
          </div>
          <ul class="recaptcha_options">
            <li>
              <a href="javascript:Recaptcha.reload()">
                <i class="fa fa-refresh"></i>
                <span class="captcha_hide">Get another CAPTCHA</span>
              </a>
            </li>
            <li class="recaptcha_only_if_image">
              <a href="javascript:Recaptcha.switch_type('audio')">
                <i class="fa fa-volume-up"></i><span class="captcha_hide"> Get an audio CAPTCHA</span>
              </a>
            </li>
            <li class="recaptcha_only_if_audio">
              <a href="javascript:Recaptcha.switch_type('image')">
                <i class="fa fa-picture-o"></i><span class="captcha_hide"> Get an image CAPTCHA</span>
              </a>
            </li>
            <li>
              <a href="javascript:Recaptcha.showhelp()">
                <i class="fa fa-question"></i><span class="captcha_hide"> Help</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
      <script type="text/javascript" src="//www.google.com/recaptcha/api/challenge?k=6LePfucSAAAAAKlUO3GQKgfXCd7SvIhtFjBH5F9Z"></script>
      <noscript>
        <iframe src="//www.google.com/recaptcha/api/noscript?k=6LePfucSAAAAAKlUO3GQKgfXCd7SvIhtFjBH5F9Z" height="300" width="500" frameborder="0"></iframe><br>
        <textarea name="recaptcha_challenge_field"></textarea>
        <input type="hidden" name="recaptcha_response_field" value="manual_challenge">
      </noscript>
      <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
      <button type="submit" name="submit" class="btn btn-default">Register</button>
    </form>
  </div>
  <div class="col-md-3"></div>
</div>
<?php include("../lib/footer.php"); ?>
