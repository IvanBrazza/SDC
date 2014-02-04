<?php
  /**
    about-us/ - display a page describing the business
  **/
  require("../lib/common.php");
  $title = "About Us";
  $page = "about-us";

  // Use HTTP since there isn't any secure content being displayed
  forceHTTP();
?>
<?php include("../lib/header.php"); ?>
  <h1>About Us</h1>
  <p>A cake is the best part of any celebration and what could be nicer than a delicious cake beautifully crafted to your individual requirements. We can scan photographs, company logos, pictures of your favourite celebrities or even pictures of an event! The image is then fully iced and decorated onto the cake of your choice. Ideally we need two weeks notice to create your cake.</p>
  <p>All you need to do to personalise your own cake is:<br />
     1. Select your favourite photo or image and simply email it to us<br />
     2. Your chosen photo will then be scanned using edible ink and applied to the cake!</p>
  <p>It is very important that the photo you send us to scan is the best quality possible seeing as scanning can not improve the photo supplied so try to select one that has no creases or folds on it. As long as you are happy with your selected picture we will then scan it on to the cake.</p>
  <p>A few of the flavours we have available are almond, vanilla, cinnamon, banana, chocolate, ginger, lemon, marble, pineapple and coconut.</p>
<?php include("../lib/footer.php"); ?>
