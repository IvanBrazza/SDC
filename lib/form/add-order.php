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
  
      try
      {
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
      }
      catch(PDOException $ex)
      {
        die("Failed to execute query: " . $ex->getMessage());
      }
  
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
  
      try
      {
        $stmt = $db->prepare($query);
        $result = $stmt->execute();
      }
      catch(PDOException $ex)
      {
        die("Failed to execute query: " . $ex->getMessage() . "Query: " . $query);
      }
  
      $row = $stmt->fetch();
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
  
      try
      {
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
      }
      catch(PDOException $ex)
      {
        die("Failed to execute query: " . $ex->getMessage() . "Query: " . $query);
      }
  
      $userrow = $stmt->fetch();
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

    try
    {
      $stmt = $db->prepare($query);
      $result = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getMessage() . "query: " . $query);
    }

    $cake_row = $stmt->fetch();

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

      if (!$row)
      {
        $order_number_unique = true;
      }
    }
    while ($order_number_unique === false);

    // Calculate base price
    switch ($_POST['cake_size'])
    {
      case '6"':
        switch ($_POST['cake_type'])
        {
          case "Sponge":
            $base_price = 25;
            break;
          case "Marble":
            $base_price = 30;
            break;
          case "Chocolate":
            $base_price = 32;
            break;
          case "Fruit":
            $base_price = 35;
            break;
        }
        break;
      case '8"':
        switch ($_POST['cake_type'])
        {
          case "Sponge":
            $base_price = 30;
            break;
          case "Marble":
            $base_price = 35;
            break;
          case "Chocolate":
            $base_price = 37;
            break;
          case "Fruit":
            $base_price = 45;
            break;
        }
        break;
      case '10"':
        switch ($_POST['cake_type'])
        {
          case "Sponge":
            $base_price = 40;
            break;
          case "Marble":
            $base_price = 45;
            break;
          case "Chocolate":
            $base_price = 47;
            break;
          case "Fruit":
            $base_price = 60;
            break;
        }
        break;
      case '12"':
        switch ($_POST['cake_type'])
        {
          case "Sponge":
            $base_price = 60;
            break;
          case "Marble":
            $base_price = 65;
            break;
          case "Chocolate":
            $base_price = 80;
            break;
          case "Fruit":
            $base_price = 85;
            break;
        }
        break;
      case '14"':
        switch ($_POST['cake_type'])
        {
          case "Sponge":
            $base_price = 75;
            break;
          case "Marble":
            $base_price = 80;
            break;
          case "Chocolate":
            $base_price = 84;
            break;
          case "Fruit":
            $base_price = 125;
            break;
        }
        break;
      default:
        echo "Oops! Something went wrong. Try again.";
        die();
    }

    // Insert the new order into the orders table
    $query = "
      INSERT INTO orders(
        customer_id,
        order_number,
        celebration_date,
        comments,
        decoration,
        filling,
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
    $order_placed     = date('Y-m-d H:i:s');
    $status         = "Processing";
    $cake_id        = $cake_row['cake_id'];

    $query_params = array(
      ':customer_id'      => $customer_id,
      ':order_number'     => $order_number,
      ':celebration_date' => $_POST['celebration_date'],
      ':comments'         => $_POST['comments'],
      ':decoration'       => $_POST['decoration'],
      ':filling'          => $_POST['filling'],
      ':cake_id'          => $cake_id,
      ':base_price'       => $base_price,
      ':order_placed'     => $order_placed,
      ':delivery_type'    => $_POST['delivery'],
      ':status'           => $status,
      ':datetime'         => $_POST['datetime']
    );

    try
    {
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getMessage() . "Query: " . $query);
    }

    // If the order is for delivery
    if ($_POST['delivery'] === "Deliver To Address")
    {
      // Calculate the delivery charge
      include "../delivery.php";
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
        ':miles'            => $miles,
        ':delivery_charge'  => $delivery->getDeliveryCharge()
      );

      try
      {
        $stmt     = $db->prepare($query);
        $result   = $stmt->execute($query_params);
      }
      catch(PDOException $ex)
      {
        die("Failed to run query: " . $ex->getMessage() . "query: " . $query);
      }
    }
    
    echo "success";
    die();
  }
