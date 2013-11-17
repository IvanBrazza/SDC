<?php
  include("../common.php");

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

  try
  {
    $stmt     = $db->prepare($query);
    $result   = $stmt->execute($query_params);
  }
  catch(PDOException $ex)
  {
    die("Failed to execute query: " . $ex->getMessage() . " query: " . $query);
  }

  $row = $stmt->fetch();

  if ($row)
  {
    echo "../all-orders/?order=" . $_POST['order'];
  }
  else
  {
    echo "nope";
  }
