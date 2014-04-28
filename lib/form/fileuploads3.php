<?php
  require('UploadHandlerS3.php');
  Class CustomUploadHandler extends UploadHandler
  {
    protected function trim_file_name($file_path, $name, $size, $type, $error, $index, $content_range) {
      $name = parent::trim_file_name($file_path, $name, $size, $type, $error, $index, $content_range);
      $name = md5(uniqid('', true));
      // Add missing file extension for known image types:
      if (strpos($name, '.') === false &&
              preg_match('/^image\/(gif|jpe?g|png)/', $type, $matches)) {
          $name .= '.'.$matches[1];
      }
      if (function_exists('exif_imagetype')) {
          switch(exif_imagetype($file_path)){
              case IMAGETYPE_JPEG:
                  $extensions = array('jpg', 'jpeg');
                  break;
              case IMAGETYPE_PNG:
                  $extensions = array('png');
                  break;
              case IMAGETYPE_GIF:
                  $extensions = array('gif');
                  break;
          }
          // Adjust incorrect image file extensions:
          if (!empty($extensions)) {
              $parts = explode('.', $name);
              $extIndex = count($parts) - 1;
              $ext = strtolower(@$parts[$extIndex]);
              if (!in_array($ext, $extensions)) {
                  $parts[$extIndex] = $extensions[0];
                  $name = implode('.', $parts);
              }
          }
      }
      return $name;
    }
  }
  $upload_handler = new CustomUploadHandler(
    array(
      'image_versions' => array()
    ), $_POST['upload_dir']
  );
