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
<div class="col-md-12">
  <h1>Testimonials</h1>
  <div class="container" id="testimonials">
    <?php for ($i = 0; $i < count($rows); $i++) : ?>
      <div class="row">
        <div class="col-md-6">
          <p class="testimonial <?php if ($rows[$i]['approved'] == 0) : ?>unapproved<?php else : ?>approved<?php endif; ?>">
            <?php echo htmlentities($rows[$i]['testimonial'], ENT_QUOTES, 'UTF-8'); ?>
          </p>
          <div class="downarrow <?php if ($rows[$i]['approved'] == 0) : ?>unapproved<?php else : ?>approved<?php endif; ?>"></div>
          <span class="testimonial-name">
            <small>- <?php echo htmlentities($rows[$i]['name'], ENT_QUOTES, 'UTF-8'); ?><i><?php if (!empty($rows[$i]['location'])) { echo ", "; echo htmlentities($rows[$i]['location'], ENT_QUOTES, 'UTF-8'); } ?></i>
              <?php if ($_SESSION['user'] and $_SESSION['user']['username'] === "admin") : ?>
                <?php if ($rows[$i]['approved'] == 0) : ?>
                  <span id="unapproved"><i> (unapproved)</i></span>
                  <a href="javascript:" data-id="<?php echo $rows[$i]['id']; ?>" data-token="<?php echo $_SESSION['token']; ?>" class="approve_testimonial">Approve</a>
                <?php endif; ?>
                <a href="javascript:" data-id="<?php echo $rows[$i]['id']; ?>" data-token="<?php echo $_SESSION['token']; ?>" class="delete_testimonial">Delete</a>
              <?php endif; ?>
            </small>
          </span>
        </div>
        <?php $i++; ?>
        <?php if ($rows[$i]) : ?>
          <div class="col-md-6">
            <p class="testimonial <?php if ($rows[$i]['approved'] == 0) : ?>unapproved<?php else : ?>approved<?php endif; ?>"><?php echo htmlentities($rows[$i]['testimonial'], ENT_QUOTES, 'UTF-8'); ?></p>
            <div class="downarrow <?php if ($rows[$i]['approved'] == 0) : ?>unapproved<?php else : ?>approved<?php endif; ?>"></div>
            <span class="testimonial-name">
              <small>- <?php echo htmlentities($rows[$i]['name'], ENT_QUOTES, 'UTF-8'); ?><i><?php if (!empty($rows[$i]['location'])) { echo ", "; echo htmlentities($rows[$i]['location'], ENT_QUOTES, 'UTF-8'); } ?></i>
                <?php if ($_SESSION['user'] and $_SESSION['user']['username'] === "admin") : ?>
                  <?php if ($rows[$i]['approved'] == 0) : ?>
                    <span id="unapproved"><i> (unapproved)</i></span>
                    <a href="javascript:" data-id="<?php echo $rows[$i]['id']; ?>" data-token="<?php echo $_SESSION['token']; ?>" class="approve_testimonial">Approve</a>
                  <?php endif; ?>
                  <a href="javascript:" data-id="<?php echo $rows[$i]['id']; ?>" data-token="<?php echo $_SESSION['token']; ?>" class="delete_testimonial">Delete</a>
                 <?php endif; ?>
              </small>
            </span>
          </div>
        <?php endif; ?>
      </div>
    <?php endfor; ?>
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
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <form action="index.php" method="POST" id="testimonial-form" role="form">
            <div class="form-group">
              <label for="name" class="col-sm-4 control-label">Name</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="name" id="name" onchange="validate.input('#name', '#name_error')">
                <div id="name_error" class="validate-error"></div>
              </div>
            </div>
            <div class="form-group">
              <label for="email" class="col-sm-4 control-label">Email</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="email" id="email" onchange="validate.email()">
                <div id="email-error" class="validate-error"></div>
              </div>
            </div>
            <div class="form-group">
              <label for="location" class="col-sm-4 control-label">Location</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="location" id="location">
              </div>
            </div>
            <div class="form-group">
              <label for="testimonial" class="col-sm-4 control-label">Testimonial</label>
              <div class="col-sm-8">
                <textarea class="form-control" name="testimonial" id="testimonial" rows="6" cols="40" onchange="validate.input('textarea#testimonial', '#testimonial_error')"></textarea>
                <div id="testimonial_error" class="validate-error"></div>
              </div>
            </div>
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
        <div class="col-md-6"></div>
      </div>
    </div>
  </div>
</div>
<?php include("../lib/footer.php");
