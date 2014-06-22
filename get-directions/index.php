<?php
  include("../lib/common.php");
  $title = "Get Directions";
  $page = "all-orders";

  // Only logged in users can access this page
  if (empty($_SESSION['user'])) {
    header("Location: ../login");
    die();
  }

  // Create a new delivery object to be
  // used for distance calculation
  include "../lib/delivery.class.php";
  $delivery = new Delivery();

  // If GET, then it means directions are to
  // be displayed for the admin to a customers
  // house. So get their details and calculate
  // the distance, otherwise directions are to
  // be displayed for the customer to the collection
  // point
  if ($_GET)
  {
    $query = "
      SELECT
        first_name,
        last_name,
        address1,
        address2,
        county,
        city,
        postcode
      FROM
        users
      WHERE
        customer_id = :customer_id
    ";

    $query_params = array(
      ':customer_id' => $_GET['id']
    );

    $db->runQuery($query, $query_params);

    $row = $db->fetch();
    
    $delivery->setAddress($row['address1'], $row['address2'], $row['city']);
    $delivery->setPostcode($row['postcode']);
    $delivery->calculateDistance();
  }
  else
  {
    $delivery->setAddress($_SESSION['user']['address1'], $_SESSION['user']['address2'], $_SESSION['user']['city']);
    $delivery->setPostcode($_SESSION['user']['postcode']);
    $delivery->calculateDistance();
  }
?>
<?php include("../lib/header.php"); ?>
<div class="row">
  <div class="col-md-12">
    <h1>Get Directions</h1>
    <p>Directions to:</p>
    <address>
      <?php if ($_GET) : ?>
        <strong><?php echo htmlentities($row['first_name'], ENT_QUOTES, 'UTF-8'); echo " "; echo htmlentities($row['last_name'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
        <?php echo htmlentities($row['address1'], ENT_QUOTES, 'UTF-8'); ?><br>
        <?php if ($row['address2']) : ?>
          <?php echo htmlentities($row['address2'], ENT_QUOTES, 'UTF-8'); ?><br>
        <?php endif; ?>
        <?php echo htmlentities($row['county'], ENT_QUOTES, 'UTF-8') . ", " . htmlentities($row['city'], ENT_QUOTES, 'UTF-8'); ?><br>
        <?php echo htmlentities($row['postcode'], ENT_QUOTES, 'UTF-8'); ?><br>
      <?php else : ?>
        95 Hoe Lane<br>
        EN3 5SW<br>
      <?php endif; ?>
      <i>(<?php echo $delivery->getDistance(); ?> miles away)</i>
    </address>
  </div>
</div>
<div class="row" style="padding-bottom:50px">
  <div class="col-md-8">
    <div id="map-canvas"></div>
  </div>
  <div class="col-md-4">
    <div id="directions-panel"></div>
  </div>
</div>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCKeZpb8doUO3DbEqT3t-uRJYsbEPbD3AE&sensor=false"></script>
<script>
  var directionsDisplay;
  var directionsService = new google.maps.DirectionsService();
  var map;
  <?php if ($_GET) : ?>
    var origin = "95+Hoe+Lane,EN35SW";
    var destination = <?php echo json_encode(str_replace(" ", "+", $row['address1']) . "," . str_replace(" ", "+", $row['city']) . "," . str_replace(" ", "+", $row['postcode'])); ?>;
  <?php else : ?>
    var origin = <?php echo json_encode(str_replace(" ", "+", $_SESSION['user']['address1']) . "," . str_replace(" ", "+", $_SESSION['user']['city']) . "," . str_replace(" ", "+", $_SESSION['user']['postcode'])); ?>;
    var destination = "95+Hoe+Lane,EN35SW";
  <?php endif; ?>
  var center = new google.maps.LatLng(51.666394, -0.048700);

  directionsDisplay = new google.maps.DirectionsRenderer();
  var mapOptions = {
    zoom: 32,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    center: center
    }
  map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
  directionsDisplay.setMap(map);
  directionsDisplay.setPanel(document.getElementById("directions-panel"));

  var request = {
      origin: origin,
      destination: destination,
      travelMode: google.maps.TravelMode.DRIVING,
      unitSystem: google.maps.UnitSystem.IMPERIAL
  };
  directionsService.route(request, function(response, status) {
    if (status == google.maps.DirectionsStatus.OK) {
      directionsDisplay.setDirections(response);
    }
  });
</script>
<?php include("../lib/footer.php"); ?>
