<?php
  /**
    lib/archive-order.php - a library to archive an order
  **/
  require("common.php");

  if (!empty($_POST))
  {
    $query = "
      UPDATE
        orders
      SET
        archived = 1,
        status = 'Complete'
      WHERE
        order_number = :order_number
    ";

    $query_params = array(
      ':order_number' => $_POST['order_number']
    );

    $db->runQuery($query, $query_params);

    header("Location: ../all-orders/?archive=success");
    die();
  }
  else
  {
    die("Error arhiving order");
  }
?>
