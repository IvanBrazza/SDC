<?php
  /**
    testimonials/ - display a list of all the testimonials
    in the db and allow the admin to delete them.
  **/
  require("../lib/common.php");
  $title = "Testimonials";
  $page = "testimonials";
  
  // Generate a token
  $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

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
  <div class="error">
    <span class="error_message" id="error_message"></span>
  </div>
  <div id="testimonials">
    <?php foreach ($rows as $row) : ?>
      <div>
        <p class="testimonial"><?php echo htmlentities($row['testimonial'], ENT_QUOTES, 'UTF-8'); ?></p>
        <span class="testimonial-name">
          <small>-<?php echo htmlentities($row['name'], ENT_QUOTES, 'UTF-8'); ?>
            <i><?php if (!empty($row['location'])) { echo ", "; echo htmlentities($row['location'], ENT_QUOTES, 'UTF-8'); } ?></i>
            <?php if ($_SESSION['user'] and $_SESSION['user']['username'] === "admin") : ?>
              <form action="../lib/delete-testimonial.php" method="POST" id="delete_testimonial" class="delete_testimonial">
                <input type="hidden" value="<?php echo $row['id']; ?>" name="id">
                <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
                <input type="submit" value="Delete" class="delete_testimonial_btn">
              </form>
            <?php endif; ?>
          </small>
        </span>
      </div>
    <?php endforeach ?>
  </div>
  <br /><br />
  <a href="javascript:" id="submit-testimonial">Submit A Testimonial</a>
  <div id="submit-testimonial-form" class="form">
    <script type="text/javascript">
      var RecaptchaOptions = {
        theme : 'clean'
      };
    </script>
    <form action="index.php" method="POST" id="testimonial-form">
      <div>
        <label for="name">Name</label>
        <input type="text" name="name" id="name" onchange="validateInput('#name', '#name_error')">
      </div>
      <div id="name_error" class="validate-error"></div>
      <div>
        <label for="email">Email</label>
        <input type="text" name="email" id="email" onchange="validateEmail()">
      </div>
      <div id="email-error" class="validate-error"></div>
      <div>
        <label for="location">Location</label>
        <input type="text" name="location" id="location">
      </div>
      <div>
        <label for="testimonial">Testimonial</label>
        <textarea name="testimonial" id="testimonial" rows="6" cols="40" onchange="validateInput('textarea#testimonial', '#testimonial_error')"></textarea>
      </div>
      <div id="testimonial_error" class="validate-error"></div>
      <div class="error">
        <span class="error_message">
          <?php echo $display_message; ?>
        </span>
      </div>
      <?php
        require_once("../lib/recaptchalib.php");
        $publickey = "6LePfucSAAAAAKlUO3GQKgfXCd7SvIhtFjBH5F9Z";
        echo recaptcha_get_html($publickey, null, true);
      ?>
      <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token" id="token">
      <input type="submit" id="submit-testimonial" value="Submit Testimonial" name="submit">
      <span class="ajax-load"></span>
    </form>
  </div>
<?php include("../lib/footer.php");
