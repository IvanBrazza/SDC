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
    shell_exec("php ../lib/sendEmail.php '" . $_SESSION['user']['email'] . "' '" . $_SESSION['user']['name']  .
               "' >> /var/log/lighttpd/email.log &");
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
