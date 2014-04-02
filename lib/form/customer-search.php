<?php
  include("../common.php");

  if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token']))
  {
    echo "Invalid token.";
    die();
  }

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
    echo "../all-orders/?id=" . $row['customer_id'];
    // Unset token
    unset($_SESSION['token']);
  }
  else
  {
    echo "Customer " . $_POST['customer_name'] . " doesn't exist!";
  }
