<?php
  /**
    forgot-password/ - display a page to the user to email them if
    they have forgotten their password.
  **/
  require("../lib/common.php");
  include_once("../lib/email.php");
  $title = "Forgot Password";
  $email = new Email;

  // Set the error text if the page has been redirected to an error
  if (!empty($_GET['e']))
  {
    if ($_GET['e'] === "email")
    {
      $display_message = "That email isn't registered with us.";
    }
  }

  // If the form has been submitted
  if ($_POST)
  {
    // Query the DB to see if the email exists
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
    
    // If the email isn't in the DB, $row will be empty, therefore
    // redirect to an error.
    // ELSE, generate a new password and email it to the user.
    if (!$row)
    {
      header("../forgot-password/?e=email");
      die();
    }
    else
    {
      $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); // Generate a new salt
      $plainpassword = uniqid(); // Generate a new password using PHP's uniqid() function
      $password = hash('sha256', $plainpassword . $salt); // Hash the new password with the new salt
      // Hash the password another 65536 times
      for ($i = 0; $i < 65536; $i++)
      {
        $password = hash('sha256', $password . $salt);
      }

      // Store the new password and salt in the DB
      $query = "
        UPDATE
          users
        SET
          password = :password,
          salt     = :salt
        WHERE
          email    = :email
      ";

      $query_params = array(
        ':password' => $password,
        ':salt'     => $salt,
        ':email'    => $row['email']
      );

      $db->runQuery($query, $query_params);

      // Email the new password to the user
      $email->password($plainpassword);
      $email->setFirstName($row['first_name']);
      $email->setRecipient($row['email']);
      $email->send();
    }
  }
?>
<?php include("../lib/header.php"); ?>
  <?php if ($_POST) : ?>
    <p>We've sent you a new password to your email address <?php echo $_POST['email']; ?>.</p>
  <?php else : ?>
    <div class="form">
      <h1>Forgot Password</h1>
      <p>Enter the email you registered with below and we'll send you an email containing your new password.</p>
      <form action="index.php" method="POST">
        <div>
          <label for="email">EMail</label>
          <input type="text" name="email" id="email" onchange="validateEmail()">
        </div>
        <div id="email-error" class="validate-error"></div>
        <div class="error">
          <span class="error_message">
            <?php echo $display_message; ?>
          </span>
        </div>
        <input type="submit" value="Reset Password" name="submit">
      </form>
    </div>
  <?php endif; ?>
<?php include("../lib/footer.php"); ?>
