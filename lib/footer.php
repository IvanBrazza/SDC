  </div>
  <div id="footer">
    <hr class="fancy-line">
    <div class="copyright">
      <div>&copy; Star Dream Cakes 2013</div>
    </div>
  </div>
  <div id="loading-spinner-dialog">
    <div class="ajax-load"></div>
  </div>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
  <script src="../js/table-pagination.js"></script>
  <?php if (strpos($_SERVER['REQUEST_URI'], "login") !== false or
            strpos($_SERVER['REQUEST_URI'], "register") !== false or
            strpos($_SERVER['REQUEST_URI'], "add-order") !== false or
            strpos($_SERVER['REQUEST_URI'], "place-an-order") !== false or
            strpos($_SERVER['REQUEST_URI'], "edit-account") !== false) : ?>
    <script src="../js/forms.js"></script>
    <script src="../js/jquery/jquery-ui-timepicker-addon.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "stats") !== false) :?>
    <script src="../js/stats.js"></script>
    <script src="../js/modernizr.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "gallery") !== false) : ?>
    <script src="../js/jquery/jquery.flexslider-min.js"></script>
    <script src="../js/gallery.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "place-an-order") !== false or
            strpos($_SERVER['REQUEST_URI'], "add-order") !== false) : ?>
    <script src="../js/tabbed-order.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "testimonials") !== false) : ?>
    <script src="../js/testimonials.js"></script>
    <script src="../js/forms.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "all-orders") !== false) : ?>
    <script src="../js/all-orders.js"></script>
  <?php endif; ?>
  <script src="../js/main.js"></script>
  <script src="../js/browser.js"></script>
<body>
