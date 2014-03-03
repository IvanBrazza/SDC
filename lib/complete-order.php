<?php
  /**
    lib/complete-order.php - mark an order as compelte
  **/
  require("common.php");

  if (!empty($_POST))
  {
    $query = "
      UPDATE
        orders
      SET
        completed = 1,
        status = 'Complete'
      WHERE
        order_number = :order_number
    ";

    $query_params = array(
      ':order_number' => $_POST['order_number']
    );
    
    $db->runQuery($query, $query_params);

    header("Location: ../all-orders/?completed=success");
    die();
  }
  else
  {
    die("Error completing order");
  }
?>
