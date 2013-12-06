<?php
  require("../common.php");
  require_once("../recaptchalib.php");
  require_once("../email.php");
  
  $privatekey = "6LePfucSAAAAAHkrfHOrSYPPvJqf6rCiNnhWT77L";
  $resp = recaptcha_check_answer($privatekey, $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);
  $email = new Email;

  if(!empty($_POST))
  {
    // Check reCAPTCHA
    if (!$resp->is_valid)
    {
      echo "reCAPTCHA incorrect.";
      die();
    }
    else if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token']))
    {
      echo "Invalid token.";
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
      
      try
      {
        $stmt     = $db->prepare($query);
        $result   = $stmt->execute($query_params);
      }
      catch(PDOException $ex)
      {
        echo "Oops! Something went wrong. Try again.";
        die("Failed to run query: " . $ex->getMessage());
      }
      
      $row = $stmt->fetch();
    
      if ($row)
      {
        echo "Username already taken.";
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
      
      try
      {
        $stmt     = $db->prepare($query);
        $result   = $stmt->execute($query_params);
      }
      catch(PDOException $ex)
      {
        echo "Oops! Something went wrong. Try again.";
        die("Failed to run query to check email: " . $ex->getMessage());
      }
    
      $row = $stmt->fetch();
     
      if ($row)
      {
        echo "Email already in use.";
        die();
      }
    
      $query = "
        INSERT INTO users (
          username,
          password,
          salt,
          email,
          email_verification,
          email_verified
        ) VALUES (
          :username,
          :password,
          :salt,
          :email,
          :email_verification,
          :email_verified
        )
      ";
      
      // Generate a salt to protect against brute force attacks and
      // rainbow table attacks. A hex representation of an 8 byte salt
      // is produced. Hex is used for human readability.
      $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
    
      // Generate a code for email verification.
      $email_verification = mt_rand(10000,99999) . mt_rand(10000,99999) . mt_rand(10000,99999) . mt_rand(10000,99999) . mt_rand(10000,99999);
        
      // Hash the password with the salt. The plaintext password is not stored
      // in the database, but rather the hashed version of it. The salt is
      // added to the password when hashed so the original password cannot
      // be recovered from the hash.
      $password = hash('sha256', $_POST['password'] . $salt);
      
      // Hash the password another 65536 more times. This is to prevent
      // against brute force attacks.
      for ($i = 0; $i < 65536; $i++)
      {
        $password = hash('sha256', $password . $salt);
      }
    
      $query_params = array(
        ':username'             => $_POST['username'],
        ':password'             => $password,
        ':salt'                 => $salt,
        ':email'                => $_POST['email'],
        ':email_verification'   => $email_verification,
        ':email_verified'       => 'no'
      );
      
      try
      {
        $stmt     = $db->prepare($query);
        $result   = $stmt->execute($query_params);
      }
      catch(PDOException $ex)
      {
        echo "Oops! Something went wrong. Try again.";
        die("Failed to run query to register: " . $ex->getMessage());
      }
      
      $email->verification($email_verification);
      $email->setFirstName($_POST['first_name']);
      $email->setRecipient($_POST['email']);
      $email->send();

      echo "registered";
      die();
    }
  }

