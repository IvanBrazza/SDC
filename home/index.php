<?php
  require("../lib/common.php");
  $title = "Home";
  $page = "home";
?>
<head>
  <html lang="en" xml:lang="en" xmlns= "http://www.w3.org/1999/xhtml">
  <meta http-equiv="Content-Language" content="en" />
  <meta name="google-site-verification" content="j7xff2YmYmFCUc5IuJslQ0hvOKaiaHKFqaQXYM-g_VE" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $title ?> | Star Dream Cakes</title>
  <link rel="icon" href="//www.<?php echo $siteUrl; ?>/favicon.ico">
  <link href='//fonts.googleapis.com/css?family=Open+Sans:400italic,400' rel='stylesheet' type='text/css'>
  <link href="//www.<?php echo $siteUrl; ?>/css/bootstrap.css" rel="stylesheet">
  <link href="//www.<?php echo $siteUrl; ?>/css/cookie.css" rel="stylesheet">
  <link href="//www.<?php echo $siteUrl; ?>/css/home.css" rel="stylesheet">
  <link href="//www.<?php echo $siteUrl; ?>/css/main.css" rel="stylesheet">
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
        <a class="navbar-brand" href="//www.<?php echo $siteUrl; ?>/home"><img src="//www.<?php echo $siteUrl; ?>/img/header-nav-logo.png"></a>
      </div>
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <li <?php if ($page === "home") : ?>class="active"<?php endif; ?>><a href="//www.<?php echo $siteUrl; ?>/home">Home</a></li>
          <li <?php if ($page === "about-us") : ?>class="active"<?php endif; ?>><a href="//www.<?php echo $siteUrl; ?>/about-us">About Us</a></li>
          <li <?php if ($page === "testimonials") : ?>class="active"<?php endif; ?>><a href="//www.<?php echo $siteUrl; ?>/testimonials">Testimonials</a></li>
          <li <?php if ($page === "gallery") : ?>class="active"<?php endif;?>><a href="//www.<?php echo $siteUrl; ?>/gallery">Gallery</a></li>
          <?php if (!empty($_SESSION['user']) and $_SESSION['user']['username'] !== "admin") : ?>
            <li <?php if ($page === "place-an-order") : ?>class="active"<?php endif; ?>><a href="//www.<?php echo $siteUrl; ?>/place-an-order">Place An Order</a></li>
            <li <?php if ($page === "your-orders") : ?>class="active"<?php endif; ?>><a href="//www.<?php echo $siteUrl; ?>/your-orders">Your Orders</a></li>
          <?php endif; ?>
          <?php if (!empty($_SESSION['user']) and $_SESSION['user']['username'] === "admin") : ?>
            <li <?php if ($page === "all-orders") : ?>class="active"<?php endif; ?>><a href="//www.<?php echo $siteUrl; ?>/all-orders">All Orders</a></li>
            <li <?php if ($page === "admin") : ?>class="active"<?php endif;?>><a href="//www.<?php echo $siteUrl; ?>/admin">Admin</a></li>
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
                <li><a href="//www.<?php echo $siteUrl; ?>/edit-account">Edit Account</a></li>
                <li class="divider"></li>
                <li><a href="//www.<?php echo $siteUrl; ?>/lib/logout.php">Logout</a></li>
              <?php else : ?>
                <li><a href="//www.<?php echo $siteUrl; ?>/login">Login</a></li>
                <li><a href="//www.<?php echo $siteUrl; ?>/register">Register</a></li>
              <?php endif; ?>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div id="myCarousel" class="carousel slide" data-ride="carousel" data-wrap="false" data-interval="10000">
    <ol class="carousel-indicators">
      <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
      <li data-target="#myCarousel" data-slide-to="1"></li>
      <li data-target="#myCarousel" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner">
      <div class="item active">
        <div id="slide1-wm" class="wm-slide">
          <ul id="slide1-wm-tiles" class="wm-slide-tiles">
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/01.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/08.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/04.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/06.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/16.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/13.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/07.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/09.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/10.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/12.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/05.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/14.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/02.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/15.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/03.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/17.jpg" width="200px" class="lazy"></li>
          </ul>
        </div>
        <div class="container">
          <div class="carousel-caption">
            <h1>Welcome to Star Dream Cakes.</h1>
            <p>Something something we're awesome idk what to write.</p>
            <p><a class="btn btn-lg btn-primary" href="//www.<?php echo $siteUrl; ?>/register" role="button">Sign up today</a></p>
          </div>
        </div>
      </div>
      <div class="item">
        <div id="slide2-wm" class="wm-slide">
          <ul id="slide2-wm-tiles" class="wm-slide-tiles">
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/cupcake/01.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/cupcake/04.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/16.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/13.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/07.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/09.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/cupcake/06.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/10.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/12.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/cupcake/05.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/14.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/cupcake/02.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/08.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/15.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/cupcake/03.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/17.jpg" width="200px" class="lazy"></li>
          </ul>
        </div>
        <div class="container">
          <div class="carousel-caption">
            <h1>Your cake, your way.</h1>
            <p>Your cake is made for you and you alone, therefore we will do our best to meet all of your requirements.</p>
            <p><a class="btn btn-lg btn-primary" href="//www.<?php echo $siteUrl; ?>/gallery" role="button">Browse gallery</a></p>
          </div>
        </div>
      </div>
      <div class="item">
        <div id="slide3-wm" class="wm-slide">
          <ul id="slide3-wm-tiles" class="wm-slide-tiles">
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/getstarted.png" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/other/01.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/other/08.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/other/04.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/other/06.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/16.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/other/13.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/other/07.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/other/09.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/other/10.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/other/12.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/other/05.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/other/14.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/other/02.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/other/15.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/other/03.jpg" width="200px" class="lazy"></li>
            <li><img data-src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/17.jpg" width="200px" class="lazy"></li>
          </ul>
        </div>
        <div class="container">
          <div class="carousel-caption">
            <h1>Delivered straight to your door.</h1>
            <p>No matter what your cake is, if you live in Haringey, Enfield or Barnet, you may be eligible for home delivery with an additional charge.</p>
            <p><a class="btn btn-lg btn-primary" href="//www.<?php echo $siteUrl; ?>/register/" role="button">Get started</a></p>
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
        <img class="img-circle" src="//www.<?php echo $siteUrl; ?>/img/fran.jpg" alt="Generic placeholder image" style="width: 140px; height: 140px;">
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
        <img class="featurette-image img-responsive" src="//www.<?php echo $siteUrl; ?>/img/gallery/celebration/17.jpg" alt="Generic placeholder image">
      </div>
    </div>
    <hr class="fancy-line hidden-xs">
    <div class="row featurette visible-xs visible-sm">
      <div class="col-md-7">
        <h2 class="featurette-heading">Cupcakes. <span class="text-muted">They'll melt in your mouth.</span></h2>
        <p class="lead">Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.</p>
      </div>
      <div class="col-md-5">
        <img class="featurette-image img-responsive" src="//www.<?php echo $siteUrl; ?>/img/gallery/cupcake/03.jpg" alt="Generic placeholder image">
      </div>
    </div>
    <div class="row featurette hidden-xs hidden-sm">
      <div class="col-md-5">
        <img class="featurette-image img-responsive" src="//www.<?php echo $siteUrl; ?>/img/gallery/cupcake/03.jpg" alt="Generic placeholder image">
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
        <img class="featurette-image img-responsive" src="//www.<?php echo $siteUrl; ?>/img/gallery/other/08.jpg" alt="Generic placeholder image">
      </div>
    </div>
    <div class="footer">
      <div class="row social-row">
        <div class="col-md-4">
          <a href="javascript:" class="social social-facebook">Facebook</a>
          <a href="javascript:" class="social social-twitter">Twitter</a>
        </div>
        <div class="col-md-4">
          <div class="copyright">
            <div>&copy; Star Dream Cakes 2014</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="contact">
            <p><span class="glyphicon glyphicon-phone-alt pull-left"></span>020 8800 8135</p>
            <p><span class="glyphicon glyphicon-envelope pull-left"></span><a href="mailto:customerhelp@<?php echo $siteUrl; ?>">customerhelp@<?php echo $siteUrl; ?></a></p>
          </div>
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
  <script src="//www.<?php echo $siteUrl; ?>/js/bootstrap.js"></script>
  <script src="//www.<?php echo $siteUrl; ?>/js/cookie.js"></script>
  <script src="//www.<?php echo $siteUrl; ?>/js/table-pagination.js"></script>
  <script src="//www.<?php echo $siteUrl; ?>/js/jquery/jquery.wookmark.min.js"></script>
  <script src="//www.<?php echo $siteUrl; ?>/js/jquery/imagesloaded.min.js"></script>
  <script src="//www.<?php echo $siteUrl; ?>/js/jquery/jquery.unveil.min.js"></script>
  <script src="//www.<?php echo $siteUrl; ?>/js/home.js"></script>
  <script src="//www.<?php echo $siteUrl; ?>/js/main.js"></script>
  <script src="//www.<?php echo $siteUrl; ?>/js/browser.js"></script>
<body>
