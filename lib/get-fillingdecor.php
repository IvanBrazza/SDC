<?php
  require("common.php");

  if ($_POST)
  {
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

    try
    {
      $stmt = $db->prepare($query);
      $result = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getMessage . " query: " . $query);
    }

    $row = $stmt->fetch();

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

    echo json_encode($response);
  }
?>
