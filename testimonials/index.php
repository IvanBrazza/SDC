<?php
  /**
    testimonials/ - display a list of all the testimonials
    in the db and allow the admin to delete them.
  **/
  require("../lib/common.php");
  $title = "Testimonials";
  $page = "testimonials";

  // Use HTTPS since the form to submit a testimonial
  // is on this page
  forceHTTPS();

  // Generate a token
  $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

  // Get all testimonials to display them
  $query = "
    SELECT
      *
    FROM
      testimonials
  ";

  $db->runQuery($query, null);

  $rows = $db->fetchAll();
?>
<?php include("../lib/header.php"); ?>
  <h1>Testimonials</h1>
  <div id="testimonials">
    <?php foreach ($rows as $row) : ?>
      <?php if ($row['approved'] == 1) : ?>
        <div>
          <p class="testimonial approved"><?php echo htmlentities($row['testimonial'], ENT_QUOTES, 'UTF-8'); ?></p>
          <div class="downarrow approved"></div>
          <span class="testimonial-name">
            <small>- <?php echo htmlentities($row['name'], ENT_QUOTES, 'UTF-8'); ?><i><?php if (!empty($row['location'])) { echo ", "; echo htmlentities($row['location'], ENT_QUOTES, 'UTF-8'); } ?></i>
              <?php if ($_SESSION['user'] and $_SESSION['user']['username'] === "admin") : ?>
                <a href="javascript:" data-id="<?php echo $row['id']; ?>" data-token="<?php echo $_SESSION['token']; ?>" class="delete_testimonial">Delete</a>
              <?php endif; ?>
            </small>
          </span>
        </div>
      <?php endif; ?>
    <?php endforeach ?>
    <?php foreach ($rows as $row) : ?>
      <?php if ($row['approved'] == 0 and $_SESSION['user']['username'] == "admin") : ?>
        <div>
          <p class="testimonial unapproved"><?php echo htmlentities($row['testimonial'], ENT_QUOTES, 'UTF-8'); ?></p>
          <div class="downarrow unapproved"></div>
          <span class="testimonial-name">
            <small>- <?php echo htmlentities($row['name'], ENT_QUOTES, 'UTF-8'); ?><i><?php if (!empty($row['location'])) { echo ", "; echo htmlentities($row['location'], ENT_QUOTES, 'UTF-8'); } ?></i>
              <span id="unapproved"><i> (unapproved)</i></span>
              <a href="javascript:" data-id="<?php echo $row['id']; ?>" data-token="<?php echo $_SESSION['token']; ?>" class="delete_testimonial">Delete</a>
              <a href="javascript:" data-id="<?php echo $row['id']; ?>" data-token="<?php echo $_SESSION['token']; ?>" class="approve_testimonial">Approve</a>
            </small>
          </span>
        </div>
      <?php endif; ?>
    <?php endforeach ?>
  </div>
  <br /><br />
  <a href="javascript:" name="submit" id="submit-testimonial">Submit A Testimonial</a>
  <div class="error">
    <span class="error_message" id="error_message"></span>
  </div>
  <div id="submit-testimonial-form" class="form">
    <script type="text/javascript">
      var RecaptchaOptions = {
        theme : 'clean'
      };
    </script>
    <form action="index.php" method="POST" id="testimonial-form">
      <div>
        <label for="name">Name</label>
        <input type="text" name="name" id="name" onchange="validate.input('#name', '#name_error')">
      </div>
      <div id="name_error" class="validate-error"></div>
      <div>
        <label for="email">Email</label>
        <input type="text" name="email" id="email" onchange="validate.email()">
      </div>
      <div id="email-error" class="validate-error"></div>
      <div>
        <label for="location">Location</label>
        <input type="text" name="location" id="location">
      </div>
      <div>
        <label for="testimonial">Testimonial</label>
        <textarea name="testimonial" id="testimonial" rows="6" cols="40" onchange="validate.input('textarea#testimonial', '#testimonial_error')"></textarea>
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
    </form>
  </div>
<?php include("../lib/footer.php");
