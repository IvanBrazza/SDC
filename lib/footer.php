    <div class="row footer">
      <div id="col-md-12">
        <div class="copyright">
          <div>&copy; Star Dream Cakes 2014</div>
        </div>
      </div>
    </div>
  </div>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="../js/bootstrap.js"></script>
  <script src="../js/jquery/jquery-ui.js"></script>
  <script src="../js/cookie.js"></script>
  <script src="../js/nprogress.js"></script>
  <script src="../js/table-pagination.js"></script>
  <?php if (strpos($_SERVER['REQUEST_URI'], "login") !== false or
            strpos($_SERVER['REQUEST_URI'], "register") !== false or
            strpos($_SERVER['REQUEST_URI'], "add-order") !== false or
            strpos($_SERVER['REQUEST_URI'], "place-an-order") !== false or
            strpos($_SERVER['REQUEST_URI'], "forgot-password") !== false or
            strpos($_SERVER['REQUEST_URI'], "edit-account") !== false) : ?>
    <script src="../js/forms.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "stats") !== false) :?>
    <script src="../js/chart.min.js"></script>
    <script src="../js/stats.js"></script>
    <script src="../js/modernizr.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "gallery") !== false) : ?>
    <script src="../js/jquery/jquery.wookmark.min.js"></script>
    <script src="../js/jquery/imagesloaded.min.js"></script>
    <script src="../js/lightbox.min.js"></script>
    <script src="../js/gallery.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "testimonials") !== false) : ?>
    <script src="../js/testimonials.js"></script>
    <script src="../js/forms.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "all-orders") !== false) : ?>
    <script src="../js/all-orders.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "customer-list") !== false) : ?>
    <script src="../js/customer-list.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "place-an-order") !== false) : ?>
    <script src="../js/place-an-order.js"></script>
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "add-order") !== false) : ?>
    <script src="../js/add-order.js"></script>
  <?php endif; ?>
  <script src="../js/main.js"></script>
  <script src="../js/browser.js"></script>
</body>
</html>
