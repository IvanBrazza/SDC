<?php
  require("../common.php");
  include_once("../email.php");

  $email = new Email;

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

    // Check if an image was uploaded, if it was make sure it's valid
    if (!empty($_FILES['fileupload']['size']))
    {
      // Check file size
      if ($_FILES['fileupload']['size'] > 5242880)
      {
        echo "Image too large.";
        die();
      }
      // Check file type
      if ($_FILES['fileupload']['type'] == "image/gif" or 
          $_FILES['fileupload']['type'] == "image/jpeg" or
          $_FILES['fileupload']['type'] == "image/jpg" or
          $_FILES['fileupload']['type'] == "image/png")
      {
        // All good, let's move the file
        $uploaddir = "/var/www/ivanbrazza.biz/htdocs/upload/" . $_SESSION['user']['customer_id'] . "/";
        $uploadfile = $uploaddir . basename($_FILES['fileupload']['name']);
        if (!is_dir($uploaddir))
        {
          mkdir($uploaddir, 0777, true);
        }
        if (!move_uploaded_file($_FILES['fileupload']['tmp_name'], $uploadfile))
        {
          echo "Oops! Something went wrong. Try again.";
          die();
        }
      }
      else
      {
        error_log($_FILES['fileupload']['type'] . " uploaded", 0);
        echo "Image must be .jpeg, .png or .gif.";
        die();
      }
    }

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
        order_placed,
        delivery_type,
        status,
        datetime,";

    if (!empty($_FILES))
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
        :decoration,
        :filling,
        :cake_id,
        :order_placed,
        :delivery_type,
        :status,
        :datetime,";

    if (!empty($_FILES))
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
      ':decoration'         => $_POST['decoration'],
      ':filling'            => $_POST['filling'],
      ':cake_id'            => $cake_id,
      ':order_placed'       => $order_placed,
      ':delivery_type'      => $_POST['delivery'],
      ':status'             => $status,
      ':datetime'           => $_POST['datetime'],
      ':base_price'         => $base_price
     );

    if (!empty($_FILES))
    {
      $query_params[':image'] = str_replace("/var/www/ivanbrazza.biz/htdocs/", "../", $uploadfile);
    }

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
        for ($i = 5, $j = 3; $i <= 50; $i = $i + 5, $j = $j + 3)
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
    $email->order($order_number,
                  $order_placed,
                  $_POST["datetime"],
                  $_POST["celebration_date"],
                  $_POST["comments"],
                  $_POST["filling"],
                  $_POST["decoration"],
                  $_POST["cake_type"],
                  $_POST["cake_size"],
                  $_POST["delivery"]);
    $email->setFirstName($_SESSION['user']['first_name']);
    $email->setRecipient($_SESSION['user']['email']);
    $email->send();

    // Start PayPal payment process
    include "../PayPal/PayWithPayPal.php";

    echo "success";
    die();
  }
