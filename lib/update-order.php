<?php 
  /**
    update-order.php - called from all-orders, this library updates the order
    from a POST request.
  **/
  require("common.php");
  include_once("email.php");
  
  $email = new Email;

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

    $email->statusUpdate($_POST['order_number'], $_POST['status']);
    $email->setFirstName($_POST['first_name']);
    $email->setRecipient($_POST['email']);
    $email->send();
  }
  else if (!empty($_POST['base_price']))
  {
    $query = "
      UPDATE
        orders
      SET
        base_price = :base_price
      WHERE
        order_number = :order_number
    ";

    $query_params = array(
      ':base_price'   => $_POST['base_price'],
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
