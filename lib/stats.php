<?php
  require("common.php");
  
  // Get all order details
  $query = "
    SELECT
      *
    FROM
      orders
  ";

  try
  {
    $stmt     = $db->prepare($query);
    $result   = $stmt->execute();
  }
  catch(PDOException $ex)
  {
    die("Failed to execute query: " . $ex->getMessage() . " query: " . $query);
  }

  $rows = $stmt->fetchAll();

  foreach ($rows as $row)
  {
    $users[$row['customer_id']]['orders']++;
    $users[$row['customer_id']]['customer_id'] = $row['customer_id'];

    $cakes[$row['cake_id']]['cake_id'] = $row['cake_id'];
    $cakes[$row['cake_id']]['value']++;

    $fillings[$row['filling_id']]['filling_id'] = $row['filling_id'];
    $fillings[$row['filling_id']]['value']++;

    $decorations[$row['decor_id']]['decor_id'] = $row['decor_id'];
    $decorations[$row['decor_id']]['value']++;
  }

  // Get first & last name for each customer to display
  foreach ($users as $user)
  {
    $query = "
      SELECT
        *
      FROM
        users
      WHERE
        customer_id = :customer_id
    ";

    $query_params = array(
      ':customer_id' => $user['customer_id']
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
    $users[$user['customer_id']]['first_name'] = $row['first_name'];
    $users[$user['customer_id']]['last_name'] = $row['last_name'];
  }
  
  // Calculate orders placed per month
  for ($i = 0; $i < 13; $i++)
  {
    $query = "
      SELECT
        *
      FROM
        orders
      WHERE
        MONTH(order_placed) = $i
    ";

    try
    {
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute();
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getMessage() . " query: " . $query);
    }

    $rows = $stmt->fetchAll();
    
    foreach ($rows as $row)
    {
      $months[$i-1]++;
    }
  }

  $response = array(
    'orders'      => array(
                       'values' => array(
                         array("X" => "Jan", "Y" => ""),
                         array("X" => "Feb", "Y" => ""),
                         array("X" => "Mar", "Y" => ""),
                         array("X" => "Apr", "Y" => ""),
                         array("X" => "May", "Y" => ""),
                         array("X" => "Jun", "Y" => ""),
                         array("X" => "Jul", "Y" => ""),
                         array("X" => "Aug", "Y" => ""),
                         array("X" => "Sep", "Y" => ""),
                         array("X" => "Oct", "Y" => ""),
                         array("X" => "Nov", "Y" => ""),
                         array("X" => "Dec", "Y" => ""),
                       )
                     ),
    'cakes'       => array(
                       'name' => array("6\"", "8\"", "10\"", "12\"", "14\"", "Sponge", "Marble", "Chocolate", "Fruit"),
                       'value' => array(0, 0, 0, 0, 0, 0, 0, 0)
                     ),
    'fillings'    => array(
                       'name' => array("None", "Butter Cream", "Chocolate", "Other"),
                       'value' => array(0, 0, 0, 0)
                     ),
    'decorations' => array(
                       'name' => array("None", "Royal Icing", "Regal Icing", "Butter Cream", "Chocolate", "Coconut", "Other"),
                       'value' => array(0, 0, 0, 0, 0, 0, 0)
                     )
  );

  foreach ($fillings as $filling)
  {
    $query = "
      SELECT
        *
      FROM
        fillings
      WHERE
        filling_id = :filling_id
    ";

    $query_params = array(
      ':filling_id' => $filling['filling_id']
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

    if ($row['filling_name'] == "Butter Cream")
    {
      $response['fillings']['value'][1] += $filling['value'];
    }
    else if ($row['filling_name'] == "Chocoalte")
    {
      $response['fillings']['value'][2] += $filling['value'];
    }
    else if ($row['filling_name'] == "Other")
    {
      $response['fillings']['value'][3] += $filling['value'];
    }
  }

  foreach ($decorations as $decoration)
  {
    $query = "
      SELECT
        *
      FROM
        decorations
      WHERE
        decor_id = :decor_id
    ";

    $query_params = array(
      ':decor_id' => $decoration['decor_id']
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

    if ($row['decor_name'] == "Royal Icing")
    {
      $response['decorations']['value'][1] += $decoration['value'];
    }
    else if ($row['decor_name'] == "Regal Icing")
    {
      $response['decorations']['value'][2] += $decoration['value'];
    }
    else if ($row['decor_name'] == "Butter Cream")
    {
      $response['decorations']['value'][3] += $decoration['value'];
    }
    else if ($row['decor_name'] == "Chocolate")
    {
      $response['decorations']['value'][4] += $decoration['value'];
    }
    else if ($row['decor_name'] == "Coconut")
    {
      $response['decorations']['value'][5] += $decoration['value'];
    }
    else if ($row['decor_name'] == "Other")
    {
      $response['decorations']['value'][6] += $decoration['value'];
    }
//    else if ($row['decor_name'] == "None")
//    {
//      $response['decorations']['value'][0] += $decoration['value'];
//    }
  }

  // Get cake details
  foreach ($cakes as $cake)
  {
    $query = "
      SELECT
        *
      FROM
        cakes
      WHERE
        cake_id = :cake_id
    ";

    $query_params = array(
      ":cake_id" => $cake['cake_id']
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

    if ($row['cake_size'] == "6\"")
    {
      $response['cakes']['value'][0] += $cake['value'];
    }
    else if ($row['cake_size'] == "8\"")
    {
      $response['cakes']['value'][1] += $cake['value'];
    }
    else if ($row['cake_size'] == "10\"")
    {
      $response['cakes']['value'][2] += $cake['value'];
    }
    else if ($row['cake_size'] == "12\"")
    {
      $response['cakes']['value'][3] += $cake['value'];
    }
    else if ($row['cake_size'] == "14\"")
    {
      $response['cakes']['value'][4] += $cake['value'];
    }

    if ($row['cake_type'] == "Sponge")
    {
      $response['cakes']['value'][5] += $cake['value'];
    }
    else if ($row['cake_type'] == "Marble")
    {
      $response['cakes']['value'][6] += $cake['value'];
    }
    else if ($row['cake_type'] == "Chocolate")
    {
      $response['cakes']['value'][7] += $cake['value'];
    }
    else if ($row['cake_type'] == "Fruit")
    {
      $response['cakes']['value'][8] += $cake['value'];
    }
  }

  for ($i = 0; $i < 9; $i++)
  {
    if (!$response['cakes']['value'][$i])
    {
      $response['cakes']['value'][$i] = 0;
    }
  }

  for ($i = 0; $i < 12; $i++)
  {
    if ($months[$i]) 
    {
      $response['orders']['values'][$i]["Y"] = $months[$i];
    } 
    else 
    {
      $response['orders']['values'][$i]["Y"] = 0;
    }
  }

  echo json_encode($response);
?>
