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
        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="First slide">
        <div class="container">
          <div class="carousel-caption">
            <h1>Example headline.</h1>
            <p>Note: If you're viewing this page via a <code>file://</code> URL, the "next" and "previous" Glyphicon buttons on the left and right might not load/display properly due to web browser security rules.</p>
            <p><a class="btn btn-lg btn-primary" href="#" role="button">Sign up today</a></p>
          </div>
        </div>
      </div>
      <div class="item">
        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAGZmZgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Second slide">
        <div class="container">
          <div class="carousel-caption">
            <h1>Your cake, your way.</h1>
            <p>Your cake is made for you and you alone, therefore we will do our best to meet all of your requirements.</p>
            <p><a class="btn btn-lg btn-primary" href="../gallery" role="button">Browse gallery</a></p>
          </div>
        </div>
      </div>
      <div class="item">
        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAFVVVQAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Third slide">
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
  <div class="container marketing">
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
    <hr class="fancy-line">
    <div class="row featurette">
      <div class="col-md-7">
        <h2 class="featurette-heading">Celebration cakes. <span class="text-muted">Party with style.</span></h2>
        <p class="lead">Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.</p>
      </div>
      <div class="col-md-5">
        <img class="featurette-image img-responsive" src="../img/gallery/celebration/17.jpg" alt="Generic placeholder image">
      </div>
    </div>
    <hr class="fancy-line">
    <div class="row featurette">
      <div class="col-md-5">
        <img class="featurette-image img-responsive" src="../img/gallery/cupcake/03.jpg" alt="Generic placeholder image">
      </div>
      <div class="col-md-7">
        <h2 class="featurette-heading">Cupcakes. <span class="text-muted">They'll melt in your mouth.</span></h2>
        <p class="lead">Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.</p>
      </div>
    </div>
    <hr class="fancy-line">
    <div class="row featurette">
      <div class="col-md-7">
        <h2 class="featurette-heading">Cakes for all occasions. <span class="text-muted">We can do it all.</span></h2>
        <p class="lead">Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.</p>
      </div>
      <div class="col-md-5">
        <img class="featurette-image img-responsive" src="../img/gallery/other/08.jpg" alt="Generic placeholder image">
      </div>
    </div>
  </div>
  <div id="footer">
    <hr class="fancy-line">
    <div class="copyright">
      <div>&copy; Star Dream Cakes 2014</div>
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
  <script src="../js/main.js"></script>
  <script src="../js/browser.js"></script>
<body>
