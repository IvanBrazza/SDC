  </div>
  <div id="footer">
    <hr class="fancy-line">
    <div class="copyright">
      <div>&copy; Star Dream Cakes 2013</div>
    </div>
  </div>
  <script src="../js/jquery/jquery-1.10.2.min.js"></script>
  <?php if ($_SERVER['REQUEST_URI'] === "/login/" or $_SERVER['REQUEST_URI'] === "/register/" or $_SERVER['REQUEST_URI'] === "/add-order/" or $_SERVER['REQUEST_URI'] === "/place-an-order/") : ?>
    <script src="../js/forms.js"></script>
    <script src="../js/jquery/jquery-ui.js"></script>
    <script src="../js/jquery/jquery-ui-timepicker-addon.js"></script>
  <?php endif; ?>
  <?php if ($_SERVER['REQUEST_URI'] === "/stats/") :?>
    <script src="../js/charts.js"></script>
  <?php endif; ?>
  <?php if ($_SERVER['REQUEST_URI'] === "/gallery/") : ?>
    <script src="../js/jquery/jquery.flexslider-min.js"></script>
    <script src="../js/gallery.js"></script>
  <?php endif; ?>
  <script src="../js/main.js"></script>
  <script src="../js/browser.js"></script>
<body>
