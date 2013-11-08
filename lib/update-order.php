<?php 
  /**
    update-order.php - called from all-orders, this library updates the order
    from a POST request.
  **/
  require("common.php");

  if (!empty($_POST['status']))
  {
    $query = "
      UPDATE
        orders
      SET
        status = :status
      WHERE
        order_number = :order_number
    ";

    $query_params = array(
      ':status'         => $_POST['status'],
      ':order_number'   => $_POST['order_number']
    );

    try
    {
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to run query: " . $ex->getMessage());
    }

    include "../lib/email.php";
    emailStatusUpdate($_POST['email'], $_POST['first_name'], $_POST['order_number'], $_POST['status']);
  }
  else if (!empty($_POST['agreed_price']))
  {
    $query = "
      UPDATE
        orders
      SET
        agreed_price = :agreed_price
      WHERE
        order_number = :order_number
    ";

    $query_params = array(
      ':agreed_price'   => $_POST['agreed_price'],
      ':order_number'   => $_POST['order_number']
    );
    
    try
    {
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to run query: " . $ex->getMessage());
    }
  }
  else if (!empty($_POST['delivery_charge']))
  {
    $query = "
      UPDATE
        delivery
      SET
        delivery_charge = :delivery_charge
      WHERE
        order_number = :order_number
    ";

    $query_params = array(
      ':delivery_charge'  => $_POST['delivery_charge'],
      ':order_number'     => $_POST['order_number']
    );
    
    try
    {
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to run query: " . $ex->getMessage());
    }
  }
  header("Location: ../all-orders/?order=" . $_POST['order_number']);
  die();
?>
