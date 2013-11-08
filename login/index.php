<?php
  /**
    login/ - display a login form to the user so they can
    log into the site.
  **/
  require("../lib/common.php");
  $title                = "Log In";
  $page                 = "login";
  $submitted_username   = "";
  $display_message      = "";

  // If the user is already logged in, redirect them to the
  // homepage
  if (!empty($_SESSION))
  {
    header("Location: ../home/");
    die();
  }

  // Set the error text if the page has been redirected due
  // to an error
  if(!empty($_GET['e']))
  {
    if ($_GET['e'] === 'pao')
    {
      $display_message = "Please login/register to place an order";
    }
  }

  // If the login form has been submitted
  if (!empty($_POST))
  {
    // Let's pull up the user's details from the username provided
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

    $logged_in = false;

    $row = $stmt->fetch();
    
    // If $row is empty, it's because the user doesn't exist in
    // the DB. If it's not, then let's check the password to see
    // if it matches the one stored in the DB.
    if ($row)
    {
      $check_password = hash('sha256', $_POST['password'] . $row['salt']);
      for ($i = 0; $i < 65536; $i++)
      {
        $check_password = hash('sha256', $check_password . $row['salt']);
      }
      if ($check_password === $row['password'])
      {
        $logged_in = true;
      }
      else
      {
        $password_correct = false;
      }
      if ($row['email_verified'] !== "yes")
      {
        $logged_in        = false;
        $email_verified   = false;
      }
    }
    
    // If the $logged_in var is true, unset the salt and password vars
    // for security reasons, then set the sessions details and redirect
    // the user to the homepage. Else, display an error message depending
    // on the combination of vars.
    if ($logged_in)
    {
      unset($row['salt']);
      unset($row['password']);

      $_SESSION['user'] = $row;

      header("Location: ../home");
      die();
    }
    else if (!$email_verified and !$logged_in)
    {
      $display_message = "Your email isn't verified, please check your emails to verify your account.";
    }
    else if ($row and !$password_correct)
    {
      $display_message = "Incorrect password.";
    }
    else if (!$row)
    {
      $display_message = "Incorrect username.";
    }
    else
    {
      die("Login failed");
      $submitted_username = htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8');
    }
  }
?>
<?php include("../lib/header.php"); ?>
  <div class="error">
    <span class="error_message">
      <?php echo $display_message; ?>
    </span>
  </div>
  <div class="form">
    <h1>Login</h1> 
    <form action="index.php" method="post" id="login-form"> 
      <div>
        <label for="username">Username</label>
        <input type="text" name="username" id="username" value="<?php echo $submitted_username; ?>" onkeyup="validateUsername()" onchange="validateUsername()"/>
      </div>
      <div id="username-error" class="validate-error"></div>
      <div>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" onkeyup="validatePassword()" onchange="validatePassword()" />
      </div>
      <div id="password-error" class="validate-error"></div>
      <a href="../forgot-password" class="forgot-password">Forgot Password</a>
      <br /><br /> 
      <input type="submit" value="Login" /> 
    </form> 
  </div>
<?php include("../lib/footer.php");
