<?php
  /**
    delete-testimonial.php - called by testimonials to delete a testimonial
    from a POST request.
  **/
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
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getMessage());
    }

    echo "success";
    die();
  }
  else
  {
    die("Error deleting testimonal");
  }

?>
