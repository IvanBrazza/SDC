<?php 
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
      ':status' => $_POST['status'],
      ':order_number' => $_POST['order_number']
    );

    try
    {
      $stmt = $db->prepare($query);
      $result = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to run query: " . $ex->getMessage());
    }

    if ($_POST['id'] === "0")
    {
      header("Location: all-orders");
      die();
    }

    header("Location: all-orders/?id=" . $_POST['id']);
    die();
  } else if (!empty($_POST['agreed_price']))
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
      ':agreed_price' => $_POST['agreed_price'],
      ':order_number' => $_POST['order_number']
    );
    
    try
    {
      $stmt = $db->prepare($query);
      $result = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to run query: " . $ex->getMessage());
    }

    header("Location: all-orders/?order=" . $_POST['order_number'] . "&id=" . $_POST['id']);
    die();
  } else if (!empty($_POST['delivery_charge']))
  {
    $query = "
      UPDATE
        orders
      SET
        delivery_charge = :delivery_charge
      WHERE
        order_number = :order_number
    ";

    $query_params = array(
      ':delivery_charge' => $_POST['delivery_charge'],
      ':order_number' => $_POST['order_number']
    );
    
    try
    {
      $stmt = $db->prepare($query);
      $result = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to run query: " . $ex->getMessage());
    }
    
    header("Location: all-orders/?order=" . $_POST['order_number'] . "&id=" . $_POST['id']);
    die();
  }
?>
