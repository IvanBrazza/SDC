<?php
  require("../lib/common.php");
  $title = "Register";

  if(!empty($_GET['e']))
  {
    if ($_GET['e'] === "captcha")
    {
      $display_message = "Incorrect captcha.";
    }

    if ($_GET['e'] === "username")
    {
      $display_message = "Username already taken.";
    }

    if ($_GET['e'] === "email")
    {
      $display_message = "Email already in use.";
    }
  }

  if(!empty($_POST))
  {
    // Check reCAPTCHA
    require_once('../lib/recaptcha.php');
    $privatekey = "6LePfucSAAAAAHkrfHOrSYPPvJqf6rCiNnhWT77L";
    $resp = recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

    if (!$resp->is_valid) 
    {
      // What happens when the CAPTCHA was entered incorrectly
      header("Location: ../register/?e=captcha");
      die();
    }
    else
    {
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
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
      }
      catch(PDOException $ex)
      {
        die("Failed to run query: " . $ex->getMessage());
      }
    
      $row = $stmt->fetch();
  
      if ($row)
      {
        header("Location: ../register/?e=username");
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
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
      }
      catch(PDOException $ex)
      {
        die("Failed to run query to check email: " . $ex->getMessage());
      }
  
      $row = $stmt->fetch();
    
      if ($row)
      {
        header("Location: ../register/?e=email");
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
        ':username' => $_POST['username'],
        ':password' => $password,
        ':salt' => $salt,
        ':email' => $_POST['email'],
        ':email_verification' => $email_verification,
        ':email_verified' => 'no'
      );
    
      try
      {
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
      }
      catch(PDOException $ex)
      {
        die("Failed to run query to register: " . $ex->getMessage());
      }
      
      require("../lib/email-verification.php");
  
      header("Location: ../verify-email");
     die();
    }
  }
?>
<?php include("../lib/header.php"); ?>
  <div class="container">
  <div class="form">
    <h1>Register</h1>
    <form action="index.php" method="post" data-validate="parsley">
      <div>
        <label for="username">Username</label>
        <div class="parsley-container">
          <input type="text" name="username" id="username" data-required="true" data-trigger="change" data-error-message="Please enter a username" />
        </div>
      </div>
      <div>
        <label for="password">Password</label>
        <div class="parsley-container">
          <input type="password" name="password" id="password" data-required="true" data-trigger="change" data-error-message="Please enter a password" />
        </div>
      </div>
      <div>
        <label for="password2">Reenter Password</label>
        <div class="parsley-container">
          <input type="password" name="password2" id="password2" data-trigger="change" data-required="true" data-error-message="Please reenter your password" data-equalto="#password" data-trigger="change" />
        </div>
      </div>
      <div>
        <label for="email">EMail</label>
        <div class="parsley-container">
          <input type="text" name="email" id="email" data-trigger="change" data-required="true" data-type="email" data-error-message="Please enter a valid email address" />
        </div>
      </div>
      <div class="error">
        <span class="error_message">
          <?php echo $display_message; ?>
        </span>
      </div>
      <?php
        require_once("../lib/recaptcha.php");
        $publickey = "6LePfucSAAAAAKlUO3GQKgfXCd7SvIhtFjBH5F9Z";
        echo recaptcha_get_html($publickey, $error = null, $use_ssl = true);
      ?>
      <input type="submit" value="Register" />
    </form>
  </div>
  </div>
<?php include("../lib/footer.php"); ?>
