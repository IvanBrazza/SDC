<?php
  require("../common.php");
  $title = "Submit A Testimonial";
  
  if (!empty($_POST))
  {
    $query = "
      INSERT INTO testimonials (
        name,
        email,
        location,
        testimonial
      ) VALUES (
        :name,
        :email,
        :location,
        :testimonial
      )
    ";

    $query_params = array(
      ':name' => $_POST['name'],
      ':email' => $_POST['email'],
      ':location' => $_POST['location'],
      ':testimonial' => $_POST['testimonial']
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
  }
?>
<?php include("../header.php"); ?>
  <div class="form">
    <h1>Submit A Testimonial</h1>
    <form action="index.php" method="POST" data-validate="parsley">
      <label for="name">Name</label><br />
      <input type="text" name="name" id="name" data-required="true" data-trigger="change" data-error="Please enter your name"><br />
      <label for="email">Email</label><br />
      <input type="text" name="email" id="email" data-type="email" data-trigger="change" data-required="true" data-error="Please enter your email"><br />
      <label for="location">Location</label><br />
      <input type="text" name="location" id="location"><br />
      <label for="testimonial">Testimonial</label><br />
      <textarea name="testimonial" id="testimonial" rows="6" cols="40" data-trigger="change" data-required="true" data-error="Please enter your testimonial"></textarea><br /><br />
      <input type="submit" value="Submit Testimonial">
    </form>
  </div>
<?php include("../footer.php"); ?>
