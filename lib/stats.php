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
  for ($i = 0; $i < 12; $i++)
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
                       'name' => array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"),
                       'value' => array()
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

  for ($i = 0; $i < 12; $i++)
  {
    if ($months[$i]) 
    {
      $response['orders']['value'][$i] = $months[$i];
    } 
    else 
    {
      $response['orders']['value'][$i] = 0;
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
