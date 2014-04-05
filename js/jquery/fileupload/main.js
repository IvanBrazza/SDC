$(document).ready(function() {
  $('#fileupload').fileupload({
    autoUpload: true,
    url: '../lib/form/fileupload.php',
    maxFileSize: 5000000,
    acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
    maxNumberOfFiles: 1,
    previewMaxWidth: 150,
    previewMaxHeight: 150,
    previewMinWidth: 150
  })
  .bind('fileuploadadd', function (e, data) {
    resizeiframe();
    $("#uploadstatus").html("Uploading image...");
    if (data.files[0].size > 5000000) {
    } else {
      $(".fileupload-buttonbar").hide();
    }
  })
  .bind('fileuploaddone', function (e, data) {
    $("#uploadstatus").html("Uploaded image");
  });

  window.parent.$("#accordion").on('hidden.bs.collapse', function(e) {
    if (e.target.id == "theCake") {
      resizeiframe();
    }
  });
  window.parent.$('a[href="#uploadaphoto"]').click(function() {
    resizeiframe();
  });
});

function resizeiframe() {
  setTimeout(function() {
    var iframe = window.parent.document.getElementById("ifileupload").contentWindow;
    $(window.parent.document.getElementById("ifileupload")).animate({height: $("body").outerHeight()+20});
  }, 500);
}
