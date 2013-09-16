<?php
  require("common.php");

  if ($_POST)
  {
    $query = "
      DELETE FROM
        testimonials
      WHERE
        id = :id
    ";

    $query_params = array(
      ':id' => $_POST['id']
    );

    try
    {
      $stmt = $db->prepare($query);
      $result = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getMessage());
    }

    header("Location: testimonials.php");
    die();
  }
  else
  {
    die("Error deleting testimonal");
  }

?>
