<?php
  /**
    order-placed/ - thank the user for their order and
    email a confirmation to them or delete it from the
    database if they cancelled it
  **/
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

  // If the order was successful, get the order details
  // and send an email confirmation. Otherwise, delete the
  // order from the database if it was not successful
  if (!empty($_GET) and $_GET['failed'] == "false")
  {
    include("../lib/email.class.php");
    $email = new Email;

    $query = "
      SELECT
        a.order_number,
        a.order_placed,
        a.datetime,
        a.celebration_date,
        a.comments,
        a.delivery_type,
        b.cake_type,
        b.cake_size,
        c.filling_name,
        d.decor_name
      FROM
        orders a,
        cakes b,
        fillings c,
        decorations d
      WHERE
        a.order_number = :order_number
      AND
        a.cake_id = b.cake_id
      AND
        a.filling_id = c.filling_id
      AND
        a.decor_id = d.decor_id
    ";

    $query_params = array(
      ':order_number' => $_GET['order']
    );

    $db->runQuery($query, $query_params);

    $row = $db->fetch();

    // Email the order details to the user
    $email->setFirstName($_SESSION['user']['name']);
    $email->setRecipient($_SESSION['user']['email']);
    $email->order($row);
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

    $db->runQuery($query, $query_params);

    $row = $db->fetch();

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

      $db->runQuery($query, $query_params);
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

    $db->runQuery($query, $query_params);
  }
?>
<?php include("../lib/header.php"); ?>
  <?php if (!empty($_GET['failed']) and $_GET['failed'] == "true") : ?>
    <h3>Your order has been cancelled.</h3>
  <?php else : ?>
    <h3>Thank you for your order, a copy of it has been emailed to you. Any further updates to your order will be sent to you by email</h3>
  <?php endif; ?>
<?php include("../lib/footer.php"); ?>
