<?php
  require("../common.php");

  // If the login form has been submitted
  if (!empty($_POST))
  {
    if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token']))
    {
      $response = array(
        "status" => "error",
        "error"  => "Invalid token (try refreshing the page)",
        "code"   => "001"
      );

      echo json_encode($response);
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

    $db->runQuery($query, $query_params);

    $logged_in = false;

    $row = $db->fetch();
    
    // If $row is empty, it's because the user doesn't exist in
    // the DB. If it's not, then let's check the password to see
    // if it matches the one stored in the DB.
    if ($row)
    {
      $check_password = hash('sha256', $_POST['password'] . $row['email']);
      for ($i = 0; $i < 65536; $i++)
      {
        $check_password = hash('sha256', $check_password . $row['email']);
      }
      if ($check_password === $row['password'])
      {
        $logged_in = true;
        $password_correct = true;
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

    // If the $logged_in var is true, unset the password var
    // for security reasons, then set the sessions details and redirect
    // the user to the homepage. Else, display an error message depending
    // on the combination of vars.
    if ($logged_in)
    {
      unset($row['password']);

      $_SESSION['user'] = $row;

      if (!empty($_POST['redirect']))
      {
        $response = array(
          "status"   => "redirect",
          "redirect" => $_POST['redirect']
        );
      }
      else if ($row['last_login'] == "0000-00-00 00:00:00")
      {
        $response = array(
          "status"   => "redirect",
          "redirect" => "/edit-account/?first=login"
        );
      }
      else
      {
        $response = array(
          "status" => "success"
        );
      }

      $query = "
        UPDATE
          users
        SET
          last_login = :date
        WHERE
          customer_id = :customer_id
      ";

      $query_params = array(
        ':date'         => date('Y-m-d H:i:s'),
        ':customer_id'  => $row['customer_id']
      );

      $db->runQuery($query, $query_params);
      echo json_encode($response);
      die();
    }
    else if (!$row)
    {
      $response = array(
        "status" => "error",
        "error"  => "Username <b>" . $_POST['username'] . "</b> not found",
        "code"   => "002",
        "token"  => $_SESSION['token']
      );
      echo json_encode($response);
      die();
    }
    else if ($row and !$password_correct)
    {
      $response = array(
        "status" => "error",
        "error"  => "Incorrect password",
        "code"   => "003",
        "token"  => $_SESSION['token']
      );
      echo json_encode($response);
      die();
    }
    else if (!$email_verified and !$logged_in)
    {
      $response = array(
        "status" => "error",
        "error"  => "Your email isn't verified, please check your emails to verify your account",
        "token"  => $_SESSION['token']
      );
      echo json_encode($response);
      die();
    }
    else
    {
      $response = array (
        "status" => "error",
        "error"  => "Oops! Something went wrong. Try again.",
        "token"  => $_SESSION['token']
      );
      echo json_encode($response);
      die();
    }
  }
