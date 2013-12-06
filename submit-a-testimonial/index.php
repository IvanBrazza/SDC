<?php
  /**
   submit-a-testimonial/ - display a form to the user to
   submit a testimonial.
  **/
  require("../lib/common.php");
  $title = "Submit A Testimonial";
  $page = "testimonials";

  // Generate a token
  $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");
?>
<?php include("../lib/header.php"); ?>
  <div class="form">
    <h1>Submit A Testimonial</h1>
    <script type="text/javascript">
      var RecaptchaOptions = {
        theme : 'clean'
      };
    </script>
    <form action="index.php" method="POST" id="testimonial-form">
      <div>
        <label for="name">Name</label>
        <input type="text" name="name" id="name" onkeyup="validateInput('#name', '#name_error')" onchange="validateInput('#name', '#name_error')">
      </div>
      <div id="name_error" class="validate-error"></div>
      <div>
        <label for="email">Email</label>
        <input type="text" name="email" id="email" onkeyup="validateEmail()" onchange="validateEmail()">
      </div>
      <div id="email-error" class="validate-error"></div>
      <div>
        <label for="location">Location</label>
        <input type="text" name="location" id="location">
      </div>
      <div>
        <label for="testimonial">Testimonial</label>
        <textarea name="testimonial" id="testimonial" rows="6" cols="40" onkeyup="validateInput('textarea#testimonial', '#testimonial_error')" onchange="validateInput('textarea#testimonial', '#testimonial_error')"></textarea>
      </div>
      <div id="testimonial_error" class="validate-error"></div>
      <div class="error">
        <span class="error_message">
          <?php echo $display_message; ?>
        </span>
      </div>
      <?php
        require_once("../lib/recaptchalib.php");
        $publickey = "6LePfucSAAAAAKlUO3GQKgfXCd7SvIhtFjBH5F9Z";
        echo recaptcha_get_html($publickey, null, true);
      ?>
      <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
      <input type="submit" id="submit-testimonial" value="Submit Testimonial" name="submit">
      <span class="ajax-load"></span>
    </form>
  </div>
<?php include("../lib/footer.php"); ?>
