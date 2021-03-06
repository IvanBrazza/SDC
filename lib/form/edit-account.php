<?php
  require("../common.php");

  if (!empty($_POST)) {
    if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token'])) {
      echo "Invalid token.";
      die();
    }

    // Unset token
    unset($_SESSION['token']);

    // Check if updated email is already in use
    if ($_POST['email'] != $_SESSION['user']['email']) {
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
      if ($row) {
        echo "That email address is already in use.";
        die();
      }
    }
    
    // Update password
    if (!empty($_POST['password'])) {
      $salt       = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));
      $password   = hash('sha256', $_POST['password'] . $salt);
      for ( $i = 0; $i < 65536; $i++ ) {
        $password = hash('sha256', $password . $salt);
      }
    }
    else {
      $password   = null;
      $salt       = null;
    }

    $query = "
      UPDATE 
        users
      SET
        email       = :email,
        postcode    = :postcode,
        phone       = :phone,
        address     = :address,
        first_name  = :first_name,
        last_name   = :last_name
    ";

    if ($password !== null) {
      $query .= "
        , password  = :password
        , salt      = :salt
      ";
    }
    if ($_POST['email'] != $_SESSION['user']['email']) {
      $query .= "
        , email_verified      = :email_verified
        , email_verification  = :email_verification
      ";
    }

    $query .= "
      WHERE
        customer_id = :user_id
    ";

    $query_params = array(
      ':email'        => $_POST['email'],
      ':user_id'      => $_SESSION['user']['customer_id'],
      ':postcode'     => $_POST['postcode'],
      ':phone'        => $_POST['phone'],
      ':address'      => $_POST['address'],
      ':first_name'   => $_POST['first_name'],
      ':last_name'    => $_POST['last_name']
    );

    if ($password !== null) {
      $query_params[':password'] = $password;
      $query_params[':salt']     = $salt;
    }

    if ($_POST['email'] != $_SESSION['user']['email']) {
      $query_params[':email_verification'] = mt_rand(10000,99999) . mt_rand(10000,99999) . mt_rand(10000,99999) . mt_rand(10000,99999) . mt_rand(10000,99999);
      $query_params[':email_verified']     = "no";
    }

    $db->runQuery($query, $query_params);

    // Update the _SESSION variables
    $_SESSION['user']['email']        = $_POST['email'];
    $_SESSION['user']['postcode']     = $_POST['postcode'];
    $_SESSION['user']['phone']        = $_POST['phone'];
    $_SESSION['user']['address']      = $_POST['address'];
    $_SESSION['user']['first_name']   = $_POST['first_name'];
    $_SESSION['user']['last_name']    = $_POST['last_name'];

    if (!empty($query_params[':email_verification'])) {
      include "../email.class.php";
      $email = new Email;
      $email->setFirstName($_POST['first_name']);
      $email->setRecipient($_POST['email']);
      $email->verification($query_params[':email_verification']);
      echo "email-verify";
    }
    else {
      echo "success";
    }

    die();
  }
