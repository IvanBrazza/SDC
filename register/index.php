<?php
  /**
   register/ - display a form to the user so they can
   register to use the site.
  **/
  require("../lib/common.php");
  require_once("../lib/ayah/ayah.php");
  $ayah   = new AYAH();
  $title  = "Register";
  $page   = "register";

  if(!empty($_GET['e']))
  {
    if ($_GET['e'] === "ayah")
    {
      $display_message = "Human verification failed.";
    }
    else if ($_GET['e'] === "username")
    {
      $display_message = "Username already taken.";
    }
    else if ($_GET['e'] === "email")
    {
      $display_message = "Email already in use.";
    }
  }

  if(!empty($_POST))
  {
    // Check AYAH
    if (array_key_exists("submit", $_POST))
    {
      if ($ayah->scoreResult())
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
          $stmt     = $db->prepare($query);
          $result   = $stmt->execute($query_params);
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
          $stmt     = $db->prepare($query);
          $result   = $stmt->execute($query_params);
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
          die("Failed to run query to register: " . $ex->getMessage());
        }
      
        require("../lib/email-verification.php");
    
        header("Location: ../verify-email");
        die();
      }
      else
      {
        header("Location: ../register/?e=ayah");
        die();
      }
    }
  }
?>
<?php include("../lib/header.php"); ?>
  <div class="form">
    <h1>Register</h1>
    <form action="index.php" method="post" id="register-form">
      <div>
        <label for="username">Username</label>
        <input type="text" name="username" id="username" onkeyup="validateUsername()" onchange="validateUsername()">
      </div>
      <div id="username-error" class="validate-error"></div>
      <div>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" onkeyup="validatePassword()" onchange="validatePassword()">
      </div>
      <div id="password-error" class="validate-error"></div>
      <div>
        <label for="password2">Reenter Password</label>
        <input type="password" name="password2" id="password2" onkeyup="validatePassword2()" onchange="validatePassword2()">
      </div>
      <div id="password2-error" class="validate-error"></div>
      <div>
        <label for="email">EMail</label>
        <input type="text" name="email" id="email" onkeyup="validateEmail()" onchange="validateEmail()">
      </div>
      <div id="email-error" class="validate-error"></div>
      <div class="error">
        <span class="error_message">
          <?php echo $display_message; ?>
        </span>
      </div>
      <?php
        echo $ayah->getPublisherHTML();
      ?>
      <input type="submit" value="Register" name="submit" />
    </form>
  </div>
<?php include("../lib/footer.php"); ?>
