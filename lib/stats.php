<?php
  /**
    lib/stats.php - a script which gets all the details
    to be displayed on the stats page and returns it in
    JSON format
  **/
  require("common.php");
  
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
    $fillings[$row['filling_id']]['filling_id'] = $row['filling_id'];
    $fillings[$row['filling_id']]['value']++;

    // +1 each decoration
    $decorations[$row['decor_id']]['decor_id'] = $row['decor_id'];
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

  // Start building the response. Orders contains X and Y values for
  // each month for the line graph, cakes contains values for each cake type
  // and size (default 0), fillings contains values for each filling (default
  // 0) and decorations contains values for each decoration (default 0)
  $fillColor = "#d0edeb";
  $strokeColor = "#21a2e6";
  $response = array(
    'orders'      => array(
                       'labels' => array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"),
                       'datasets' => array(
                         0 => array (
                           "fillColor" => $fillColor,
                           "strokeColor" => $strokeColor,
                           "pointColor" => $strokeColor,
                           "pointStrokeColor" => "#fff",
                           "data" => array(0,0,0,0,0,0,0,0,0,0,0,0)
                         )
                       )
                     ),
    'cakes'       => array(
                       'labels' => array("6\"", "8\"", "10\"", "12\"", "14\"", "Sponge", "Marble", "Chocolate", "Fruit"),
                       'datasets' => array(
                         0 => array(
                           "fillColor" => $fillColor,
                           "strokeColor" => $strokeColor,
                           "data" => array(0,0,0,0,0,0,0,0,0)
                         )
                       )
                     ),
    'fillings'    => array(
                       'labels' => array("None", "Butter Cream", "Chocolate", "Other"),
                       'datasets' => array(
                         0 => array(
                           "fillColor" => $fillColor,
                           "strokeColor" => $strokeColor,
                           "data" => array(0,0,0,0)
                         )
                       )
                     ),
    'decorations' => array(
                       'labels' => array("None", "Royal Icing", "Regal Icing", "Butter Cream", "Chocolate", "Coconut", "Other"),
                       'datasets' => array(
                         0 => array(
                           "fillColor" => $fillColor,
                           "strokeColor" => $strokeColor,
                           "data" => array(0,0,0,0,0,0,0)
                         )
                       )
                     )
  );

  // For each filling ID, get the filling details
  // from the database and add its popularity to
  // the response
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

    $db->runQuery($query, $query_params);

    $row = $db->fetch();

    if ($row['filling_name'] == "None")
    {
      $response['fillings']['datasets'][0]['data'][0] += $filling['value'];
    }
    else if ($row['filling_name'] == "Butter Cream")
    {
      $response['fillings']['datasets'][0]['data'][1] += $filling['value'];
    }
    else if ($row['filling_name'] == "Chocoalte")
    {
      $response['fillings']['datasets'][0]['data'][2] += $filling['value'];
    }
    else if ($row['filling_name'] == "Other")
    {
      $response['fillings']['datasets'][0]['data'][3] += $filling['value'];
    }
  }

  // For each decoration ID, get the decoration details
  // from the database and add its popularity to
  // the response
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

    $db->runQuery($query, $query_params);

    $row = $db->fetch();

    if ($row['decor_name'] == "Royal Icing")
    {
      $response['decorations']['datasets'][0]['data'][1] += $decoration['value'];
    }
    else if ($row['decor_name'] == "Regal Icing")
    {
      $response['decorations']['datasets'][0]['data'][2] += $decoration['value'];
    }
    else if ($row['decor_name'] == "Butter Cream")
    {
      $response['decorations']['datasets'][0]['data'][3] += $decoration['value'];
    }
    else if ($row['decor_name'] == "Chocolate")
    {
      $response['decorations']['datasets'][0]['data'][4] += $decoration['value'];
    }
    else if ($row['decor_name'] == "Coconut")
    {
      $response['decorations']['datasets'][0]['data'][5] += $decoration['value'];
    }
    else if ($row['decor_name'] == "Other")
    {
      $response['decorations']['datasets'][0]['data'][6] += $decoration['value'];
    }
    else if ($row['decor_name'] == "None")
    {
      $response['decorations']['datasets'][0]['data'][0] += $decoration['value'];
    }
  }

  // For each cake ID, get the cake details
  // from the database and add its popularity to
  // the response
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
      $response['cakes']['datasets'][0]['data'][0] += $cake['value'];
    }
    else if ($row['cake_size'] == "8\"")
    {
      $response['cakes']['datasets'][0]['data'][1] += $cake['value'];
    }
    else if ($row['cake_size'] == "10\"")
    {
      $response['cakes']['datasets'][0]['data'][2] += $cake['value'];
    }
    else if ($row['cake_size'] == "12\"")
    {
      $response['cakes']['datasets'][0]['data'][3] += $cake['value'];
    }
    else if ($row['cake_size'] == "14\"")
    {
      $response['cakes']['datasets'][0]['data'][4] += $cake['value'];
    }

    if ($row['cake_type'] == "Sponge")
    {
      $response['cakes']['datasets'][0]['data'][5] += $cake['value'];
    }
    else if ($row['cake_type'] == "Marble")
    {
      $response['cakes']['datasets'][0]['data'][6] += $cake['value'];
    }
    else if ($row['cake_type'] == "Chocolate")
    {
      $response['cakes']['datasets'][0]['data'][7] += $cake['value'];
    }
    else if ($row['cake_type'] == "Fruit")
    {
      $response['cakes']['datasets'][0]['data'][8] += $cake['value'];
    }
  }

  // For each month, add its popularity to the response
  for ($i = 0; $i < 12; $i++)
  {
    if ($months[$i]) 
    {
      $response['orders']['datasets'][0]['data'][$i] = $months[$i];
    } 
  }

  // Return the response in JSON format
  echo json_encode($response);
?>
