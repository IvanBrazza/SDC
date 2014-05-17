<?php
  require("../common.php");

  if (!empty($_POST))
  {
    if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token']))
    {
      $response = array(
        "status" => "error",
        "error"  => "Invalid token (try refreshing the page)",
        "code"   => "001"
      );

      echo json_encode($response);
      die();
    }

    // Unset token
    unset($_SESSION['token']);

    // Get base price, filling & decor details
    $query = "
      SELECT
        a.cake_price,
        b.filling_price, b.filling_name,
        c.decor_price, c.decor_name
      FROM
        cakes a, fillings b, decorations c
      WHERE
        a.cake_size = :cake_size
      AND
        a.cake_type = :cake_type
      AND
        b.filling_id = :filling_id
      AND
        c.decor_id = :decor_id
    ";
    $query_params = array(
      ':cake_size'  => $_POST['cakeSize'],
      ':cake_type'  => $_POST['cakeType'],
      ':filling_id' => $_POST['fillingId'],
      ':decor_id'   => $_POST['decorId']
    );
    $db->runQuery($query, $query_params);
    $row = $db->fetch();

    // Calculate delivery charge (if appropriate)
    if ($_POST['delivery'] == "Deliver To Address")
    {
      include "../delivery.class.php";
      $delivery = new Delivery;
      $delivery->setAddress($_POST['address']);
      $delivery->setPostcode($_POST['postcode']);
      $delivery->calculateDistance();
      $delivery->calculateDeliveryCharge();
      $delivery_charge = $delivery->getDeliveryCharge();
    }
    else
    {
      $delivery_charge = 0;
    }

    // Calculate total
    $total = $row['cake_price'] + $delivery_charge + $row['decor_price'] + $row['filling_price'];

    // Generate token
    $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

    // Send response
    $response = array(
      'status'         => 'success',
      'token'          => $_SESSION['token'],
      'basePrice'      => $row['cake_price'],
      'fillingName'    => $row['filling_name'],
      'fillingPrice'   => $row['filling_price'],
      'decorName'      => $row['decor_name'],
      'decorPrice'     => $row['decor_price'],
      'deliveryCharge' => $delivery_charge,
      'total'          => $total
    );
    echo json_encode($response);
    die();
  }
