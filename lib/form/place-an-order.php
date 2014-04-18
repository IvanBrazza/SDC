<?php
  require("../common.php");

  // If the order form has been submitted
  if (!empty($_POST))
  {
    if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token']))
    {
      echo "Invalid token.";
      die();
    }

    // Unset the token
    unset($_SESSION['token']);

    // Get the cake_id of the cake based on the cake_size and cake_type
    $query = "
      SELECT
        cake_id
      FROM
        cakes
      WHERE
        cake_size = :cake_size
      AND
        cake_type = :cake_type
    ";

    $query_params = array(
      ':cake_size'  => $_POST['cake_size'],
      ':cake_type'  => $_POST['cake_type']
    );

    $db->runQuery($query, $query_params);

    $row = $db->fetch();
    $cake_id = $row['cake_id'];
    
    // Generate order number and make sure it is unique
    $order_number_unique  = false;
    
    do
    {
      $order_number         = $_SESSION['user']['customer_id'] . rand(10000,99999);
      
      $query = "
        SELECT
          *
        FROM
          orders
        WHERE
          order_number = :order_number
      ";

      $query_params = array(
        ':order_number' => $order_number
      );

      $db->runQuery($query, $query_params);

      $row = $db->fetch();

      if (!$row)
      {
        $order_number_unique = true;
      }
    }
    while ($order_number_unique === false);

    $query = "
      SELECT
        cake_price
      FROM
        cakes
      WHERE
        cake_size = :cake_size
      AND
        cake_type = :cake_type
    ";

    $query_params = array(
      'cake_size' => $_POST['cake_size'],
      'cake_type' => $_POST['cake_type']
    );

    $db->runQuery($query, $query_params);
    $row = $db->fetch();

    $base_price = $row['cake_price'];

    // String together the datetime
    $datetime = $_POST['datetime_date'] . ' ' . $_POST['datetime_time'];

    // Insert the order into the DB
    $query = "
      INSERT INTO orders (
        customer_id,
        order_number,
        celebration_date,
        comments,
        decor_id,
        filling_id,
        cake_id,
        order_placed,
        delivery_type,
        status,
        datetime,";

    if (!empty($_POST['fileupload']))
    {
      $query .= "
          image,
      ";
    }

    $query .= "
        base_price
      ) VALUES (
        :customer_id,
        :order_number,
        :celebration_date,
        :comments,
        :decor_id,
        :filling_id,
        :cake_id,
        :order_placed,
        :delivery_type,
        :status,
        :datetime,";

    if (!empty($_POST['fileupload']))
    {
      $query .= "
        :image,
      ";
    }

    $query .= "
        :base_price
      )
    ";

    $order_placed   = date('Y-m-d H:i:s');
    $status         = "Processing";

    $query_params = array(
      ':customer_id'        => $_SESSION['user']['customer_id'],
      ':order_number'       => $order_number,
      ':celebration_date'   => $_POST['celebration_date'],
      ':comments'           => $_POST['comments'],
      ':decor_id'           => $_POST['decoration'],
      ':filling_id'         => $_POST['filling'],
      ':cake_id'            => $cake_id,
      ':order_placed'       => $order_placed,
      ':delivery_type'      => $_POST['delivery'],
      ':status'             => $status,
      ':datetime'           => $datetime,
      ':base_price'         => $base_price
     );

    if (!empty($_POST['fileupload']))
    {
      $query_params[':image'] = $_POST['fileupload'];
    }

    $db->runQuery($query, $query_params);

    // If the order is to be delivered then calculate the
    // delivery charge and insert the delivery details into
    // the "delivery" DB table.
    if ($_POST['delivery'] === "Deliver To Address")
    {
      include "../delivery.class.php";
      $delivery = new Delivery;
      $delivery->setAddress($_SESSION['user']['address']);
      $delivery->setPostcode($_SESSION['user']['postcode']);
      $delivery->calculateDistance();
      $delivery->calculateDeliveryCharge();
      $distance = $delivery->getDistance();
      $deliveryCharge = $delivery->getDeliveryCharge();

      $query = "
        INSERT INTO delivery (
          order_number,
          miles,
          delivery_charge
        ) VALUES (
          :order_number,
          :miles,
          :delivery_charge
        )
      ";

      $status = "Processing";

      $query_params = array(
        ':order_number'     => $order_number,
        ':miles'            => $distance,
        ':delivery_charge'  => $deliveryCharge
      );

      $db->runQuery($query, $query_params);
    }

    // Start PayPal payment process
    include "../PayPal/PayWithPayPal.php";

    echo "success";
    die();
  }
