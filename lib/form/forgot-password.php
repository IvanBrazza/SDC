<?php
  /**
    lib/form/forgot-password.php - called by forgot-password
    via AJAX to reset a users password.
  **/
  require("../common.php");
  include_once("../email.class.php");
  $email = new Email;

  // If the form has been submitted
  if ($_POST) {
    if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token'])) {
      $response = array(
        'status' => "Invalid token.",
        'token'  => $_SESSION['token']
      );
      echo json_encode($response);
      die();
    }
    
    $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");#

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
    if (!$row) {
      $response = array(
        'status' => 'Email doesn\'t exist.',
        'token'  => $_SESSION['token']
      );
      echo json_encode($response);
      die();
    }
    else {
      $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); // Generate a new salt
      $plainpassword = uniqid(); // Generate a new password using PHP's uniqid() function
      $password = hash('sha256', $plainpassword . $salt); // Hash the new password with the new salt
      // Hash the password another 65536 times
      for ($i = 0; $i < 65536; $i++) {
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

      $response = array(
        'status' => 'success'
      );

      echo json_encode($response);
      die();
    }
  }
