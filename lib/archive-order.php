<?php
  /**
    lib/archive-order.php - a library to archive an order
  **/
  require("common.php");

  if (!empty($_POST))
  {
    $query = "
      SELECT
        *
      FROM
        orders
      WHERE
        order_number = :order_number
    ";

    $query_params = array(
      ':order_number' => $_POST['order_number']
    );

    try
    {
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getMessage());
    }

    $row = $stmt->fetch();

    $query = "
      INSERT INTO archived_orders (
        customer_id,
        order_number,
        order_date,
        datetime,
        celebration_date,
        status,
        customer_order,
        filling,
        size,
        design,
        decoration,
        delivery,
        agreed_price,
        delivery_charge,
        grand_total
      ) VALUES (
        :customer_id,
        :order_number,
        :order_date,
        :datetime,
        :celebration_date,
        :status,
        :customer_order,
        :filling,
        :size,
        :design,
        :decoration,
        :delivery,
        :agreed_price,
        :delivery_charge,
        :grand_total
      )
    ";

    $query_params = array(
        ':customer_id'      => $row['customer_id'],
        ':order_number'     => $row['order_number'],
        ':order_date'       => $row['order_date'],
        ':datetime'         => $row['datetime'],
        ':celebration_date' => $row['celebration_date'],
        ':status'           => $row['status'],
        ':customer_order'   => $row['customer_order'],
        ':filling'          => $row['filling'],
        ':size'             => $row['size'],
        ':design'           => $row['design'],
        ':decoration'       => $row['design'],
        ':delivery'         => $row['delivery'],
        ':agreed_price'     => $row['agreed_price'],
        ':delivery_charge'  => $row['delivery_charge'],
        ':grand_total'      => $row['grand_total']
    );
    
    try
    {
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getMessage());
    }

    $query = "
      DELETE FROM
        orders
      WHERE
        order_number = :order_number
    ";

    $query_params = array(
      ':order_number' => $_POST['order_number']
    );

    try
    {
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getMessage());
    }

    header("Location: ../all-orders/?archive=success");
    die();
  }
  else
  {
    die("Error arhiving order");
  }
?>
