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
        archived = 1
      WHERE
        order_number = :order_number
    ";

    $query_params = array(
      ':order_number' => $_POST['order_number']
    );

    try
    {
      $stmt   = $db->prepare($query);
      $result = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getMessage() . " query: " . $query);
    }

    header("Location: ../all-orders/?archive=success");
    die();
  }
  else
  {
    die("Error arhiving order");
  }
?>
