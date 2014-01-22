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
    // Calculate popular filling & decoration
    $fillings[$row['filling']]['name'] = $row['filling'];
    $fillings[$row['filling']]['amount']++;
    $largestFilling = 0;
    foreach ($fillings as $filling)
    {
      $largestFilling = max($largestFilling, $filling['amount']);
    }

    $decorations[$row['decoration']]['name'] = $row['decoration'];
    $decorations[$row['decoration']]['amount']++;
    $largestDecoration = 0;
    foreach ($decorations as $decoration)
    {
      $largestDecoration = max($largestDecoration, $decoration['amount']);
    }
    $users[$row['customer_id']]['orders']++;
    $users[$row['customer_id']]['customer_id'] = $row['customer_id'];

    $cakes[$row['cake_id']]['cake_id'] = $row['cake_id'];
    $cakes[$row['cake_id']]['value']++;
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
                       'value' => array()
                     ),
    'decorations' => array(
                       'name' => array("None", "Royal Icing", "Regal Icing", "Butter Cream", "Chocolate", "Coconut", "Other"),
                       'value' => array()
                     )
  );

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

  if ($fillings['None']['amount']) {
    $response['fillings']['value'][0] =  $fillings['None']['amount'];
  } else {
    $response['fillings']['value'][0] =  0;
  }
  if ($fillings['Butter Cream']['amount']) {
    $response['fillings']['value'][1] =  $fillings['Butter Cream']['amount'];
  } else {
    $response['fillings']['value'][1] =  0;
  }
  if ($fillings['Chocolate']['amount']) {
    $response['fillings']['value'][2] =  $fillings['Chocolate']['amount'];
  } else {
    $response['fillings']['value'][2] =  0;
  }
  if ($fillings['Other']['amount']) {
    $response['fillings']['value'][3] =  $fillings['Other']['amount'];
  } else {
    $response['fillings']['value'][3] =  0;
  }

  if ($decorations['None']['amount']) {
    $response['decorations']['value'][0] =  $decorations['None']['amount'];
  } else {
    $response['decorations']['value'][0] =  0;
  }
  if ($decorations['Royal Icing']['amount']) {
    $response['decorations']['value'][1] =  $decorations['Royal Icing']['amount'];
  } else {
    $response['decorations']['value'][1] =  0;
  }
  if ($decorations['Regal Icing']['amount']) {
    $response['decorations']['value'][2] =  $decorations['Regal Icing']['amount'];
  } else {
    $response['decorations']['value'][2] =  0;
  }
  if ($decorations['Butter Cream']['amount']) {
    $response['decorations']['value'][3] =  $decorations['Butter Cream']['amount'];
  } else {
    $response['decorations']['value'][3] =  0;
  }
  if ($decorations['Chocolate']['amount']) {
    $response['decorations']['value'][4] =  $decorations['Chocolate']['amount'];
  } else {
    $response['decorations']['value'][4] =  0;
  }
  if ($decorations['Coconut']['amount']) {
    $response['decorations']['value'][5] =  $decorations['Coconut']['amount'];
  } else {
    $response['decorations']['value'][5] =  0;
  }
  if ($decorations['Other']['amount']) {
    $response['decorations']['value'][6] =  $decorations['Other']['amount'];
  } else {
    $response['decorations']['value'][6] =  0;
  }

  echo json_encode($response);
?>
