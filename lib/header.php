<head>
  <title><?php echo $title ?> | Star Dream Cakes</title>
  <link rel="icon" href="../favicon.ico">
  <link href="../css/main.css" rel="stylesheet">
  <link href="../css/timepicker.css" rel="stylesheet">
  <link href="../css/jquery-ui.css" rel="stylesheet">
</head>
<body>
  <div class="navbar">
    <ul>
      <?php if ($_SESSION) : ?>
        <li><a href="../lib/logout.php">Logout</a></li>
        <li <?php if ($page === "edit-account") : ?>class="active"<?php endif; ?>><a href="../edit-account">Edit Account</a></li>
      <?php endif; ?>
      <?php if (!$_SESSION) : ?>
        <li <?php if ($page === "login") : ?>class="active"<?php endif; ?>><a href="../login">Login</a></li>
        <li <?php if ($page === "register") : ?>class="active"<?php endif; ?>><a href="../register">Register</a></li>
      <?php endif; ?>
      <?php if ($_SESSION and $_SESSION['user']['username'] === "admin") : ?>
        <li <?php if ($page === "all-orders") : ?>class="active"<?php endif; ?>><a href="../all-orders">All Orders</a></li>
        <li <?php if ($page === "memberlist") : ?>class="active"<?php endif; ?>><a href="../memberlist">Memberlist</a></li>
      <?php endif; ?>
      <?php if ($_SESSION and $_SESSION['user']['username'] !== "admin") : ?>
        <li <?php if ($page === "your-orders") : ?>class="active"<?php endif; ?>><a href="../your-orders">Your Orders</a></li>
        <li <?php if ($page === "place-an-order") : ?>class="active"<?php endif; ?>><a href="../place-an-order">Place An Order</a></li>
      <?php endif; ?>
      <li <?php if ($page === "testimonials") : ?>class="active"<?php endif; ?>><a href="../testimonials">Testimonials</a></li>
      <li <?php if ($page === "about-us") : ?>class="active"<?php endif; ?>><a href="#">About Us</a></li>
      <li <?php if ($page === "home") : ?>class="active"<?php endif; ?>><a href="../home">Home</a></li>
    </ul>
  </div>
