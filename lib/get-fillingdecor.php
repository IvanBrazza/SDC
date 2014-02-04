<?php
  /**
    lib/getfilling-decor.php - a script which is called via
    AJAX which gets the details of a cake filling or decoration
    for when an order is being placed
  **/
  require("common.php");

  if ($_POST)
  {
    // If filling details are needed, get them,
    // else if decoration details are needed, get those
    if ($_POST['type'] == "filling")
    {
      $query = "
        SELECT
          filling_price, filling_name
        FROM
          fillings
        WHERE
          filling_id = :filling_id
      ";

      $query_params = array(
        ':filling_id' => $_POST['id']
      );
    }
    else if ($_POST['type'] == "decor")
    {
      $query = "
        SELECT
          decor_price, decor_name
        FROM
          decorations
        WHERE
          decor_id = :decor_id
      ";

      $query_params = array(
        ':decor_id' => $_POST['id']
      );
    }

    $db->runQuery($query, $query_params);

    $row = $db->fetch();

    // Set the response according to filling
    // or decoration
    if ($_POST['type'] == "filling")
    {
      $response = array(
        "name"  => $row['filling_name'],
        "price" => $row['filling_price']
      );
    }
    else if ($_POST['type'] == "decor")
    {
      $response = array(
        "name"  => $row['decor_name'],
        "price" => $row['decor_price']
      );
    }

    // Return the details in JSON format
    echo json_encode($response);
  }
?>
