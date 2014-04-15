<?php
  require("../common.php");

  // If the form was submitted
  if ($_POST)
  {
    if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token']))
    {
      echo "Invalid token.";
      die();
    }

    // Unset token
    unset($_SESSION['token']);

    // If we're inserting an order for a customer that
    // isn't registered on the site
    if ($_POST['existing_id'] === "null")
    {
      // Insert the customer into the users table
      $query = "
        INSERT INTO users(
          first_name,
          last_name,
          address,
          postcode,
          phone,
          email
        ) VALUES (
          :first_name,
          :last_name,
          :address,
          :postcode,
          :phone,
          :email
        )
      ";
  
      $query_params = array(
          ':first_name' => $_POST['first_name'],
          ':last_name'  => $_POST['last_name'],
          ':address'    => $_POST['address'],
          ':postcode'   => $_POST['postcode'],
          ':phone'      => $_POST['phone'],
          ':email'      => $_POST['email']
      );

      $db->runQuery($query, $query_params);

      // Get the customer_id of the new user we just created
      // so we can use it in the orders table
      $query = "
        SELECT
          *
        FROM
          users
        ORDER BY
          customer_id DESC
        LIMIT
          1
      ";

      $db->runQuery($query, null);

      $row = $db->fetch();
    }
    else
    {
      $query = "
        SELECT
          address,
          postcode
        FROM
          users
        WHERE
          customer_id = :customer_id
      ";

      $query_params = array(
        ':customer_id' => $_POST['existing_id']
      );

      $db->runQuery($query, $query_params);

      $userrow = $db->fetch();
    }

    // Get the cake ID of the cake based on
    // the type and size given by the user
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
      ':cake_size' => $_POST['cake_size'],
      ':cake_type' => $_POST['cake_type']
    );

    $db->runQuery($query, $query_params);

    $cake_row = $db->fetch();

    // Generate order number and make sure it is unique
    $order_number_unique  = false;
    
    do
    {
      $order_number   = "m" . rand(10000,99999);
      
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

    // Calculate base price
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

    // String together the celebration date
    $celebration_date = $_POST['date_year'] . '/' . $_POST['date_month'] . '/' . $_POST['date_day'];

    // String together the datetime
    $datetime = $_POST['datetime_year'] . '/' . $_POST['datetime_month'] . '/' . $_POST['datetime_day'] . ' ' .
                $_POST['datetime_hour'] . ':' . $_POST['datetime_minute'] . ':00';

    // String together the order placed
    $order_placed = $_POST['placed_year'] . '/' . $_POST['placed_month'] . '/' . $_POST['placed_day'] . ' ' .
                    $_POST['placed_hour'] . ':' . $_POST['placed_minute'] . ':00';

    // Insert the new order into the orders table
    $query = "
      INSERT INTO orders(
        customer_id,
        order_number,
        celebration_date,
        comments,
        decor_id,
        filling_id,
        cake_id,
        base_price,
        order_placed,
        delivery_type,
        status,
        datetime
      ) VALUES (
        :customer_id,
        :order_number,
        :celebration_date,
        :comments,
        :decoration,
        :filling,
        :cake_id,
        :base_price,
        :order_placed,
        :delivery_type,
        :status,
        :datetime
      )
    ";
    
    // If new customer use the ID from the DB,
    // Else use the one from the form
    if ($_POST['existing_id'] === "null")
    {
      $customer_id = $row['customer_id'];
    }
    else
    {
      $customer_id = $_POST['existing_id'];
    }
    $status         = "Processing";
    $cake_id        = $cake_row['cake_id'];

    $query_params = array(
      ':customer_id'      => $customer_id,
      ':order_number'     => $order_number,
      ':celebration_date' => $celebration_date,
      ':comments'         => $_POST['comments'],
      ':decoration'       => $_POST['decoration'],
      ':filling'          => $_POST['filling'],
      ':cake_id'          => $cake_id,
      ':base_price'       => $base_price,
      ':order_placed'     => $order_placed,
      ':delivery_type'    => $_POST['delivery'],
      ':status'           => $status,
      ':datetime'         => $datetime
    );

    $db->runQuery($query, $query_params);

    // If the order is for delivery
    if ($_POST['delivery'] === "Deliver To Address")
    {
      // Calculate the delivery charge
      include "../delivery.class.php";
      $delivery = new Delivery;
      $delivery->setAddress($userrow['address']);
      $delivery->setPostcode($userrow['postcode']);
      $delivery->calculateDistance();
      $delivery->calculateDeliveryCharge();

      // Insert the delivery details into the "delivery" DB table
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
        ':miles'            => $delivery->getDistance(),
        ':delivery_charge'  => $delivery->getDeliveryCharge()
      );

      $db->runQuery($query, $query_params);
    }
    
    header("Location: ../../all-orders/?new-order=added");
    die();
  }
