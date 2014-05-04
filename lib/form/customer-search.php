<?php
  include("../common.php");

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

  $names = explode(" ", $_POST['customer_name']);
  $first_name = $names[0];
  $last_name = $names[1];

  $query = "
    SELECT
      customer_id
    FROM
      users
    WHERE
      first_name = :first_name
    AND
      last_name = :last_name
  ";

  $query_params = array(
    ':first_name' => $first_name,
    ':last_name'  => $last_name
  );

  $db->runQuery($query, $query_params);

  $row = $db->fetch();

  if ($row)
  {
    $response = array(
      'status' => 'success',
      'redirect' => "../all-orders/?id=" . $row['customer_id']
    );
  }
  else
  {
    // Generate new token
    $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

    $response = array(
      'status' => 'error',
      'error'  => "Customer <b>" . $_POST['customer_name'] . "</b> doesn't exist!",
      'code'   => '002',
      'token'  => $_SESSION['token']
    );
  }

  echo json_encode($response);
  die();
