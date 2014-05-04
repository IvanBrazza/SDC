<?php
 /**
   admin/ - admin stuffs
 **/
  require("../lib/common.php");
  $page = "admin";
  $title = "Admin";

  // Only the admin user can access this page
  if (empty($_SESSION) or $_SESSION['user']['username'] != "admin")
  {
    header("Location: ../home/");
    die();
  }

  // Get customer details
  $query = "
    SELECT
      customer_id,
      username,
      email,
      first_name,
      last_name,
      address,
      postcode
    FROM
      users
  ";
  $db->runQuery($query, null);
  $customers = $db->fetchAll();

  // Get all order details
  $query = "
    SELECT
      *
    FROM
      orders
  ";
  $db->runQuery($query, null);
  $rows = $db->fetchAll();
  foreach ($rows as $row)
  {
    // Calculate orders per customer & completed/outstanding
    if ($row['completed'] === "0")
    {
      $outstanding_orders++;
    } 
    else if ($row['completed'] === "1")
    {
      $completed_orders++;
    }
    $orders++;
  }

  // Get all fillings
  $query = "
    SELECT
      *
    FROM
      fillings
  ";
  $db->runQuery($query, null);
  $fillings = $db->fetchAll();

  // Get all decorations
  $query = "
    SELECT
      *
    FROM
      decorations
  ";
  $db->runQuery($query, null);
  $decorations = $db->fetchAll();

  // Get all cake types
  $query = "
    SELECT
      *
    FROM
      cakes
  ";
  $db->runQuery($query, null);
  $cakes = $db->fetchAll();

  // Get galleries
  $query = "
    SELECT
      *
    FROM
      gallery
  ";
  $db->runQuery($query, null);
  $galleries = $db->fetchAll();

  // Get latest backup times
  $latestDbBackup    = file_get_contents("../backups/db/latest.txt");
  $latestFilesBackup = file_get_contents("../backups/files/latest.txt");

  // Generate token
  $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");
?>
<?php include("../lib/header.php"); ?>
<script>
  var $token = "<?php echo $_SESSION['token']; ?>";
</script>
<div class="modal fade" role="dialog" aria-hidden="true" id="unsupported_browser" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">You're using an unsupported browser!</h4>
      </div>
      <div class="modal-body">
        <p>
          It seems that you're using a browser that doesn't support the HTML5 canvas element. Because of this,
          you won't be able to see any charts relating to any statistics.
        </p>
        <p>
          To see these charts, you should upgrade to a modern browser such as Google Chrome or Mozilla Firefox.
          As well as enabling you to see the charts, you'll have a much nicer browsing experience.
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        <a href="https://www.google.com/chrome" class="pull-right"><button type="button" class="btn btn-primary">Get Google Chrome</button></a>
      </div>
    </div>
  </div>
</div>
<?php foreach ($galleries as $gallery) : ?>
  <?php
    $query = "
      SELECT
        *
      FROM
        " . $gallery['table_name']
    ;
    $db->runQuery($query, null);
    $rows = $db->fetchAll();
  ?>
  <div class="modal fade" role="dialog" aria-hidden="true" id="gallery_modal_<?php echo $gallery['gallery_id']; ?>" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Edit Images In "<?php echo $gallery['gallery_name']; ?>"</h4>
        </div>
        <div class="modal-body">
          <?php if (empty($rows)) : ?>
            <p>There aren't any images in this gallery.</p>
          <?php endif; ?>
          <form class="fileupload" action="../lib/form/fileuploads3.php" method="POST" enctype="multipart/form-data" data-upload-template-id="template-upload-<?php echo $gallery['gallery_id']; ?>" data-download-template-id="template-download-<?php echo $gallery['gallery_id']; ?>">
            <input type="hidden" name="upload_dir" value="<?php echo $gallery['gallery_id']; ?>">
            <div class="row fileupload-buttonbar">
              <div class="col-lg-7">
                <span class="btn btn-success fileinput-button">
                  <i class="glyphicon glyphicon-plus"></i>
                  <span>Add Image...</span>
                  <input type="file" name="files[]" accept="image/*" multiple>
                </span>
                <span class="fileupload-process"></span>
              </div>
            </div>
            <span id="uploadstatus"></span>
            <div class="well well-sm">
              <table role="presentation" class="uploaded-images table">
                <tbody class="files">
                  <?php if (!empty($rows)) : ?>
                    <?php foreach ($rows as $row) : ?>
                      <tr class="fade in">
                        <td>
                          <span class="preview">
                            <img src="../img/spinner.gif" data-src="https://s3.amazonaws.com/SDC-images/<?php echo $gallery['gallery_id']."/".$row['images']; ?>" width="100px" class="lazy">
                          </span>
                        </td>
                        <td>
                          <p class="name" id="filename">
                            <span><?php echo $row['images']; ?></span>
                          </p>
                        </td>
                        <td>
                          <a class="btn btn-danger pull-right delete-image" data-image="<?php echo $row['images']; ?>" data-gallery="<?php echo $gallery['gallery_id']; ?>">
                            <i class="glyphicon glyphicon-trash"></i>
                            <span>Delete</span>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </form>
          <!-- The template to display files available for upload -->
          <script id="template-upload-<?php echo $gallery['gallery_id']; ?>" type="text/x-tmpl">
          {% for (var i=0, file; file=o.files[i]; i++) { %}
            <tr class="template-upload fade">
              <td>
                <span class="preview"></span>
              </td>
              <td>
                <p class="name">{%=file.name%}</p>
                <strong class="error text-danger"></strong>
              </td>
              <td>
                <p class="size">Processing...</p>
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
              </td>
              <td>
                {% if (!i && !o.options.autoUpload) { %}
                  <button class="btn btn-primary start" disabled>
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start</span>
                  </button>
                {% } %}
                {% if (!i) { %}
                  <button class="btn btn-warning cancel pull-right">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                  </button>
                {% } %}
              </td>
            </tr>
          {% } %}
          </script>
          <!-- The template to display files available for download -->
          <script id="template-download-<?php echo $gallery['gallery_id']; ?>" type="text/x-tmpl">
          {% for (var i=0, file; file=o.files[i]; i++) { %}
            <tr class="template-download fade">
              <td>
                <span class="preview">
                  <img src="../img/spinner.gif" data-src="https://s3.amazonaws.com/SDC-images/<?php echo $gallery['gallery_id']; ?>/{%=file.name%}" width="100px" class="new_lazy">
                </span>
              </td>
              <td>
                <p class="name" id="filename">
                  <span>{%=file.name%}</span>
                </p>
                {% if (file.error) { %}
                  <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                {% } %}
              </td>
              <td>
                <a class="btn btn-danger delete-image pull-right" data-image="{%=file.name%}" data-gallery="<?php echo $gallery['gallery_id']; ?>">
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Delete</span>
                </a>
              </td>
            </tr>
          {% } %}
          </script>
        </div>
        <div class="modal-footer">
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>
<div class="row">
  <div class="col-md-2">
    <div class="scrollspy affix hidden-sm hidden-xs">
      <ul class="nav">
        <li>
          <a href="#customer-list">Customer List</a>
        </li>
        <li>
          <a href="#stats">Stats</a>
          <div class="nested-scrollspy" id="stats-scrollspy">
            <ul class="nav">
              <li>
                <a href="#stats-general">General stats</a>
              </li>
              <li>
                <a href="#stats-by-month">Orders placed by month</a>
              </li>
              <li>
                <a href="#stats-cake-types">Popularity of cake types</a>
              </li>
              <li>
                <a href="#stats-fillings">Popularity of fillings</a>
              </li>
              <li>
                <a href="#stats-decorations">Popularity of decorations</a>
              </li>
            </ul>
          </div>
        </li>
        <li>
          <a href="#edit">Edit Products</a>
          <div class="nested-scrollspy" id="edit-scrollspy">
            <ul class="nav">
              <li>
                <a href="#edit-fillings">Edit fillings</a>
              </li>
              <li>
                <a href="#edit-decorations">Edit decorations</a>
              </li>
              <li>
                <a href="#edit-cake-types">Edit cake types</a>
              </li>
            </ul>
          </div>
        </li>
        <li>
          <a href="#galleries">Galleries</a>
        </li>
        <li>
          <a href="#backup">Backup</a>
          <div class="nested-scrollspy" id="backup-scrollspy">
            <ul class="nav">
              <li>
                <a href="#backup-website-files">Backup website files</a>
              </li>
              <li>
                <a href="#backup-database">Backup database</a>
              </li>
            </ul>
          </div>
        </li>
      </ul>
    </div>
  </div>
  <div class="col-md-10">
    <div id="customer-list">
      <div class="page-header">
        <h1>Customer List</h1>
      </div>
      <form id="customer_search" class="form-inline col-md-6" role="form">
        <input type="hidden" id="token" name="token" value="<?php echo $_SESSION['token']; ?>" />
        <div class="input-group">
          <input type="text" id="customer_name" name="customer_name" class="form-control" placeholder="Enter customer name">
          <span class="input-group-btn">
            <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span>   Search all customers</button>
          </span>
        </div>
      </form>
      <div class="alert alert-danger col-md-6" id="search_error_message" style="max-height: 34px;padding-top: 6px;"></div>
      <script>
        var customerNames = [
          <?php
            foreach ($customers as $customer)
            {
              echo "\"" . $customer['first_name'] . " " . $customer['last_name'] . "\",";
            }
          ?>
        ];
      </script>
      <div class="table-responsive">
        <table id="orders-js" class="table table-hover">
          <thead>
            <tr> 
              <th>Username</th> 
              <th>E-Mail Address</th>
              <th>First Name</th>
              <th>Last Name</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($customers as $customer): ?>
              <tr>
                <td><a href="../all-orders/?id=<?php echo $customer['customer_id']; ?>"></a><?php echo htmlentities($customer['username'], ENT_QUOTES, 'UTF-8'); ?></td> 
                <td><?php echo htmlentities($customer['email'], ENT_QUOTES, 'UTF-8'); ?></td> 
                <td><?php echo htmlentities($customer['first_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlentities($customer['last_name'], ENT_QUOTES, 'UTF-8'); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <div id="stats">
      <div class="page-header">
        <h1>Stats</h1>
      </div>
      <h3 id="stats-general">General Stats</h3>
      <table id="order-stats" class="table">
        <tr>
          <th>Total number of orders:</th>
          <td><?php echo $orders; ?></td>
        </tr>
        <tr>
          <th>Number of outstanding orders:</th>
          <td><?php echo $outstanding_orders; ?></td>
        </tr>
        <tr>
          <th>Number of completed orders:</th>
          <td><?php echo $completed_orders; ?></td>
        </tr>
      </table>
      <div id="stats-by-month">
        <h3>Orders placed by month</h3>
        <canvas id="ordersChart" height="350px" width="350px"></canvas>
      </div>
      <div id="stats-cake-types">
        <h3>Popularity of cake types</h3>
        <canvas id="cakesChart" height="350px" width="350px"></canvas>
      </div>
      <div id="stats-fillings">
        <h3>Popularity of fillings</h3>
        <canvas id="fillingsChart" height="350px" width="350px"></canvas>
      </div>
      <div id="stats-decorations">
        <h3>Popularity of decorations</h3>
        <canvas id="decorationsChart" height="350px" width="350px"></canvas>
      </div>
    </div>
    <div id="edit">
      <div class="page-header">
        <h1>Edit Products</h1>
      </div>
      <h3 id="edit-fillings">
        Edit fillings
        <button class="btn btn-primary btn-sm pull-right" id="add-filling"><span class="glyphicon glyphicon-plus"></span>   Add Filling</button>
      </h3>
      <div class="table-responsive">
        <table class="table table-striped" id="fillings">
          <thead>
            <tr>
              <th>#</th>
              <th>Filling Name</th>
              <th>Filling Price</th>
              <th>Edit Filling</th>
              <th>Delete Filling</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($fillings as $filling) : ?>
              <tr data-fillingId="<?php echo $filling['filling_id']; ?>">
                <td><?php echo $filling['filling_id']; ?></td>
                <td><?php echo $filling['filling_name']; ?></td>
                <td>&pound;<?php echo $filling['filling_price']; ?></td>
                <td><button class="btn btn-primary btn-sm edit-filling"><span class="glyphicon glyphicon-pencil"></span></button></td>
                <td><button class="btn btn-danger btn-sm delete-filling"><span class="glyphicon glyphicon-trash"></span></button></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <h3 id="edit-decorations">
        Edit decorations
        <button class="btn btn-primary btn-sm pull-right" id="add-decor"><span class="glyphicon glyphicon-plus"></span>   Add Decoration</button>
      </h3>
      <div class="table-responsive">
        <table class="table table-striped" id="decorations">
          <thead>
            <tr>
              <th>#</th>
              <th>Decoration Name</th>
              <th>Decoration Price</th>
              <th>Edit Decoration</th>
              <th>Delete Decoration</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($decorations as $decoration) : ?>
              <tr data-decorid="<?php echo $decoration['decor_id']; ?>">
                <td><?php echo $decoration['decor_id']; ?></td>
                <td><?php echo $decoration['decor_name']; ?></td>
                <td>&pound;<?php echo $decoration['decor_price']; ?></td>
                <td><button class="btn btn-primary btn-sm edit-decor"><span class="glyphicon glyphicon-pencil"></span></button></td>
                <td><button class="btn btn-danger btn-sm delete-decor"><span class="glyphicon glyphicon-trash"></span></button></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <h3 id="edit-cake-types">
        Edit cake types
        <button class="btn btn-primary btn-sm pull-right" id="add-cake-type"><span class="glyphicon glyphicon-plus"></span>   Add Cake Type</button>
      </h3>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Cake Type</th>
              <th>Cake Type Price</th>
              <th>Edit Cake Type</th>
              <th>Delete Cake Type</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cakes as $cake) : ?>
              <tr data-caketypeid="<?php echo $cake['cake_id']; ?>">
                <td><?php echo $cake['cake_id']; ?></td>
                <td><?php echo $cake['cake_size'] . " " . $cake['cake_type']; ?></td>
                <td>&pound;<?php echo $cake['cake_price']; ?></td>
                <td><button class="btn btn-primary btn-sm edit-cake-type"><span class="glyphicon glyphicon-pencil"></span></button></td>
                <td><button class="btn btn-danger btn-sm delete-cake-type"><span class="glyphicon glyphicon-trash"></span></button></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div id="galleries">
        <div class="page-header">
          <h1>
            Galleries
            <button class="btn btn-primary btn-sm pull-right" id="add-gallery"><span class="glyphicon glyphicon-plus"></span>   Add Gallery</button>
          </h1>
        </div>
        <div class="table-responsive">
          <table class="table table-striped" id="gallery">
            <thead>
              <tr>
                <th>#</th>
                <th>Gallery Name</th>
                <th>Edit Images</th>
                <th>Delete Gallery</th>
              </tr>
            </thead>
            <?php foreach ($galleries as $gallery) : ?>
              <tr data-galleryid="<?php echo $gallery['gallery_id']; ?>">
                <td><?php echo $gallery['gallery_id']; ?></td>
                <td><?php echo $gallery['gallery_name']; ?></td>
                <td><button class="btn btn-primary btn-sm edit-gallery"><span class="glyphicon glyphicon-pencil"></span></button></td>
                <td><button class="btn btn-danger btn-sm delete-gallery"><span class="glyphicon glyphicon-trash"></span></button></td>
              </tr>
            <?php endforeach; ?>
          </table>
        </div>
      </div>
      <div id="backup">
        <div class="page-header">
          <h1>Backup</h3>
        </div>
        <h3 id="backup-website-files">Backup website files</h3>
        <p>Here you can download a backup of the website files. This will be a .zip file containing the all site pages (and relevant css/js/images) as well as the PayPal SDK. <strong>Site file backups occur weekly.</strong></p>
        <a href="../backups/files/files-dump-latest.zip" class="btn btn-primary"><span class="glyphicon glyphicon-download-alt"></span>   Download Website Files</a>
        <small>Latest backup: <?php echo $latestDbBackup; ?></small>
        <h3 id="backup-database">Backup database</h3>
        <p>Here you can download a backup of the database. This contains details about orders, users, and other details that are permanantly stored. <strong>Database backups occur daily.</strong></p>
        <a href="../backups/db/db-dump-latest.zip" class="btn btn-primary"><span class="glyphicon glyphicon-download-alt"></span>   Download Database</a>
        <small>Latest backup: <?php echo $latestFilesBackup; ?></small>
      </div>
    </div>
  </div>
</div>
<?php include("../lib/footer.php"); ?>
