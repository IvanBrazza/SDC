<?php
  /**
    lib/complete-order.php - mark an order as compelte
  **/
  require("common.php");

  if (!empty($_POST))
  {
    if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token']))
    {
      echo "Invalid token.";
      die();
    }

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

    $query = "
      SELECT
        a.order_number,
        b.first_name, b.email
      FROM
        orders a, users b
      WHERE
        a.order_number = :order_number
      AND
        a.customer_id = b.customer_id
    ";

    $query_params = array(
      ':order_number' => $_POST['order_number']
    );

    $db->runQuery($query, $query_params);
    $row = $db->fetch();

    include("email.class.php");
    $email = new Email;
    $email->setRecipient($row['email']);
    $email->setFirstName($row['first_name']);
    $email->requestTestimonial($row['order_number']);
    $email->send();

    echo "success";
    die();
  }
  else
  {
    die("Error completing order");
  }
?>
