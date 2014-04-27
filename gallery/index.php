<?php
  /**
    gallery/ - a page to showcase pictures using a jQuery plugin
  **/
  include("../lib/common.php");
  $title = "Gallery";
  $page = "gallery";

  $query = "
    SELECT
      *
    FROM
      gallery
  ";
  $db->runQuery($query, null);
  $galleries = $db->fetchAll();
?>
<?php include("../lib/header.php"); ?>
<div class="row">
  <div class="col-md-12">
    <h1>Gallery</h1>
    <div class="alert alert-info alert-dismissable">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <strong>Psst.</strong> Click an image to see it bigger
    </div>
    <ul class="nav nav-tabs">
      <?php foreach ($galleries as $gallery) : ?>
        <?php
          $query = "
            SELECT
              *
            FROM " .
              $gallery['table_name']
          ;
          $db->runQuery($query, null);
          $rows = $db->fetchAll();
        ?>
        <?php if ($rows) : ?>
          <li <?php if ($gallery['gallery_id'] == "1") {echo "class='active'";} ?>><a href="#<?php echo $gallery['table_name']; ?>" data-toggle="tab" data-gallery="<?php echo $gallery['table_name']; ?>"><?php echo $gallery['gallery_name']; ?></a></li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
    <div class="tab-content">
      <?php foreach ($galleries as $gallery) : ?>
        <?php
          $query = "
            SELECT
              *
            FROM " .
              $gallery['table_name']
          ;
          $db->runQuery($query, null);
          $rows = $db->fetchAll();
        ?>
        <?php if ($rows) : ?>
          <div class="tab-pane fade in <?php  if ($gallery['gallery_id'] == "1") {echo "active";} ?>" id="<?php echo $gallery['table_name']; ?>">
            <div class="gallery_container">
              <div class="load"></div>
              <ul>
                <?php foreach ($rows as $row) : ?>
                  <li class="thumbnail">
                    <a href="https://s3.amazonaws.com/SDC-images/<?php echo $gallery['gallery_id'] . "/" . $row['images']; ?>" data-lightbox="<?php echo $gallery['table_name']; ?>">
                      <img src="https://s3.amazonaws.com/SDC-images/<?php echo $gallery['gallery_id'] . "/" . $row['images']; ?>" width="180px" class="thumb">
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        <?php endif?>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php include("../lib/footer.php"); ?>
