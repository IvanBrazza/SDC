<?php
  /**
    testimonials/ - display a list of all the testimonials
    in the db and allow the admin to delete them.
  **/
  require("../lib/common.php");
  $title = "Testimonials";
  $page = "testimonials";
  
  $query = "
    SELECT
      *
    FROM
      testimonials
  ";

  try
  {
    $stmt     = $db->prepare($query);
    $result   = $stmt->execute();
  }
  catch(PDOException $ex)
  {
    die("Failed to execute query: " . $ex->getMessage());
  }

  $rows = $stmt->fetchAll();
?>
<?php include("../lib/header.php"); ?>
  <h1>Testimonials</h1>
  <?php foreach ($rows as $row) : ?>
    <div>
      <p class="testimonial"><?php echo htmlentities($row['testimonial'], ENT_QUOTES, 'UTF-8'); ?></p>
      <span class="testimonial-name">
        <small>-<?php echo htmlentities($row['name'], ENT_QUOTES, 'UTF-8'); ?>
          <i><?php if (!empty($row['location'])) { echo ", "; echo htmlentities($row['location'], ENT_QUOTES, 'UTF-8'); } ?></i>
          <?php if ($_SESSION and $_SESSION['user']['username'] === "admin") : ?>
            <form action="../lib/delete-testimonial.php" method="POST" id="delete_testimonial" class="delete_testimonial">
              <input type="hidden" value="<?php echo $row['id']; ?>" name="id">
              <input type="submit" value="Delete" class="delete_testimonial_btn">
            </form>
          <?php endif; ?>
        </small>
      </span>
    </div>
  <?php endforeach ?>
  <br /><br />
  <a href="../submit-a-testimonial">Submit A Testimonial</a>
<?php include("../lib/footer.php");
