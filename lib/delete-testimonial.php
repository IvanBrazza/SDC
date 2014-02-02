<?php
  /**
    delete-testimonial.php - called by testimonials to delete a testimonial
    from a POST request.
  **/
  require("common.php");

  if ($_POST)
  {
    if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token']))
    {
      echo "Invalid token.";
      die();
    }

    // Unset the token
    unset($_SESSION['token']);

    $query = "
      DELETE FROM
        testimonials
      WHERE
        id = :id
    ";

    $query_params = array(
      ':id' => $_POST['id']
    );

    $db->runQuery($query, $query_params);

    echo "success";
    die();
  }
  else
  {
    echo "Oops! Something went wrong. Try again.";
    die("Error deleting testimonal");
  }

?>
