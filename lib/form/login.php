<?php
  require("../common.php");

  // If the login form has been submitted
  if (!empty($_POST))
  {
    if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token']))
    {
      echo "Invalid token.";
      die();
    }
    
    // Unset token
    unset($_SESSION['token']);

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
      echo "Oops! Something went wrong. Try again.";
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
      
      echo "logged-in";
      die();
    }
    else if (!$row)
    {
      echo "Incorrect username.";
      die();
    }
    else if ($row and !$password_correct)
    {
      echo "Incorrect password.";
      die();
    }
    else if (!$email_verified and !$logged_in)
    {
      echo "Your email isn't verified, please check your emails to verify your account.";
      die();
    }
    else
    {
      echo "Oops! Something went wrong. Try again.";
      die();
      $submitted_username = htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8');
    }
  }
