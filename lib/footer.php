    <div class="row footer">
      <div id="col-md-12">
        <div class="copyright">
          <div>&copy; Star Dream Cakes 2014</div>
        </div>
      </div>
    </div>
  </div>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="//www.<?php echo $siteUrl; ?>/js/bootstrap.js"></script>
  <script src="//www.<?php echo $siteUrl; ?>/js/jquery/jquery-ui.js"></script>
  <script src="//www.<?php echo $siteUrl; ?>/js/cookie.js"></script>
  <script src="//www.<?php echo $siteUrl; ?>/js/nprogress.js"></script>
  <script src="//www.<?php echo $siteUrl; ?>/js/table-pagination.js"></script>
  <?php if (strpos($_SERVER['REQUEST_URI'], "login") !== false or
            strpos($_SERVER['REQUEST_URI'], "register") !== false or
            strpos($_SERVER['REQUEST_URI'], "add-order") !== false or
            strpos($_SERVER['REQUEST_URI'], "place-an-order") !== false or
            strpos($_SERVER['REQUEST_URI'], "forgot-password") !== false or
            strpos($_SERVER['REQUEST_URI'], "edit-account") !== false) : ?>
    <script src="//www.<?php echo $siteUrl; ?>/js/forms.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "gallery") !== false) : ?>
    <script src="//www.<?php echo $siteUrl; ?>/js/jquery/jquery.wookmark.min.js"></script>
    <script src="//www.<?php echo $siteUrl; ?>/js/jquery/imagesloaded.min.js"></script>
    <script src="//www.<?php echo $siteUrl; ?>/js/lightbox.min.js"></script>
    <script src="//www.<?php echo $siteUrl; ?>/js/gallery.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "testimonials") !== false) : ?>
    <script src="//www.<?php echo $siteUrl; ?>/js/testimonials.js"></script>
    <script src="//www.<?php echo $siteUrl; ?>/js/forms.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "all-orders") !== false) : ?>
    <script src="//www.<?php echo $siteUrl; ?>/js/all-orders.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "place-an-order") !== false) : ?>
    <script src="//www.<?php echo $siteUrl; ?>/js/jquery/jquery.fileupload.min.js"></script>
    <script src="//www.<?php echo $siteUrl; ?>/js/place-an-order.js"></script>
    <script src="//www.<?php echo $siteUrl; ?>/js/picker.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "add-order") !== false) : ?>
    <script src="//www.<?php echo $siteUrl; ?>/js/add-order.js"></script>
    <script src="//www.<?php echo $siteUrl; ?>/js/forms.js"></script>
    <script src="//www.<?php echo $siteUrl; ?>/js/picker.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "edit-order") !== false) : ?>
    <script src="//www.<?php echo $siteUrl; ?>/js/edit-order.js"></script>
    <script src="//www.<?php echo $siteUrl; ?>/js/forms.js"></script>
    <script src="//www.<?php echo $siteUrl; ?>/js/picker.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "admin") !== false) : ?>
    <script src="//www.<?php echo $siteUrl; ?>/js/jquery/jquery.fileupload.min.js"></script>
    <script src="//www.<?php echo $siteUrl; ?>/js/chart.min.js"></script>
    <script src="//www.<?php echo $siteUrl; ?>/js/modernizr.js"></script>
    <script src="//www.<?php echo $siteUrl; ?>/js/jquery/jquery.unveil.min.js"></script>
    <script src="//www.<?php echo $siteUrl; ?>/js/stats.js"></script>
    <script src="//www.<?php echo $siteUrl; ?>/js/admin.js"></script>
  <?php endif; ?>
  <script src="//www.<?php echo $siteUrl; ?>/js/main.js"></script>
  <script src="//www.<?php echo $siteUrl; ?>/js/browser.js"></script>
</body>
</html>
