<?php
  require("../common.php");

  if (!empty($_POST))
  {
    if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token']))
    {
      $response = array(
        "status" => "error",
        "error"  => "Invalid token (try refreshing the page)"
      );

      echo json_encode($response);
      die();
    }

    // Unset token
    unset($_SESSION['token']);

    // Get user details
    $query = "
      SELECT
        *
      FROM
        users
      WHERE
        customer_id = :customer_id
    ";
    $query_params = array(
      ':customer_id' => $_POST['id']
    );
    $db->runQuery($query, $query_params);
    $row = $db->fetch();

    // What are we updating?
    if ($_POST['type'] == "personal")
    {
      $query = "
        UPDATE 
          users
        SET
          first_name  = :first_name,
          last_name   = :last_name
        WHERE
          customer_id = :user_id
      ";

      $query_params = array(
        ':user_id'      => $_SESSION['user']['customer_id'],
        ':first_name'   => $_POST['first_name'],
        ':last_name'    => $_POST['last_name']
      );

      $db->runQuery($query, $query_params);
      $_SESSION['user']['first_name'] = $_POST['first_name'];
      $_SESSION['user']['first_name'] = $_POST['first_name'];

      // Generate new token
      $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

      $response = array(
        'status' => 'success',
        'token'  => $_SESSION['token']
      );
    }
    else if ($_POST['type'] == "email")
    {
      // Check if updated email is already in use
      if ($_POST['email'] != $_SESSION['user']['email'])
      {
        if ($row)
        {
          // Generate new token
          $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

          $response = array(
            'status' => 'error',
            'error'  => 'That email address is already in use',
            'token'  => $_SESSION['token']
          );

          echo json_encode($response);
          die();
        }
        else
        {
          $query = "
            UPDATE 
              users
            SET
              email               = :email,
              email_verified      = :email_verified,
              email_verification  = :email_verification
            WHERE
              customer_id = :user_id
          ";

          $query_params = array(
            ':user_id'            => $_SESSION['user']['customer_id'],
            ':email'              => $_POST['email'],
            ':email_verified'     => "no",
            ':email_verification' => mt_rand(10000,99999) . mt_rand(10000,99999) . mt_rand(10000,99999) . mt_rand(10000,99999) . mt_rand(10000,99999)
          );

          $db->runQuery($query, $query_params);
          $_SESSION['user']['email'] = $_POST['email'];
          include "../email.class.php";
          $email = new Email;
          $email->setFirstName($_POST['first_name']);
          $email->setRecipient($_POST['email']);
          $email->verification($query_params[':email_verification']);
          $response = array(
            'status' => 'verify-email'
          );
        }
      }
      else
      {
        // Generate new token
        $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

        $response = array(
          'status' => 'error',
          'error'  => 'Please enter a different email address',
          'token'  => $_SESSION['token']
        );

        echo json_encode($response);
        die();
      }
    }
    else if ($_POST['type'] == "password")
    {
      // Update password
      $password   = hash('sha256', $_POST['password'] . $row['email']);
      for ( $i = 0; $i < 65536; $i++ )
      {
        $password = hash('sha256', $password . $row['email']);
      }
      $query = "
        UPDATE 
          users
        SET
          password = :password
        WHERE
          customer_id = :user_id
      ";

      $query_params = array(
        ':user_id'  => $_SESSION['user']['customer_id'],
        ':password' => $password
      );

      $db->runQuery($query, $query_params);

      // Generate new token
      $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

      $response = array(
        'status' => 'success',
        'token'  => $_SESSION['token']
      );
    }
    else if ($_POST['type'] == "address")
    {
      $query = "
        UPDATE 
          users
        SET
          address  = :address,
          postcode = :postcode
        WHERE
          customer_id = :user_id
      ";

      $query_params = array(
        ':user_id'  => $_SESSION['user']['customer_id'],
        ':address'  => $_POST['address'],
        ':postcode' => $_POST['postcode']
      );

      $db->runQuery($query, $query_params);
      $_SESSION['user']['address']  = $_POST['address'];
      $_SESSION['user']['postcode'] = $_POST['postcode'];

      // Generate new token
      $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

      $response = array(
        'status' => 'success',
        'token'  => $_SESSION['token']
      );
    }
    else if ($_POST['type'] == "phone")
    {
      $query = "
        UPDATE 
          users
        SET
          phone = :phone
        WHERE
          customer_id = :user_id
      ";

      $query_params = array(
        ':user_id' => $_SESSION['user']['customer_id'],
        ':phone'   => $_POST['phone']
      );    

      $db->runQuery($query, $query_params);
      $_SESSION['user']['phone'] = $_POST['phone'];

      // Generate new token
      $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

      $response = array(
        'status' => 'success',
        'token'  => $_SESSION['token']
      );
    }       

    echo json_encode($response);
    die();
  }
