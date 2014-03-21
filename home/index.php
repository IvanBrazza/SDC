<?php
  require("../lib/common.php");
  $title = "Home";
  $page = "home";

  // Use HTTP since no secure data is being displayed
  forceHTTP();
?>
<head>
  <html lang="en" xml:lang="en" xmlns= "http://www.w3.org/1999/xhtml">
  <meta http-equiv="Content-Language" content="en" />
  <meta name="google-site-verification" content="j7xff2YmYmFCUc5IuJslQ0hvOKaiaHKFqaQXYM-g_VE" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $title ?> | Star Dream Cakes</title>
  <link rel="icon" href="../favicon.ico">
  <link href='//fonts.googleapis.com/css?family=Open+Sans:400italic,400' rel='stylesheet' type='text/css'>
  <link href="../css/bootstrap.css" rel="stylesheet">
  <link href="../css/home.css" rel="stylesheet">
  <link href="../css/main.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="../home"><img src="../img/header-nav-logo.png"></a>
      </div>
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <li <?php if ($page === "home") : ?>class="active"<?php endif; ?>><a href="../home">Home</a></li>
          <li <?php if ($page === "about-us") : ?>class="active"<?php endif; ?>><a href="../about-us">About Us</a></li>
          <li <?php if ($page === "testimonials") : ?>class="active"<?php endif; ?>><a href="../testimonials">Testimonials</a></li>
          <li <?php if ($page === "gallery") : ?>class="active"<?php endif;?>><a href="../gallery">Gallery</a></li>
          <?php if (!empty($_SESSION['user']) and $_SESSION['user']['username'] !== "admin") : ?>
            <li <?php if ($page === "place-an-order") : ?>class="active"<?php endif; ?>><a href="../place-an-order">Place An Order</a></li>
            <li <?php if ($page === "your-orders") : ?>class="active"<?php endif; ?>><a href="../your-orders">Your Orders</a></li>
          <?php endif; ?>
          <?php if (!empty($_SESSION['user']) and $_SESSION['user']['username'] === "admin") : ?>
            <li <?php if ($page === "all-orders") : ?>class="active"<?php endif; ?>><a href="../all-orders">All Orders</a></li>
            <li <?php if ($page === "customer-list") : ?>class="active"<?php endif; ?>><a href="../customer-list">Customer List</a></li>
            <li <?php if ($page === "stats") : ?>class="active"<?php endif;?>><a href="../stats">Stats</a></li>
          <?php endif; ?>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li><a href="#"></a></li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <?php if (!empty($_SESSION['user'])) : ?>
                Welcome, <?php echo $_SESSION['user']['first_name']; ?>!
              <?php else : ?>
                Welcome to Star Dream Cakes!
              <?php endif; ?> <b class="caret"></b>
            </a>
            <ul class="dropdown-menu">
              <?php if (!empty($_SESSION['user'])) : ?>
                <li><a href="../edit-account">Edit Account</a></li>
                <li class="divider"></li>
                <li><a href="../lib/logout.php">Logout</a></li>
              <?php else : ?>
                <li><a href="../login">Login</a></li>
                <li><a href="../register">Register</a></li>
              <?php endif; ?>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div id="myCarousel" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
      <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
      <li data-target="#myCarousel" data-slide-to="1"></li>
      <li data-target="#myCarousel" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner">
      <div class="item active">
        <div id="slide1-wm" class="wm-slide">
          <ul id="slide1-wm-tiles" class="wm-slide-tiles">
            <li><img src="../img/gallery/celebration/01.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/08.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/04.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/06.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/16.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/13.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/07.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/09.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/10.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/12.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/05.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/14.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/02.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/15.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/03.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/17.jpg" width="200px"></li>
          </ul>
        </div>
        <div class="container">
          <div class="carousel-caption">
            <h1>Welcome to Star Dream Cakes.</h1>
            <p>Something something we're awesome idk what to write.</p>
            <p><a class="btn btn-lg btn-primary" href="../register" role="button">Sign up today</a></p>
          </div>
        </div>
      </div>
      <div class="item">
        <div id="slide2-wm" class="wm-slide">
          <ul id="slide2-wm-tiles" class="wm-slide-tiles">
            <li><img src="../img/gallery/cupcake/01.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/08.jpg" width="200px"></li>
            <li><img src="../img/gallery/cupcake/04.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/16.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/13.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/07.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/09.jpg" width="200px"></li>
            <li><img src="../img/gallery/cupcake/06.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/10.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/12.jpg" width="200px"></li>
            <li><img src="../img/gallery/cupcake/05.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/14.jpg" width="200px"></li>
            <li><img src="../img/gallery/cupcake/02.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/15.jpg" width="200px"></li>
            <li><img src="../img/gallery/cupcake/03.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/17.jpg" width="200px"></li>
          </ul>
        </div>
        <div class="container">
          <div class="carousel-caption">
            <h1>Your cake, your way.</h1>
            <p>Your cake is made for you and you alone, therefore we will do our best to meet all of your requirements.</p>
            <p><a class="btn btn-lg btn-primary" href="../gallery" role="button">Browse gallery</a></p>
          </div>
        </div>
      </div>
      <div class="item">
        <div id="slide3-wm" class="wm-slide">
          <ul id="slide3-wm-tiles" class="wm-slide-tiles">
            <li><img src="../img/gallery/other/01.jpg" width="200px"></li>
            <li><img src="../img/gallery/other/08.jpg" width="200px"></li>
            <li><img src="../img/gallery/other/04.jpg" width="200px"></li>
            <li><img src="../img/gallery/other/06.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/16.jpg" width="200px"></li>
            <li><img src="../img/gallery/other/13.jpg" width="200px"></li>
            <li><img src="../img/gallery/other/07.jpg" width="200px"></li>
            <li><img src="../img/gallery/other/09.jpg" width="200px"></li>
            <li><img src="../img/gallery/other/10.jpg" width="200px"></li>
            <li><img src="../img/gallery/other/12.jpg" width="200px"></li>
            <li><img src="../img/gallery/other/05.jpg" width="200px"></li>
            <li><img src="../img/gallery/other/14.jpg" width="200px"></li>
            <li><img src="../img/gallery/other/02.jpg" width="200px"></li>
            <li><img src="../img/gallery/other/15.jpg" width="200px"></li>
            <li><img src="../img/gallery/other/03.jpg" width="200px"></li>
            <li><img src="../img/gallery/celebration/17.jpg" width="200px"></li>
          </ul>
        </div>
        <div class="container">
          <div class="carousel-caption">
            <h1>Delivered straight to your door.</h1>
            <p>No matter what your cake is, if you live in Haringey, Enfield or Barnet, you may be eligible for home delivery with an additional charge.</p>
            <p><a class="btn btn-lg btn-primary" href="../register/" role="button">Get started</a></p>
          </div>
        </div>
      </div>
    </div>
    <a class="left carousel-control" href="#myCarousel" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
    <a class="right carousel-control" href="#myCarousel" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
  </div>
  <div class="container">
    <div class="row">
      <div class="col-lg-4">
      </div>
      <div class="col-lg-4">
        <img class="img-circle" src="../img/fran.jpg" alt="Generic placeholder image" style="width: 140px; height: 140px;">
        <h2>Fran Bacelar</h2>
        <p>Fran Bacelar, the creative talent behind Star Dream Cakes, has been making celebration cakes for over 20 years.
           Her creations for customers and friends have gained her respect locally. Fran also holds the City and Guilds certificate
           in Sugarcraft and design and Wilton Method of Cake Decorating.</p>
      </div>
      <div class="col-lg-4">
      </div>
    </div>
    <hr class="fancy-line hidden-xs">
    <div class="row featurette">
      <div class="col-md-7">
        <h2 class="featurette-heading">Celebration cakes. <span class="text-muted">Party with style.</span></h2>
        <p class="lead">Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.</p>
      </div>
      <div class="col-md-5">
        <img class="featurette-image img-responsive" src="../img/gallery/celebration/17.jpg" alt="Generic placeholder image">
      </div>
    </div>
    <hr class="fancy-line hidden-xs">
    <div class="row featurette visible-xs visible-sm">
      <div class="col-md-7">
        <h2 class="featurette-heading">Cupcakes. <span class="text-muted">They'll melt in your mouth.</span></h2>
        <p class="lead">Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.</p>
      </div>
      <div class="col-md-5">
        <img class="featurette-image img-responsive" src="../img/gallery/cupcake/03.jpg" alt="Generic placeholder image">
      </div>
    </div>
    <div class="row featurette hidden-xs hidden-sm">
      <div class="col-md-5">
        <img class="featurette-image img-responsive" src="../img/gallery/cupcake/03.jpg" alt="Generic placeholder image">
      </div>
      <div class="col-md-7">
        <h2 class="featurette-heading">Cupcakes. <span class="text-muted">They'll melt in your mouth.</span></h2>
        <p class="lead">Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.</p>
      </div>
    </div>
    <hr class="fancy-line hidden-xs">
    <div class="row featurette">
      <div class="col-md-7">
        <h2 class="featurette-heading">Cakes for all occasions. <span class="text-muted">We can do it all.</span></h2>
        <p class="lead">Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.</p>
      </div>
      <div class="col-md-5">
        <img class="featurette-image img-responsive" src="../img/gallery/other/08.jpg" alt="Generic placeholder image">
      </div>
    </div>
    <div class="row footer">
      <div id="col-md-12">
        <div class="copyright">
          <div>&copy; Star Dream Cakes 2014</div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="loading-spinner-dialog">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-body">
          <div class="ajax-load"></div>
        </div>
      </div>
    </div>
  </div>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="../js/bootstrap.js"></script>
  <script src="../js/table-pagination.js"></script>
  <script src="../js/jquery/jquery.wookmark.min.js"></script>
  <script src="../js/jquery/imagesloaded.min.js"></script>
  <script>
    $(document).ready(function() {
      var wmOptions = {
            autoResize: true,
            itemWidth: 200,
            align: "center",
            direction: "right",
            flexibleWidth: true,
            offset: 0,
            verticalOffset: 0,
            fillEmptySapce: false
          },
          $slide1 = $("#slide1-wm-tiles li"),
          $slide2 = $("#slide2-wm-tiles li"),
          $slide3 = $("#slide3-wm-tiles li"),
          d = 0;
      $slide1.imagesLoaded(function() {
        $slide1.wookmark({
            autoResize: true,
            itemWidth: 200,
            align: "center",
            direction: "right",
            flexibleWidth: true,
            container: $("#slide1-wm"),
            offset: 0,
            verticalOffset: 0,
            fillEmptySapce: false
          }).hide().each(function() {
          $(this).delay(d).fadeIn();
          d += 100;
        });
        d = 0;
      });
      $slide2.imagesLoaded(function() {
        $slide2.wookmark({
            autoResize: true,
            itemWidth: 200,
            align: "center",
            direction: "right",
            flexibleWidth: true,
            container: $("#slide2-wm"),
            offset: 0,
            verticalOffset: 0,
            fillEmptySapce: false
          }).hide();
      });
      $slide3.imagesLoaded(function() {
        $slide3.wookmark({
            autoResize: true,
            itemWidth: 200,
            align: "center",
            direction: "right",
            flexibleWidth: true,
            container: $("#slide3-wm"),
            offset: 0,
            verticalOffset: 0,
            fillEmptySapce: false
          }).hide();
      });
      var slide = 1;
      $("#myCarousel").on('slid.bs.carousel', function() {
        if (slide < 3) {
          slide++;
          if (slide == 2) {
            $slide2.trigger("refreshWookmark").each(function() {
              $(this).delay(d).fadeIn();
              d += 100;
            });
            d = 0;
          } else if (slide == 3) {
            $slide3.trigger("refreshWookmark").each(function() {
              $(this).delay(d).fadeIn();
              d += 100;
            });
          }
        }
      });
    });
  </script>
  <script src="../js/main.js"></script>
  <script src="../js/browser.js"></script>
<body>
