  </div>
  <div id="footer">
    <hr class="fancy-line">
    <div class="copyright">
      <div>&copy; Star Dream Cakes 2013</div>
    </div>
  </div>
  <script src="../js/jquery/jquery-1.10.2.min.js"></script>
  <?php if (strpos($_SERVER['REQUEST_URI'], "login") !== false or
            strpos($_SERVER['REQUEST_URI'], "register") !== false or
            strpos($_SERVER['REQUEST_URI'], "add-order") !== false or
            strpos($_SERVER['REQUEST_URI'], "place-an-order") !== false) : ?>
    <script src="../js/forms.js"></script>
    <script src="../js/jquery/jquery-ui.js"></script>
    <script src="../js/jquery/jquery-ui-timepicker-addon.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "stats") !== false) :?>
    <script src="../js/charts.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "gallery") !== false) : ?>
    <script src="../js/jquery/jquery.flexslider-min.js"></script>
    <script src="../js/gallery.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "place-an-order") !== false) : ?>
    <script src="../js/place-an-order.js"></script>
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
