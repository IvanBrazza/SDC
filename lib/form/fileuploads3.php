<?php
  require('UploadHandlerS3.php');
  $upload_handler = new UploadHandler(
    array(
      'image_versions' => array()
    ), $_POST['upload_dir']
  );
