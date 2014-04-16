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
<div class="row">
  <div class="col-md-12">
    <h1>Forgot Password</h1>
    <p>Enter the email you registered with below and we'll send you an email containing your new password.</p>
  </div>
</div>
<div class="row">
  <div class="col-md-3"></div>
  <div class="col-md-6">
    <div class="alert alert-danger" id="error_message"></div>
    <div class="alert alert-success" id="success_message"></div>
    <form id="forgot-password-form" class="form-horizontal" role="form">
      <div class="form-group">
        <label for="email" class="col-sm-2 control-label">EMail</label>
        <div class="col-sm-3"></div>
        <div class="col-md-7 input-group">
          <input type="text" name="email" class="form-control" id="email" onchange="validate.email()">
          <span class="input-group-addon">@</span>
        </div>
      </div>
      <div id="email-error" class="validate-error pull-right"></div>
      <input type="hidden" value="<?php echo $_SESSION['token']; ?>" id="token" name="token">
      <button type="submit" class="btn btn-default">Reset Password</button>
    </form>
  </div>
  <div class="col-md-3"></div>
</div>
<?php include("../lib/footer.php"); ?>
