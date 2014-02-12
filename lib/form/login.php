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

    $db->runQuery($query, $query_params);

    $logged_in = false;

    $row = $db->fetch();
    
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
        $password_correct = true;

        // Unset token
        unset($_SESSION['token']);
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
    
    // Generate a new token
    $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

    // If the $logged_in var is true, unset the salt and password vars
    // for security reasons, then set the sessions details and redirect
    // the user to the homepage. Else, display an error message depending
    // on the combination of vars.
    if ($logged_in)
    {
      unset($row['salt']);
      unset($row['password']);

      $_SESSION['user'] = $row;

      if (!empty($_POST['redirect']))
      {
        $response = array(
          "status"   => "redirect",
          "redirect" => $_POST['redirect']
        );
      }
      else
      {
        $response = array(
          "status" => "success"
        );
      }
      echo json_encode($response);
      die();
    }
    else if (!$row)
    {
      $response = array(
        "status" => "Incorrect username.",
        "token"  => $_SESSION['token']
      );
      echo json_encode($response);
      die();
    }
    else if ($row and !$password_correct)
    {
      $response = array(
        "status" => "Incorrect password.",
        "token"  => $_SESSION['token']
      );
      echo json_encode($response);
      die();
    }
    else if (!$email_verified and !$logged_in)
    {
      $response = array(
        "status" => "Your email isn't verified, please check your emails to verify your account.",
        "token"  => $_SESSION['token']
      );
      echo json_encode($response);
      die();
    }
    else
    {
      $response = array (
        "status" => "Oops! Something went wrong. Try again.",
        "token"  => $_SESSION['token']
      );
      echo json_encode($response);
      die();
    }
  }
