<?php
  require("../common.php");
  require_once("../recaptchalib.php");

  $privatekey = "6LePfucSAAAAAHkrfHOrSYPPvJqf6rCiNnhWT77L";
  $resp = recaptcha_check_answer($privatekey, $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);

  if (!empty($_POST)) {
    if (!$resp->is_valid) {
      echo "reCAPTCHA incorrect.";
      die();
    }
    else if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token'])) {
      echo "Invalid token.";
      die();
    }
    else {
      // Unset the token
      unset($_SESSION['token']);

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

      $db->runQuery($query, $query_params);

      $testimonialDetails = array(
        'name'          => $_POST['name'],
        'testimonial'   => $_POST['testimonial']
      );

      include("../email.class.php");
      $email = new Email;
      $email->setRecipient("dudeman1996@gmail.com");
      $email->testimonial($testimonialDetails);
      $email->send();

      // Generate new token
      $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

      echo "success " . $_SESSION['token'];
    }
  }
