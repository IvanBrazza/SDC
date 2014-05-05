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
  <div class="row">
    <div class="col-md-12">
      <h1>Testimonials</h1>
    </div>
  </div>
  <div id="testimonials">
    <?php for ($i = 0; $i < count($rows); $i++) : ?>
      <div class="row">
        <?php if ($rows[$i]['approved'] == 1) : ?>
          <div class="col-md-6 testimonial-col">
            <p class="testimonial approved">
              <?php echo htmlentities($rows[$i]['testimonial'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <div class="downarrow approved"></div>
            <span class="testimonial-name">
              <small>- <?php echo htmlentities($rows[$i]['name'], ENT_QUOTES, 'UTF-8'); ?><i><?php if (!empty($rows[$i]['location'])) { echo ", "; echo htmlentities($rows[$i]['location'], ENT_QUOTES, 'UTF-8'); } ?></i>
                <?php if ($_SESSION['user'] and $_SESSION['user']['username'] === "admin") : ?>
                  <button data-id="<?php echo $rows[$i]['id']; ?>" data-token="<?php echo $_SESSION['token']; ?>" class="btn btn-danger btn-xs pull-right delete_testimonial"><span class="glyphicon glyphicon-remove"></span>  Delete</button>
                <?php endif; ?>
              </small>
            </span>
          </div>
        <?php elseif ($rows[$i]['approved'] == 0 and $_SESSION['user']['username'] === "admin") : ?>
          <div class="col-md-6 testimonial-col">
            <p class="testimonial unapproved">
              <?php echo htmlentities($rows[$i]['testimonial'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <div class="downarrow unapproved"></div>
            <span class="testimonial-name">
              <small>- <?php echo htmlentities($rows[$i]['name'], ENT_QUOTES, 'UTF-8'); ?><i><?php if (!empty($rows[$i]['location'])) { echo ", "; echo htmlentities($rows[$i]['location'], ENT_QUOTES, 'UTF-8'); } ?></i>
                <?php if ($_SESSION['user'] and $_SESSION['user']['username'] === "admin") : ?>
                  <span id="unapproved"><i> (unapproved)</i></span>
                  <button data-id="<?php echo $rows[$i]['id']; ?>" data-token="<?php echo $_SESSION['token']; ?>" class="btn btn-success btn-xs pull-right approve_testimonial"><span class="glyphicon glyphicon-ok"></span>  Approve</button>
                  <button data-id="<?php echo $rows[$i]['id']; ?>" data-token="<?php echo $_SESSION['token']; ?>" class="btn btn-danger btn-xs pull-right delete_testimonial"><span class="glyphicon glyphicon-remove"></span>  Delete</button>
                <?php endif; ?>
              </small>
            </span>
          </div>
        <?php endif; ?>
        <?php $i++; ?>
        <?php if ($rows[$i]) : ?>
          <?php if ($rows[$i]['approved'] == 1) : ?>
            <div class="col-md-6 testimonial-col">
              <p class="testimonial approved">
                <?php echo htmlentities($rows[$i]['testimonial'], ENT_QUOTES, 'UTF-8'); ?>
              </p>
              <div class="downarrow approved"></div>
              <span class="testimonial-name">
                <small>- <?php echo htmlentities($rows[$i]['name'], ENT_QUOTES, 'UTF-8'); ?><i><?php if (!empty($rows[$i]['location'])) { echo ", "; echo htmlentities($rows[$i]['location'], ENT_QUOTES, 'UTF-8'); } ?></i>
                  <?php if ($_SESSION['user'] and $_SESSION['user']['username'] === "admin") : ?>
                    <button data-id="<?php echo $rows[$i]['id']; ?>" data-token="<?php echo $_SESSION['token']; ?>" class="btn btn-danger btn-xs pull-right delete_testimonial"><span class="glyphicon glyphicon-remove"></span>  Delete</button>
                  <?php endif; ?>
                </small>
              </span>
            </div>
          <?php elseif ($rows[$i]['approved'] == 0 and $_SESSION['user']['username'] === "admin") : ?>
            <div class="col-md-6 testimonial-col">
              <p class="testimonial unapproved">
                <?php echo htmlentities($rows[$i]['testimonial'], ENT_QUOTES, 'UTF-8'); ?>
              </p>
              <div class="downarrow unapproved"></div>
              <span class="testimonial-name">
                <small>- <?php echo htmlentities($rows[$i]['name'], ENT_QUOTES, 'UTF-8'); ?><i><?php if (!empty($rows[$i]['location'])) { echo ", "; echo htmlentities($rows[$i]['location'], ENT_QUOTES, 'UTF-8'); } ?></i>
                  <?php if ($_SESSION['user'] and $_SESSION['user']['username'] === "admin") : ?>
                    <span id="unapproved"><i> (unapproved)</i></span>
                    <button data-id="<?php echo $rows[$i]['id']; ?>" data-token="<?php echo $_SESSION['token']; ?>" class="btn btn-success btn-xs pull-right approve_testimonial"><span class="glyphicon glyphicon-ok"></span>  Approve</button>
                    <button data-id="<?php echo $rows[$i]['id']; ?>" data-token="<?php echo $_SESSION['token']; ?>" class="btn btn-danger btn-xs pull-right delete_testimonial"><span class="glyphicon glyphicon-remove"></span>  Delete</button>
                  <?php endif; ?>
                </small>
              </span>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    <?php endfor; ?>
  </div>
  <br /><br />
  <a href="#submit" name="submit" id="submit-testimonial">Submit A Testimonial</a>
  <div class="error">
    <span class="error_message" id="error_message"></span>
  </div>
  <div id="submit-testimonial-form" class="row">
    <div class="col-md-6">
      <form action="index.php" method="POST" id="testimonial-form" role="form">
        <div class="form-group">
          <label for="name" class="col-sm-4 control-label">Name</label>
          <div class="col-sm-8">
            <div class="input-group">
              <input type="text" class="form-control" name="name" id="name" onchange="validate.input('#name', '#name_error', 'Please enter your name')">
              <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
            </div>
            <div id="name_error" class="validate-error"></div>
          </div>
        </div>
        <div class="form-group">
          <label for="email" class="col-sm-4 control-label">Email</label>
          <div class="col-sm-8">
            <div class="input-group">
              <input type="email" class="form-control" name="email" id="email" onchange="validate.email()">
              <span class="input-group-addon">@</span>
            </div>
            <div id="email-error" class="validate-error"></div>
          </div>
        </div>
        <div class="form-group">
          <label for="location" class="col-sm-4 control-label">Location</label>
          <div class="col-sm-8">
            <div class="input-group">
              <input type="text" class="form-control" name="location" id="location">
              <span class="input-group-addon"><span class="glyphicon glyphicon-home"></span></span>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label for="testimonial" class="col-sm-4 control-label">Testimonial</label>
          <div class="col-sm-8">
            <textarea class="form-control" name="testimonial" id="testimonial" rows="6" cols="40" onchange="validate.input('textarea#testimonial', '#testimonial_error', 'Please enter your testimonial')"></textarea>
            <div id="testimonial_error" class="validate-error"></div>
          </div>
        </div>
        <div class="error">
          <span class="error_message">
            <?php echo $display_message; ?>
          </span>
        </div>
        <script type="text/javascript">
          var RecaptchaOptions = {
            theme : 'custom',
            custom_theme_widget: 'recaptcha_widget'
          };
        </script>
        <div class="form-group">
          <label class="col-sm-4 control-label">reCAPTCHA</label>
          <div id="recaptcha_widget" style="display:none;margin-left:15px;" class="recaptcha_widget col-sm-8">
            <div id="recaptcha_image"></div>
            <div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect. Please try again.</div>
            <div class="recaptcha_input">
              <label class="recaptcha_only_if_image" for="recaptcha_response_field">Enter the words above:</label>
              <label class="recaptcha_only_if_audio" for="recaptcha_response_field">Enter the numbers you hear:</label>
              <input type="text" id="recaptcha_response_field" name="recaptcha_response_field">
            </div>
            <ul class="recaptcha_options">
              <li>
                <a href="javascript:Recaptcha.reload()">
                  <i class="glyphicon glyphicon-refresh"></i>
                  <span class="captcha_hide">Get another CAPTCHA</span>
                </a>
              </li>
              <li class="recaptcha_only_if_image">
                <a href="javascript:Recaptcha.switch_type('audio')">
                  <i class="glyphicon glyphicon-volume-up"></i><span class="captcha_hide"> Get an audio CAPTCHA</span>
                </a>
              </li>
              <li class="recaptcha_only_if_audio">
                <a href="javascript:Recaptcha.switch_type('image')">
                  <i class="glyphicon glyphicon-picture"></i><span class="captcha_hide"> Get an image CAPTCHA</span>
                </a>
              </li>
              <li>
                <a href="javascript:Recaptcha.showhelp()">
                  <i class="glyphicon glyphicon-question-sign"></i><span class="captcha_hide"> Help</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
        <script type="text/javascript" src="//www.google.com/recaptcha/api/challenge?k=6LePfucSAAAAAKlUO3GQKgfXCd7SvIhtFjBH5F9Z"></script>
        <noscript>
          <iframe src="//www.google.com/recaptcha/api/noscript?k=6LePfucSAAAAAKlUO3GQKgfXCd7SvIhtFjBH5F9Z" height="300" width="500" frameborder="0"></iframe><br>
          <textarea name="recaptcha_challenge_field"></textarea>
          <input type="hidden" name="recaptcha_response_field" value="manual_challenge">
        </noscript>
        <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token" id="token">
        <button type="submit" class="btn btn-default">Submit Testimonial</button>
      </form>
    </div>
    <div class="col-md-6"></div>
  </div>
<?php include("../lib/footer.php");
