<?php
  require("../common.php");

  // If the order form has been submitted
  if (!empty($_POST))
  {
    // Get the cake_id of the cake based on the cake_size and
    // cake_type provided by the user.
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
    
    try
    {
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      echo "Oops! Something went wrong. Try again.";
      die("Failed to execute query: " . $ex->getMessage() . " query: " . $query);
    }

    $row = $stmt->fetch();
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

      try
      {
        $stmt     = $db->prepare($query);
        $result   = $stmt->execute($query_params);
      }
      catch(PDOException $ex)
      {
        echo "Oops! Something went wrong. Try again.";
        die("Failed to execute query: " . $ex->getMessage() . " query: " . $query);
      }

      $row = $stmt->fetch();

      if (!$row)
      {
        $order_number_unique = true;
      }
    }
    while ($order_number_unique === false);

    // Insert the order into the DB
    $query = "
      INSERT INTO orders (
        customer_id,
        order_number,
        celebration_date,
        comments,
        decoration,
        filling,
        cake_id,
        order_date,
        delivery_type,
        status,
        datetime,
        base_price
      ) VALUES (
        :customer_id,
        :order_number,
        :celebration_date,
        :comments,
        :decoration,
        :filling,
        :cake_id,
        :order_date,
        :delivery_type,
        :status,
        :datetime,
        :base_price
      )
    ";

    $order_date     = date('Y-m-d');
    $status         = "Processing";

    $query_params = array(
      ':customer_id'        => $_SESSION['user']['customer_id'],
      ':order_number'       => $order_number,
      ':celebration_date'   => $order_date,
      ':comments'           => $_POST['comments'],
      ':decoration'         => $_POST['decoration'],
      ':filling'            => $_POST['filling'],
      ':cake_id'            => $cake_id,
      ':order_date'         => $order_date,
      ':delivery_type'      => $_POST['delivery'],
      ':status'             => $status,
      ':datetime'           => $_POST['datetime'],
      ':base_price'       => $_POST['base-hidden']
     );

    try
    {
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      echo "Oops! Something went wrong. Try again.";
      die("Failed to run query: " . $ex->getMessage());
    }
    
    // If the order is to be delivered then calculate the
    // delivery charge and insert the delivery details into
    // the "delivery" DB table.
    if ($_POST['delivery'] === "Deliver To Address")
    {
      include "../distance.php";
      $miles = calculateDistance($_SESSION['user']['address'], $_SESSION['user']['postcode']);
      $remaining_miles = $miles - 5;
      $remaining_miles = round($remaining_miles / 5) * 5;
      if ($remaining_miles <= 0)
      {
        $delivery_charge = 0;
      }
      else
      {
        for ($i = 5, $j = 1; $i <= 50; $i = $i + 5, $j++)
        {
          if ($remaining_miles == $i)
          {
            $delivery_charge = $j;
          }
        }
      }

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
        ':delivery_charge'  => $delivery_charge
      );

      try
      {
        $stmt     = $db->prepare($query);
        $result   = $stmt->execute($query_params);
      }
      catch(PDOException $ex)
      {
        echo "Oops! Something went wrong. Try again.";
        die("Failed to run query: " . $ex->getMessage() . "query: " . $query);
      }
    }

    // Email the order details to the user
    include "../email.php";
    emailOrder($_SESSION['user']['email'], 
               $_SESSION['user']['first_name'],
               $order_number,
               $order_date,
               $_POST["datetime"],
               $_POST["celebration_date"],
               $_POST["comments"],
               $_POST["filling"],
               $_POST["decoration"],
               $_POST["cake_type"],
               $_POST["cake_size"],
               $_POST["delivery"]);
    
    echo "success";
    die();
  }
