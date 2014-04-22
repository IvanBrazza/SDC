$(document).ready(function() {
  $('body').scrollspy({
    target: '.scrollspy',
    offset: 100
  });
  $(".scrollspy").on('activate.bs.scrollspy', function () {
    var current = $(this).find(".active a").html();
    if (current == "Customer List") {
      $(this).find(".nested-scrollspy").hide();
    } else if (current == "Stats") {
      $(this).find(".nested-scrollspy").hide();
      $("#stats-scrollspy").show();
    } else if (current == "Edit Products") {
      $(this).find(".nested-scrollspy").hide();
      $("#edit-scrollspy").show();
    } else if (current == "Backup") {
      $(this).find(".nested-scrollspy").hide();
      $("#backup-scrollspy").show();
    }
  });

  // When the customer search form is submitted
  $("#customer_search").submit(function(e) {
    // Make an AJAX call to lib/form/order-search.php
    // to see if the order exists. If it does, redirect
    // to that page, otherwise show an error message
    $.ajax({
      type: 'post',
      url: '../lib/form/customer-search.php',
      data: $(this).serialize(),
      success: function(response) {
        if (response.slice(0,13) === "../all-orders") {
          window.location.href = response;
        } else {
          $("#search_error_message").html("<span class='glyphicon glyphicon-remove-circle'></span>" + response).show();
        }
      }
    });
    e.preventDefault();
  });

  // Enable autocomplete for the order search form
  // with all of the order numbers. If the user
  // clicks on one of these autocompletes, insert
  // that value into the form and submit it
  $("#customer_name").autocomplete({
    source: customerNames,
    select: function(event, ui) {
      $("#customer_name").val(ui.item.value);
      $("#customer_search").submit();
    }
  });

  $("body").on('click', '.edit-filling', function() {
    var $row           = $(this).closest("tr"),
        $filling_id    = $row.data("fillingid"),
        $filling_name  = $row.find("td:eq(1)").html(),
        $filling_price = $row.find("td:eq(2)").html().replace(/[^\d]/g, "");
    $row.find("td:eq(1)").html("<input name='filling_name' class='form-control' value='" + $filling_name + "' data-original='" + $filling_name + "'><span class='validate-error name-error'></span>");
    $row.find("td:eq(2)").html("<span style='display:inline-block;'>&pound;</span><input name='filling_price' class='form-control' value='" + $filling_price + "' data-original='" + $filling_price + "' style='width:90%;margin-left:9px;display:inline-block;'><span class='validate-error price-error'></span>");
    $row.find("td:eq(3)").html('<button class="btn btn-success btn-sm submit-filling-edit"><span class="glyphicon glyphicon-ok"></span>   Edit</button>');
    $row.find("td:eq(4)").html('<button class="btn btn-danger btn-sm remove-filling-edit"><span class="glyphicon glyphicon-remove"></span>   Cancel</button>');
  });

  $("body").on('click', '.submit-filling-edit', function() {
    var $row           = $(this).closest("tr"),
        $filling_id    = $row.data("fillingid"),
        $filling_name  = $row.find("td:eq(1) input").val(),
        $filling_price = $row.find("td:eq(2) input").val(),
        name_regex     = /^[A-Za-z ]+$/,
        price_regex    = /^\d+(\.\d{2})?$/;
    if (name_regex.test($filling_name) && price_regex.test($filling_price)) {
      $row.find(".name-error").slideUp("fast");
      $row.find(".price-error").slideUp("fast");
      $.ajax({
        type: 'post',
        url: '../lib/edit-fillingdecor.php',
        data: {
          token: $token,
          type: 'filling',
          command: 'edit',
          id: $filling_id,
          filling_name: $filling_name,
          filling_price: $filling_price
        },
        success: function(response) {
          var object = JSON.parse(response);
          $token = object.token;
          if (object.status == "success") {
            $("#success_message .alert").html("<span class='glyphicon glyphicon-ok-circle'></span>   Successfully edited filling");
            $("#success_message").modal("show");
            $row.find("td:eq(1)").html($filling_name);
            $row.find("td:eq(2)").html("&pound;" + $filling_price);
            $row.find("td:eq(3)").html("<button class='btn btn-primary btn-sm edit-filling'><span class='glyphicon glyphicon-pencil'></span></button>");
            $row.find("td:eq(4)").html("<button class='btn btn-danger btn-sm delete-filling'><span class='glyphicon glyphicon-trash'></span></button>");
            setTimeout(function() {
              $("#success_message").modal("hide");
            }, 1500);
          } else {
            $("#error_message .alert").html("<span class='glyphicon glyphicon-remove-circle'></span>   " + object.error);
            $("#error_message").modal("show");
          }
        }
      });
    } else {
      if ($filling_name == "") {
        $row.find(".name-error").html("Please enter a filling name").slideDown("fast");
      } else if (!name_regex.test($filling_name)) {
        $row.find(".name-error").html("Filling names are letters only").slideDown("fast");
      }
      if ($filling_price == "") {
        $row.find(".price-error").html("Please enter a filling price").slideDown("fast");
      } else if (!price_regex.test($filling_price)) {
        $row.find(".price-error").html("Please enter a valid filling price").slideDown("fast");
      }
    }
  });

  $("body").on('click', '.remove-filling-edit', function() {
    var $row           = $(this).closest("tr"),
        $filling_name  = $row.find("td:eq(1) input").data("original"),
        $filling_price = $row.find("td:eq(2) input").data("original");
    $row.find("td:eq(1)").html($filling_name);
    $row.find("td:eq(2)").html("&pound;" + $filling_price);
    $row.find("td:eq(3)").html('<button class="btn btn-primary btn-sm edit-filling"><span class="glyphicon glyphicon-pencil"></span></button>');
    $row.find("td:eq(4)").html('<button class="btn btn-danger btn-sm delete-filling"><span class="glyphicon glyphicon-trash"></span></button>');
  });

  $("body").on('click', '.delete-filling', function() {
    var $row        = $(this).closest("tr"),
        $filling_id = $row.data("fillingid");
    $.ajax({
      type: 'post',
      url: '../lib/edit-fillingdecor.php',
      data: {token: $token,
             type: 'filling',
             command: 'delete',
             id: $filling_id},
      success: function(response) {
        object = JSON.parse(response);
        $token = object.token;
        if (object.status == "success") {
          $row.remove();
          $("#success_message .alert").html("<span class='glyphicon glyphicon-ok-circle'></span>   Successfully deleted filling");
          $("#success_message").modal("show");
          setTimeout(function() {
            $("#success_message").modal("hide");
          }, 1500);
        } else {
          $("#error_message .alert").html("<span class='glyphicon glyphicon-remove-circle'></span>   " + object.error);
          $("#error_message").modal("show");
        }
      }
    });
  });

  $("body").on('click', '.add-new-filling', function() {
    var $row           = $(this).closest("tr");
        $filling_name  = $row.find("input[name=filling_name]").val(),
        $filling_price = $row.find("input[name=filling_price]").val(),
        name_regex     = /^[A-Za-z ]+$/,
        price_regex    = /^\d+(\.\d{2})?$/;
    if (name_regex.test($filling_name) && price_regex.test($filling_price)) {
      $row.find(".name-error").slideUp("fast");
      $row.find(".price-error").slideUp("fast");
      $.ajax({
        type: 'post',
        url: '../lib/edit-fillingdecor.php',
        data: {
          token: $token,
          type: 'filling',
          command: 'add',
          filling_name: $filling_name,
          filling_price: $filling_price
        },
        success: function(response) {
          var object = JSON.parse(response);
          $token = object.token;
          if (object.status == "success") {
            $("#success_message .alert").html("<span class='glyphicon glyphicon-ok-circle'></span>   Successfully added filling");
            $("#success_message").modal("show");
            $row.find("td:eq(1)").html($filling_name);
            $row.find("td:eq(2)").html("&pound;" + $filling_price);
            $row.find("td:eq(3)").html("<button class='btn btn-primary btn-sm edit-filling'><span class='glyphicon glyphicon-pencil'></span></button>");
            $row.find("td:eq(4)").html("<button class='btn btn-danger btn-sm delete-filling'><span class='glyphicon glyphicon-trash'></span></button>");
            setTimeout(function() {
              $("#success_message").modal("hide");
            }, 1500);
          } else {
            $("#error_message .alert").html("<span class='glyphicon glyphicon-remove-circle'></span>   " + object.error);
            $("#error_message").modal("show");
          }
        }
      });
    } else {
      if ($filling_name == "") {
        $row.find(".name-error").html("Please enter a filling name").slideDown("fast");
      } else if (!name_regex.test($filling_name)) {
        $row.find(".name-error").html("Filling names are letters only").slideDown("fast");
      }
      if ($filling_price == "") {
        $row.find(".price-error").html("Please enter a filling price").slideDown("fast");
      } else if (!price_regex.test($filling_price)) {
        $row.find(".price-error").html("Please enter a valid filling price").slideDown("fast");
      }
    }
  });

  $("#add-filling").click(function() {
    var $row        = $(this).closest("tr"),
        $filling_id = $("#fillings tbody tr:last").data("fillingid") + 1;
    $("#fillings tbody").append("<tr data-fillingid='" + $filling_id + "'>" +
                                "<td>" + $filling_id + "</td>" +
                                "<td><input name='filling_name' class='form-control new-input' placeholder='Filling name'><span class='validate-error name-error'></span></td>" +
                                "<td><span style='display:inline-block;'>&pound;</span><input name='filling_price' class='form-control new-input' style='display:inline-block;width:90%;margin-left:9px;' placeholder='Filling price'><span class='validate-error price-error'></span></td>" +
                                "<td><button class='btn btn-success btn-sm add-new-filling'><span class='glyphicon glyphicon-ok'></span>   Add</button></td>" +
                                "<td><button class='btn btn-danger btn-sm remove-new-filling'><span class='glyphicon glyphicon-remove'></span>   Cancel</button></td>" +
                                "</tr>");

    $(".remove-new-filling").click(function() {
      $(this).closest("tr").remove();
    });
  });

  $("body").on('click', '.edit-decor', function() {
    var $row         = $(this).closest("tr"),
        $decor_id    = $row.data("decorid"),
        $decor_name  = $row.find("td:eq(1)").html(),
        $decor_price = $row.find("td:eq(2)").html().replace(/[^\d]/g, "");
    $row.find("td:eq(1)").html("<input name='decor_name' class='form-control' value='" + $decor_name + "' data-original='" + $decor_name + "'><span class='validate-error name-error'></span>");
    $row.find("td:eq(2)").html("<span style='display:inline-block;'>&pound;</span><input name='decor_price' class='form-control' value='" + $decor_price + "' data-original='" + $decor_price + "' style='width:90%;margin-left:9px;display:inline-block;'><span class='validate-error price-error'></span>");
    $row.find("td:eq(3)").html('<button class="btn btn-success btn-sm submit-decor-edit"><span class="glyphicon glyphicon-ok"></span>   Edit</button>');
    $row.find("td:eq(4)").html('<button class="btn btn-danger btn-sm remove-decor-edit"><span class="glyphicon glyphicon-remove"></span>   Cancel</button>');
  });

  $("body").on('click', '.submit-decor-edit', function() {
    var $row         = $(this).closest("tr"),
        $decor_id    = $row.data("decorid"),
        $decor_name  = $row.find("td:eq(1) input").val(),
        $decor_price = $row.find("td:eq(2) input").val(),
        name_regex     = /^[A-Za-z ]+$/,
        price_regex    = /^\d+(\.\d{2})?$/;
    if (name_regex.test($decor_name) && price_regex.test($decor_price)) {
      $row.find(".name-error").slideUp("fast");
      $row.find(".price-error").slideUp("fast");
      $.ajax({
        type: 'post',
        url: '../lib/edit-fillingdecor.php',
        data: {
          token: $token,
          type: 'decoration',
          command: 'edit',
          id: $decor_id,
          decor_name: $decor_name,
          decor_price: $decor_price
        },
        success: function(response) {
          var object = JSON.parse(response);
          $token = object.token;
          if (object.status == "success") {
            $("#success_message .alert").html("<span class='glyphicon glyphicon-ok-circle'></span>   Successfully edited decoration");
            $("#success_message").modal("show");
            $row.find("td:eq(1)").html($decor_name);
            $row.find("td:eq(2)").html("&pound;" + $decor_price);
            $row.find("td:eq(3)").html("<button class='btn btn-primary btn-sm edit-decor'><span class='glyphicon glyphicon-pencil'></span></button>");
            $row.find("td:eq(4)").html("<button class='btn btn-danger btn-sm delete-decor'><span class='glyphicon glyphicon-trash'></span></button>");
            setTimeout(function() {
              $("#success_message").modal("hide");
            }, 1500);
          } else {
            $("#error_message .alert").html("<span class='glyphicon glyphicon-remove-circle'></span>   " + object.error);
            $("#error_message").modal("show");
          }
        }
      });
    } else {
      if ($decor_name == "") {
        $row.find(".name-error").html("Please enter a decoration name").slideDown("fast");
      } else if (!name_regex.test($decor_name)) {
        $row.find(".name-error").html("Decoration names are letters only").slideDown("fast");
      }
      if ($decor_price == "") {
        $row.find(".price-error").html("Please enter a decor price").slideDown("fast");
      } else if (!price_regex.test($decor_price)) {
        $row.find(".price-error").html("Please enter a valid decor price").slideDown("fast");
      }
    }
  });

  $("body").on('click', '.remove-decor-edit', function() {
    var $row         = $(this).closest("tr"),
        $decor_name  = $row.find("td:eq(1) input").data("original"),
        $decor_price = $row.find("td:eq(2) input").data("original");
    $row.find("td:eq(1)").html($decor_name);
    $row.find("td:eq(2)").html("&pound;" + $decor_price);
    $row.find("td:eq(3)").html('<button class="btn btn-primary btn-sm edit-decor"><span class="glyphicon glyphicon-pencil"></span></button>');
    $row.find("td:eq(4)").html('<button class="btn btn-danger btn-sm delete-decor"><span class="glyphicon glyphicon-trash"></span></button>');
  });

  $("body").on('click', '.delete-decor', function() {
    var $row      = $(this).closest("tr"),
        $decor_id = $row.data("decorid");
    $.ajax({
      type: 'post',
      url: '../lib/edit-fillingdecor.php',
      data: {token: $token,
             type: 'decoration',
             command: 'delete',
             id: $decor_id},
      success: function(response) {
        object = JSON.parse(response);
        if (object.status == "success") {
          $row.remove();
          $token = object.token;
          $("#success_message .alert").html("<span class='glyphicon glyphicon-ok-circle'></span>   Successfully deleted decoration");
          $("#success_message").modal("show");
          setTimeout(function() {
            $("#success_message").modal("hide");
          }, 1500);
        } else {
          $("#error_message .alert").html("<span class='glyphicon glyphicon-remove-circle'></span>   " + object.error);
          $("#error_message").modal("show");
        }
      }
    });
  });

  $("#add-decor").click(function() {
    var $row      = $(this).closest("tr"),
        $decor_id = $("#decorations tbody tr:last").data("decorid") + 1;
    $("#decorations tbody").append("<tr data-decorid='" + $decor_id + "'>" +
                                "<td>" + $decor_id + "</td>" +
                                "<td><input name='decor_name' class='form-control new-input' placeholder='Decoration name'><span class='validate-error name-error'></span></td>" +
                                "<td><span style='display:inline-block;'>&pound;</span><input name='decor_price' class='form-control new-input' style='display:inline-block;width:90%;margin-left:9px;' placeholder='Decoration price'><span class='validate-error price-error'></span></td>" +
                                "<td><button class='btn btn-success btn-sm add-new-decor'><span class='glyphicon glyphicon-ok'></span>   Add</button></td>" +
                                "<td><button class='btn btn-danger btn-sm remove-new-decor'><span class='glyphicon glyphicon-remove'></span>   Cancel</button></td>" +
                                "</tr>");
    $(".remove-new-decor").click(function() {
      $(this).closest("tr").remove();
    });
  });

  $("body").on('click', '.add-new-decor', function() {
    var $row         = $(this).closest("tr");
        $decor_name  = $row.find("input[name=decor_name]").val(),
        $decor_price = $row.find("input[name=decor_price]").val(),
        name_regex   = /^[A-Za-z ]+$/,
        price_regex  = /^\d+(\.\d{2})?$/;
    if (name_regex.test($decor_name) && price_regex.test($decor_price)) {
      $row.find(".name-error").slideUp("fast");
      $row.find(".price-error").slideUp("fast");
      $.ajax({
        type: 'post',
        url: '../lib/edit-fillingdecor.php',
        data: {
          token: $token,
          type: 'decoration',
          command: 'add',
          decor_name: $decor_name,
          decor_price: $decor_price
        },
        success: function(response) {
          var object = JSON.parse(response);
          $token = object.token;
          if (object.status == "success") {
            $("#success_message .alert").html("<span class='glyphicon glyphicon-ok-circle'></span>   Successfully added decoration");
            $("#success_message").modal("show");
            $row.find("td:eq(1)").html($decor_name);
            $row.find("td:eq(2)").html("&pound;" + $decor_price);
            $row.find("td:eq(3)").html("<button class='btn btn-primary btn-sm edit-decor'><span class='glyphicon glyphicon-pencil'></span></button>");
            $row.find("td:eq(4)").html("<button class='btn btn-danger btn-sm delete-decor'><span class='glyphicon glyphicon-trash'></span></button>");
            setTimeout(function() {
              $("#success_message").modal("hide");
            }, 1500);
          } else {
            $("#error_message .alert").html("<span class='glyphicon glyphicon-remove-circle'></span>   " + object.error);
            $("#error_message").modal("show");
          }
        }
      });
    } else {
      if ($decor_name == "") {
        $row.find(".name-error").html("Please enter a decoration name").slideDown("fast");
      } else if (!name_regex.test($decor_name)) {
        $row.find(".name-error").html("Decoration names are letters only").slideDown("fast");
      }
      if ($decor_price == "") {
        $row.find(".price-error").html("Please enter a decoration price").slideDown("fast");
      } else if (!price_regex.test($decor_price)) {
        $row.find(".price-error").html("Please enter a valid decoration price").slideDown("fast");
      }
    }
  });

  $(".edit-cake-type").click(function() {
    //TODO: Cake type edit
    $caketype_id = $(this).closest("tr").data("caketypeid");
    console.log("Editing cake type " + $caketype_id);
  });

  $(".delete-cake-type").click(function() {
    //TODO: Delete cake type
    $caketype_id = $(this).closest("tr").data("caketypeid");
    console.log("Deleting cake type " + $caketype_id);
  });

  $("#add-cake-type").click(function() {
    //TODO: Add cake type
    console.log("Adding new cake type");
  });
});
