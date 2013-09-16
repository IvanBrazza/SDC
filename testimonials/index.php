<?php
  require("../common.php");
  $title = "Testimonials";
  
  $query = "
    SELECT
      *
    FROM
      testimonials
  ";

  try
  {
    $stmt = $db->prepare($query);
    $result = $stmt->execute();
  }
  catch(PDOException $ex)
  {
    die("Failed to execute query: " . $ex->getMessage());
  }

  $rows = $stmt->fetchAll();
?>
<?php include("../header.php"); ?>
  <div class="container">
    <h1>Testimonials</h1>
    <?php foreach ($rows as $row) : ?>
      <p class="testimonial"><?php echo htmlentities($row['testimonial'], ENT_QUOTES, 'UTF-8'); ?></p>
      <small>-<b><?php echo htmlentities($row['name'], ENT_QUOTES, 'UTF-8'); ?></b><i><?php if (!empty($row['location'])) { echo ", "; echo htmlentities($row['location'], ENT_QUOTES, 'UTF-8'); } ?></i><?php if ($_SESSION and $_SESSION['user']['username'] === "admin") : ?><form action="deletetestimonial.php" method="POST" class="delete_testimonial"><input type="hidden" value="<?php echo $row['id']; ?>" name="id"><input type="submit" value="Delete" class="delete_testimonial_btn"></form><?php endif; ?></small>
    <?php endforeach ?>
    <br /><br />
    <a href="../submit-a-testimonial">Submit A Testimonial</a>
  </div>
<?php include("../footer.php");
