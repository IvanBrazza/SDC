<?php
  /**
    delete-testimonial.php - called by testimonials to delete a testimonial
    from a POST request.
  **/
  require("common.php");

  if ($_POST) {
    // If the token is invalid, die
    if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token'])) {
      echo "Invalid token.";
      die();
    }

    // Unset the token
    unset($_SESSION['token']);

    // Delete the testimonial based on its ID
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

    $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

    $response = array(
      "response" => "success",
      "token" => $_SESSION['token']
    );
    echo json_encode($response);;
    die();
  }
  else {
    echo "Oops! Something went wrong. Try again.";
    die("Error deleting testimonal");
  }

?>
