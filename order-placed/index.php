<?php
  require("../lib/common.php");
  if (!empty($_GET) and $_GET['failed'] == "true")
  {
    $title = "Order cancelled";
  }
  else
  {
    $title = "Thanks for your order!";
  }
  $page = "place-an-order";

  if (!empty($_GET) and $_GET['failed'] == "false")
  {
    include("../lib/email.php");
    $email = new Email;

    $query = "
      SELECT
        a.order_number,
        a.order_placed,
        a.datetime,
        a.celebration_date,
        a.comments,
        a.filling,
        a.decoration,
        a.delivery_type,
        b.cake_type,
        b.cake_size
      FROM
        orders a,
        cakes b
      WHERE
        a.order_number = :order_number
      AND
        a.cake_id = b.cake_id
    ";

    $query_params = array(
      ':order_number' => $_GET['order']
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

    // Email the order details to the user
    $email->setFirstName($_SESSION['user']['name']);
    $email->setRecipient($_SESSION['user']['email']);
    $email->order($row["order_number"],
                  $row["order_placed"],
                  $row["datetime"],
                  $row["celebration_date"],
                  $row["comments"],
                  $row["filling"],
                  $row["decoration"],
                  $row["cake_type"],
                  $row["cake_size"],
                  $row["delivery_type"]);
    $email->send();
  }
  else if (!empty($_GET) and $_GET['failed'] == "true")
  {
    $query = "
      SELECT
        delivery_type
      FROM
        orders
      WHERE
        order_number = :order_number
    ";

    $query_params = array(
      ':order_number' => $_GET['order']
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

    if ($row['delivery_type'] == "Deliver To Address")
    {
      $query = "
        DELETE FROM
          delivery
        WHERE
          order_number = :order_number
      ";

      $query_params = array(
        ':order_number' => $_GET['order']
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
    }
    $query = "
      DELETE FROM
        orders
      WHERE
        order_number = :order_number
    ";

    $query_params = array(
      ':order_number' => $_GET['order']
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
  }
?>
<?php include("../lib/header.php"); ?>
  <?php if (!empty($_GET['failed']) and $_GET['failed'] == "true") : ?>
    <h3>Your order has been cancelled.</h3>
  <?php else : ?>
    <h3>Thank you for your order, a copy of it has been emailed to you. Any further updates to your order will be sent to you by email</h3>
  <?php endif; ?>
<?php include("../lib/footer.php"); ?>
