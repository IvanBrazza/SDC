<?php 
  /**
    update-order.php - called from all-orders, this library updates the order
    from a POST request.
  **/
  require("common.php");
  include_once("email.class.php");
  
  $email = new Email;

  // Update the status of the order and email a
  // status update email to the customer. Else update
  // the base price based on the form data. Else update
  // the delivery charge based on the form data
  if (!empty($_POST['status']))
  {
    if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token']))
    {
      $response = array(
        "status" => "Invalid token"
      );
      echo json_encode($response);
      die();
    }

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

    $db->runQuery($query, $query_params);

    $email->setFirstName($_POST['first_name']);
    $email->setRecipient($_POST['email']);
    $email->statusUpdate($_POST['order_number'], $_POST['status']);
    $email->send();
  }
  else if (!empty($_POST['base_price']))
  {
    if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token']))
    {
      $response = array(
        "status" => "Invalid token"
      );
      echo json_encode($response);
      die();
    }

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
    
    $db->runQuery($query, $query_params);
  }
  else if (!empty($_POST['delivery_charge']))
  {
    if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token']))
    {
      $response = array(
        "status" => "Invalid token"
      );
      echo json_encode($response);
      die();
    }

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
    
    $db->runQuery($query, $query_params);
  }

  $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

  $response = array(
    "status"  => "success",
    "token"   => $_SESSION['token'],
    "message" => ""
  );

  if ($_POST['status'])
  {
    $response['message'] = "Order status updated to " . $_POST['status'];
  }
  else if ($_POST['base_price'])
  {
    $response['message'] = "Base price updated to &pound;" . $_POST['base_price'];
  }
  else if ($_POST['delivery_charge'])
  {
    $response['message'] = "Delivery charge updated to &pound;" . $_POST['delivery_charge'];
  }

  echo json_encode($response);
  die();
?>
