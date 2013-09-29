<?php
  /**
   submit-a-testimonial/ - display a form to the user to
   submit a testimonial.
  **/
  require("../lib/common.php");
  $title = "Submit A Testimonial";
  $page = "testimonials";
  
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
    require_once('../lib/recaptcha.php');
    $privatekey   = "6LePfucSAAAAAHkrfHOrSYPPvJqf6rCiNnhWT77L";
    $resp         = recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

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
        ':name'           => $_POST['name'],
        ':email'          => $_POST['email'],
        ':location'       => $_POST['location'],
        ':testimonial'    => $_POST['testimonial']
      );
  
      try
      {
        $stmt     = $db->prepare($query);
        $result   = $stmt->execute($query_params);
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
<?php include("../lib/header.php"); ?>
  <div class="form">
    <h1>Submit A Testimonial</h1>
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
        require_once("../lib/recaptcha.php");
        $publickey = "6LePfucSAAAAAKlUO3GQKgfXCd7SvIhtFjBH5F9Z";
        echo recaptcha_get_html($publickey, $error = null, $use_ssl = false);
      ?>
      <input type="submit" id="submit-testimonial" value="Submit Testimonial">
    </form>
  </div>
<?php include("../lib/footer.php"); ?>
