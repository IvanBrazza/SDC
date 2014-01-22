<?php
  include("../lib/common.php");
  $title = "Get Directions";
  $page = "all-orders";

  if (empty($_SESSION['user'])) {
    header("Location: ../login");
    die();
  }

  include "../lib/delivery.php";
  $delivery = new Delivery();

  if ($_GET)
  {
    $query = "
      SELECT
        first_name,
        last_name,
        address,
        postcode
      FROM
        users
      WHERE
        customer_id = :customer_id
    ";

    $query_params = array(
      ':customer_id' => $_GET['id']
    );

    try
    {
      $stmt   = $db->prepare($query);
      $result = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getmessage() . " query: " . $query);
    }

    $row = $stmt->fetch();
    
    $delivery->setAddress($row['address']);
    $delivery->setPostcode($row['postcode']);
    $delivery->calculateDistance();
  }
  else
  {
    $delivery->setAddress($_SESSION['user']['address']);
    $delivery->setPostcode($_SESSION['user']['postcode']);
    $delivery->calculateDistance();
  }
?>
<?php include("../lib/header.php"); ?>
  <h1>Get Directions</h1>
  <p>Directions to:</p>
  <?php if ($_GET) : ?>
    <p><?php echo $row['first_name'] . " " . $row['last_name']; ?><br />
    <?php echo $row['address']; ?><br />
    <?php echo $row['postcode']; ?><br />
  <?php else : ?>
    <p>95 Hoe Lane<br />
    EN3 5SW<br />
  <?php endif; ?>
  <i>(<?php echo $delivery->getDistance(); ?> miles away)</i></p>

  <div id="directions-panel"></div>
  <div id="map-canvas"></div>
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCKeZpb8doUO3DbEqT3t-uRJYsbEPbD3AE&sensor=false"></script>
  <script>
    var directionsDisplay;
    var directionsService = new google.maps.DirectionsService();
    var map;
    <?php if ($_GET) : ?>
      var origin = "95+Hoe+Lane,EN35SW";
      var destination = <?php echo json_encode(str_replace(" ", "+", $row['address']) . "," . str_replace(" ", "+", $row['postcode'])); ?>;
    <?php else : ?>
      var origin = <?php echo json_encode(str_replace(" ", "+", $_SESSION['user']['address']) . "," . str_replace(" ", "+", $_SESSION['user']['postcode'])); ?>;
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
