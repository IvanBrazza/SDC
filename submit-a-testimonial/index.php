<?php
  require("../common.php");
  $title = "Submit A Testimonial";
  
  if(!empty($_GET['e']))
  {
    if ($_GET['e'] === "captcha")
    {
      $display_message = "Incorrect captcha.";
    }
  }

  if (!empty($_POST))
  {
    // Check reCAPTCHA
    require_once('../recaptchalib.php');
    $privatekey = "6LePfucSAAAAAHkrfHOrSYPPvJqf6rCiNnhWT77L";
    $resp = recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

    if (!$resp->is_valid) 
    {
      // What happens when the CAPTCHA was entered incorrectly
      header("Location: ../submit-a-testimonial/?e=captcha");
      die();
    }
    else
    {
      $query = "
        INSERT INTO testimonials (
          name,
          email,
          location,
          testimonial
        ) VALUES (
          :name,
          :email,
          :location,
          :testimonial
        )
      ";

      $query_params = array(
        ':name' => $_POST['name'],
        ':email' => $_POST['email'],
        ':location' => $_POST['location'],
        ':testimonial' => $_POST['testimonial']
      );
  
      try
      {
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
      }
      catch(PDOException $ex)
      { 
        die("Failed to execute query: " . $ex->getMessage());
      }

      header("Location: ../testimonials/");
      die();
    }
  }
?>
<?php include("../header.php"); ?>
  <div class="form">
    <h1>Submit A Testimonial</h1>
    <form action="index.php" method="POST" data-validate="parsley">
      <div>
        <label for="name">Name</label>
        <div class="parsley-container">
          <input type="text" name="name" id="name" data-required="true" data-trigger="change" data-error="Please enter your name">
        </div>
      </div>
      <div>
        <label for="email">Email</label>
        <div class="parsley-container">
          <input type="text" name="email" id="email" data-type="email" data-trigger="change" data-required="true" data-error="Please enter your email">
        </div>
      </div>
      <div>
        <label for="location">Location</label>
        <div class="parsley-container">
          <input type="text" name="location" id="location">
        </div>
      </div>
      <div>
        <label for="testimonial">Testimonial</label>
        <div class="parsley-container">
          <textarea name="testimonial" id="testimonial" rows="6" cols="40" data-trigger="change" data-required="true" data-error="Please enter your testimonial"></textarea>
        </div>
      </div>
      <div class="error">
        <span class="error_message">
          <?php echo $display_message; ?>
        </span>
      </div>
      <?php
        require_once("../recaptchalib.php");
        $publickey = "6LePfucSAAAAAKlUO3GQKgfXCd7SvIhtFjBH5F9Z";
        echo recaptcha_get_html($publickey, $error = null, $use_ssl = false);
      ?>
      <input type="submit" id="submit-testimonial" value="Submit Testimonial">
    </form>
  </div>
<?php include("../footer.php"); ?>
