<?php
  /**
    gallery/ - a page to showcase pictures using a jQuery plugin
  **/
  include("../lib/common.php");
  $title = "Gallery";
  $page = "gallery";

  forceHTTP();
?>
<?php include("../lib/header.php"); ?>
<div class="row">
  <div class="col-md-12">
    <h1>Gallery</h1>
    <ul class="nav nav-tabs">
      <li class="active"><a href="#celebration" data-toggle="tab">Celebration Cakes</a></li>
      <li><a href="#cupcakes" data-toggle="tab">Cupcakes</a></li>
      <li><a href="#other" data-toggle="tab">Other Occasion Cakes</a></li>
    </ul>

    <div class="tab-content">
      <div class="tab-pane fade in active" id="celebration">
        <div id="celebration-container">
          <div id="celebration-load" class="load"></div>
          <ul id="celebration-tiles">
            <li class="thumbnail">
              <img src="../img/gallery/celebration/01.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/celebration/02.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/celebration/03.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/celebration/04.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/celebration/05.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/celebration/06.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/celebration/07.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/celebration/08.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/celebration/09.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/celebration/10.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/celebration/11.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/celebration/12.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/celebration/13.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/celebration/14.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/celebration/15.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/celebration/16.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/celebration/17.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/celebration/18.jpg" width="180px" title="" class="thumb">
            </li>
          </ul>
        </div>
      </div>
      <div class="tab-pane fade" id="cupcakes">
        <div id="cupcakes-container">
          <div id="cupcakes-load" class="load"></div>
          <ul id="cupcakes-tiles">
            <li class="thumbnail">
              <img src="../img/gallery/cupcake/01.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/cupcake/02.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/cupcake/03.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/cupcake/04.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/cupcake/05.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/cupcake/06.jpg" width="180px" title="" class="thumb">
            </li>
          </ul>
        </div>
      </div>
      <div class="tab-pane fade" id="other">
        <div id="other-container">
          <div id="other-load" class="load"></div>
          <ul id="other-tiles">
            <li class="thumbnail">
              <img src="../img/gallery/other/01.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/other/02.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/other/03.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/other/04.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/other/05.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/other/06.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/other/07.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/other/08.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/other/09.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/other/10.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/other/11.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/other/12.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/other/13.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/other/14.jpg" width="180px" title="" class="thumb">
            </li>
            <li class="thumbnail">
              <img src="../img/gallery/other/15.jpg" width="180px" title="" class="thumb">
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include("../lib/footer.php"); ?>
