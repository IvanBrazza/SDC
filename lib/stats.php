<?php
  /**
    lib/stats.php - a script which gets all the details
    to be displayed on the stats page and returns it in
    JSON format
  **/
  require("common.php");

  // Start building the response. Orders contains X and Y values for each month for the line graph,
  // cakes contains values for each cake type and size (default 0), fillings contains values for each filling (default
  // 0) and decorations contains values for each decoration (default 0)
  $fillColor = "#d0edeb";
  $strokeColor = "#21a2e6";
  $response = array(
    'orders'   => array(
      'labels' => array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'),
      'data'   => array(0,0,0,0,0,0,0,0,0,0,0,0)
    ),
    'cakes'    => array(
      'labels' => array('6"', '8"', '10"', '12"', '14"', 'Sponge', 'Marble', 'Chocolate', 'Fruit'),
      'data'   => array(0,0,0,0,0,0,0,0,0)
    ),
    'fillings' => array(
      'labels' => array(),
      'data'   => array()
    ),
    'decorations' => array(
      'labels'    => array(),
      'data'      => array()
    )
  );

  // Initiate fillings & decorations
  $query = "
    SELECT
      a.*, b.*
    FROM
      fillings a, decorations b
  ";

  $db->runQuery($query, null);
  $rows = $db->fetchAll();
  foreach ($rows as $row)
  {
    $fillings[$row['filling_id']]['filling_name'] = $row['filling_name'];
    $fillings[$row['filling_id']]['filling_id'] = $row['filling_id'];
    $fillings[$row['filling_id']]['value'] = 0;
    $decorations[$row['decor_id']]['decor_name'] = $row['decor_name'];
    $decorations[$row['decor_id']]['decor_id'] = $row['decor_id'];
    $decorations[$row['decor_id']]['value'] = 0;
  }

  // Get all order details
  $query = "
    SELECT
      *
    FROM
      orders
  ";

  $db->runQuery($query, $query_params);

  $rows = $db->fetchAll();

  // Calculate the popularity of each cake ID, filling and decoration
  foreach ($rows as $row)
  {
    // +1 each cake ID
    $cakes[$row['cake_id']]['cake_id'] = $row['cake_id'];
    $cakes[$row['cake_id']]['value']++;

    // +1 each filling
    $fillings[$row['filling_id']]['value']++;

    // +1 each decoration
    $decorations[$row['decor_id']]['value']++;
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

    $db->runQuery($query, null);

    $rows = $db->fetchAll();
    
    foreach ($rows as $row)
    {
      $months[$i-1]++;
    }
  }

  // Add each filling name and value to their respective arrays in the response
  foreach ($fillings as $filling)
  {
    array_push($response['fillings']['labels'], $filling['filling_name']);
    array_push($response['fillings']['data'], $filling['value']);
  }

  // Add each decoration name and value to their respective arrays in the response
  foreach ($decorations as $decoration)
  {
    array_push($response['decorations']['labels'], $decoration['decor_name']);
    array_push($response['decorations']['data'], $decoration['value']);
  }

  // For each cake ID, get the cake details from the database and add its popularity to the response
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

    $db->runQuery($query, $query_params);

    $row = $db->fetch();

    if ($row['cake_size'] == "6\"")
    {
      $response['cakes']['data'][0] += $cake['value'];
    }
    else if ($row['cake_size'] == "8\"")
    {
      $response['cakes']['data'][1] += $cake['value'];
    }
    else if ($row['cake_size'] == "10\"")
    {
      $response['cakes']['data'][2] += $cake['value'];
    }
    else if ($row['cake_size'] == "12\"")
    {
      $response['cakes']['data'][3] += $cake['value'];
    }
    else if ($row['cake_size'] == "14\"")
    {
      $response['cakes']['data'][4] += $cake['value'];
    }

    if ($row['cake_type'] == "Sponge")
    {
      $response['cakes']['data'][5] += $cake['value'];
    }
    else if ($row['cake_type'] == "Marble")
    {
      $response['cakes']['data'][6] += $cake['value'];
    }
    else if ($row['cake_type'] == "Chocolate")
    {
      $response['cakes']['data'][7] += $cake['value'];
    }
    else if ($row['cake_type'] == "Fruit")
    {
      $response['cakes']['data'][8] += $cake['value'];
    }
  }

  // For each month, add its popularity to the response
  for ($i = 0; $i < 12; $i++)
  {
    if ($months[$i]) 
    {
      $response['orders']['data'][$i] = $months[$i];
    } 
  }

  // Return the response in JSON format
  echo json_encode($response);
?>
