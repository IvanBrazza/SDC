<head>
  <title><?php echo $title ?> | Star Dream Cakes</title>
  <link rel="icon" href="../favicon.ico">
  <link href="../css/main.css" rel="stylesheet">
  <link href="../css/timepicker.css" rel="stylesheet">
  <link href="../css/jquery-ui.css" rel="stylesheet">
</head>
<body>
  <script type="text/javascript">
    var $buoop = {
      vs: {i:8,f:15,o:10.6,s:4,n:10},
      reminder: 0,
      newwindow: true
    } 
    $buoop.ol = window.onload; 
    window.onload=function(){ 
      try {if ($buoop.ol) $buoop.ol();}catch (e) {} 
      var e = document.createElement("script"); 
      e.setAttribute("type", "text/javascript"); 
      e.setAttribute("src", "http://browser-update.org/update.js"); 
      document.body.appendChild(e); 
    } 
  </script> 
  <div class="navbar">
    <div class="nav-container">
      <ul>
        <li <?php if ($page === "home") : ?>class="active"<?php endif; ?>><a href="../home">Home</a></li>
        <li <?php if ($page === "about-us") : ?>class="active"<?php endif; ?>><a href="#">About Us</a></li>
        <li <?php if ($page === "testimonials") : ?>class="active"<?php endif; ?>><a href="../testimonials">Testimonials</a></li>
        <?php if ($_SESSION and $_SESSION['user']['username'] !== "admin") : ?>
          <li <?php if ($page === "place-an-order") : ?>class="active"<?php endif; ?>><a href="../place-an-order">Place An Order</a></li>
          <li <?php if ($page === "your-orders") : ?>class="active"<?php endif; ?>><a href="../your-orders">Your Orders</a></li>
        <?php endif; ?>
        <?php if ($_SESSION and $_SESSION['user']['username'] === "admin") : ?>
          <li <?php if ($page === "all-orders") : ?>class="active"<?php endif; ?>><a href="../all-orders">All Orders</a></li>
          <li <?php if ($page === "memberlist") : ?>class="active"<?php endif; ?>><a href="../memberlist">Memberlist</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
  <div class="container">
    <div class="header">
      <div class="logo">
        <img src="../img/logo.jpg" alt="logo" height="50px" />
      </div>
      <div class="user">
        <span>Welcome to Star Dream Cakes<?php if ($_SESSION) : ?>, <?php echo $_SESSION['user']['first_name']; ?>!<?php endif; ?></span><br />
        <span class="links">
          <?php if ($_SESSION) : ?>
            <a href="../edit-account">Edit Account</a> | <a href="../lib/logout.php">Logout</a>
          <?php else : ?>
            <a href="../login">Login</a> | <a href="../register">Register</a>
          <?php endif; ?>
        </span>
      </div>
    </div>
