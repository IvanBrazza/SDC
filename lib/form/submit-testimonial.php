<?php
  require("../common.php");
  require_once("../recaptchalib.php");

  $privatekey = "6LePfucSAAAAAHkrfHOrSYPPvJqf6rCiNnhWT77L";
  $resp = recaptcha_check_answer($privatekey, $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);

  if (!empty($_POST))
  {
    if (!$resp->is_valid)
    {
      echo "reCAPTCHA incorrect.";
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
        echo "Oops! Something went wrong. Try again.";
        die("Failed to execute query: " . $ex->getMessage());
      }

      echo "testimonial-submitted";
    }
  }
