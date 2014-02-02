<?php
  include("../common.php");

  if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token']))
  {
    echo "Invalid token.";
    die();
  }

  $query = "
    SELECT
      *
    FROM
      orders
    WHERE
      order_number = :order_number
  ";

  $query_params = array(
    ':order_number' => $_POST['order']
  );

  $db->runQuery($query, $query_params);

  $row = $db->fetch();

  if ($row)
  {
    echo "../all-orders/?order=" . $_POST['order'];
    // Unset token
    unset($_SESSION['token']);
  }
  else
  {
    echo "Order " . $_POST['order'] . " doesn't exist!";
  }
