<?php
  require("../common.php");
  $title = "Log In";
  
  $submitted_username = "";
  $display_message = "";

  if(!empty($_GET['e']))
  {
    if ($_GET['e'] === 'pao')
    {
      $display_message = "Please login/register to place an order";
    }
  }

  if (!empty($_POST))
  {
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

    $logged_in = false;

    $row = $stmt->fetch();
    
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
        $logged_in = false;
        $email_verified = false;
      }
    }

    if ($logged_in)
    {
      unset($row['salt']);
      unset($row['password']);

      $_SESSION['user'] = $row;

      header("Location: ../private");
      die();
    }
    else if (!$email_verified and $logged_in)
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
<?php include("../header.php"); ?>
  <div class="container">
    <div class="error">
      <span class="error_message">
        <?php echo $display_message; ?>
      </span>
    </div>
    <div class="form">
      <h1>Login</h1> 
      <form action="index.php" method="post" data-validate="parsley"> 
        <div>
          <label for="username">Username</label>
          <div class="parsley-container">
            <input type="text" name="username" id="username" value="<?php echo $submitted_username; ?>" data-trigger="change" data-required="true" data-error-message="Please enter your username" />
          </div>
        </div>
        <div>
          <label for="password">Password</label>
          <div class="parsley-container">
            <input type="password" name="password" id="password" data-required="true" data-trigger="change" data-error-message="Please enter your password" />
          </div>
        </div>
        <br /><br /> 
        <input type="submit" value="Login" /> 
      </form> 
    </div>
  </div>
<?php include("../footer.php");
