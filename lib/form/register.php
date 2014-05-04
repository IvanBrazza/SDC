<?php
  require("../common.php");
  require_once("../recaptchalib.php");
  require_once("../email.class.php");
  
  $privatekey = "6LePfucSAAAAAHkrfHOrSYPPvJqf6rCiNnhWT77L";
  $resp = recaptcha_check_answer($privatekey, $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);
  $email = new Email;

  if(!empty($_POST))
  {
    // Check reCAPTCHA
    if (!$resp->is_valid)
    {
      // Generate new token
      $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

      $response = array(
        "status" => "error",
        "error"  => "Incorrect reCAPTCHA - please try again",
        "code"   => "002",
        "token"  => $_SESSION['token']
      );

      echo json_encode($response);
      die();
    }
    else if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token']))
    {
      $response = array(
        "status" => "error",
        "error"  => "Invalid token (try refreshing the page)",
        "code"   => "001"
      );

      echo json_encode($response);
      die();
    }
    else
    {
      // Clear the token
      unset($_SESSION['token']);
      // Check if username is taken
      $query = "
        SELECT
          * 
        FROM 
          users 
        WHERE 
          username = :username
      ";
    
      $query_params = array(
        ':username' => $_POST['username']
      );

      $db->runQuery($query, $query_params);

      $row = $db->fetch();
    
      if ($row)
      {
        // Generate new token
        $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

        $response = array(
          'status' => 'error',
          'error'  => 'That username is already in use - try a different one',
          'code'   => '003',
          'token'  => $_SESSION['token']
        );

        echo json_encode($response);
        die();
      }
        
      // Check if email is already in use
      $query = "
        SELECT
          *
        FROM
          users
        WHERE
          email = :email
      ";
      
      $query_params = array(
        ':email' => $_POST['email']
      );

      $db->runQuery($query, $query_params);

      $row = $db->fetch();
     
      if ($row)
      {
        // Generate new token
        $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

        $response = array(
          'status' => 'error',
          'error'  => 'That email address is already in use',
          'code'   => '004',
          'token'  => $_SESSION['token']
        );

        echo json_encode($response);
        die();
      }
    
      $query = "
        INSERT INTO users (
          username,
          password,
          email,
          email_verification,
          email_verified
        ) VALUES (
          :username,
          :password,
          :email,
          :email_verification,
          :email_verified
        )
      ";
    
      // Generate a code for email verification.
      $email_verification = mt_rand(10000,99999) . mt_rand(10000,99999) . mt_rand(10000,99999) . mt_rand(10000,99999) . mt_rand(10000,99999);
        
      // Hash the password. The plaintext password is not stored
      // in the database, but rather the hashed version of it.
      $password = hash('sha256', $_POST['password'] . $row['email']);
      
      // Hash the password another 65536 more times. This is to prevent
      // against brute force attacks.
      for ($i = 0; $i < 65536; $i++)
      {
        $password = hash('sha256', $password . $row['email']);
      }
    
      $query_params = array(
        ':username'             => $_POST['username'],
        ':password'             => $password,
        ':email'                => $_POST['email'],
        ':email_verification'   => $email_verification,
        ':email_verified'       => 'no'
      );

      $db->runQuery($query, $query_params);

      $email->setFirstName($_POST['username']);
      $email->setRecipient($_POST['email']);
      $email->verification($email_verification);
      $email->send();

      // Generate new token
      $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

      $response = array(
        'status' => 'success',
        'token'  => $_SESSION['token']
      );

      echo json_encode($response);
      die();
    }
  }

