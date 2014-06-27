$(document).ready(function() {
  $('body').scrollspy({
    target: '.scrollspy',
    offset: 100
  });
  $(".scrollspy").on('activate.bs.scrollspy', function () {
    var $active  = $(this).find("li.active .nested-scrollspy");
    $(this).find(".nested-scrollspy").slideUp();
    if ($active.index() > 0) {
      $active.stop().slideDown();
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
        try {
          var object = JSON.parse(response);
          if (object.status == "success") {
            window.location.href = object.redirect;
          } else if (object.status == "error") {
            switch (object.code) {
              case "002":
                $("input[customer_name]").closest(".form-group").removeClass("has-success").addClass("has-error");
                break;
            }
            $("#search_error_message").html("<i class='fa fa-times-circle'></i>" + object.error).show();
            if (object.code != "001") {
              $("input[name=token]").val(object.token);
              $token = object.token;
            }
          }
        } catch(error) {
          $("#error_modal .alert").html("<i class='fa fa-times-circle'></i>   Oops! Something went wrong. Try again<br>" +
            "<b>Error: " + error.message + "</b>");
          $("#error_modal").modal("show");
          setTimeout(function() {
            $("#error_modal").modal("hide");
          }, 1500);
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
    $row.find("td:eq(3) button").switchClass("btn-primary", "btn-success").switchClass("edit-filling", "submit-filling-edit")
        .animate({width: "58px"}, {duration: 300, queue: false}).append("   Edit")
        .find("span").switchClass("fa-pencil", "fa-check");
    $row.find("td:eq(4) button").switchClass("delete-filling", "remove-filling-edit")
        .animate({width: "75px"}, {duration: 300, queue: false}).append("   Cancel")
        .find("span").switchClass("fa-trash-o", "fa-times");
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
          try {
            var object = JSON.parse(response);
            $token = object.token;
            if (object.status == "success") {
              $("#success_modal .alert").html("<i class='fa fa-check-circle'></i>   Successfully edited filling");
              $("#success_modal").modal("show");
              $row.find("td:eq(1)").html($filling_name);
              $row.find("td:eq(2)").html("&pound;" + $filling_price);
              $row.find("td:eq(3)").html("<button class='btn btn-primary btn-sm edit-filling'><i class='fa fa-pencil'></i></button>");
              $row.find("td:eq(4)").html("<button class='btn btn-danger btn-sm delete-filling'><i class='fa fa-trash-o'></i></button>");
              setTimeout(function() {
                $("#success_modal").modal("hide");
              }, 1500);
            } else {
              $("#error_modal .alert").html("<i class='fa fa-times-circle'></i>   " + object.error);
              $("#error_modal").modal("show");
            }
          } catch(error) {
            $("#error_modal .alert").html("<i class='fa fa-times-circle'></i>   Oops! Something went wrong. Try again<br>" +
              "<b>Error: " + error.message + "</b>");
            $("#error_modal").modal("show");
            setTimeout(function() {
              $("#error_modal").modal("hide");
            }, 1500);
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
    $row.find("td:eq(3) button").switchClass("btn-success", "btn-primary").switchClass("submit-filling-edit", "edit-filling")
        .animate({width: "34px"}, {duration: 300, queue: false}).empty()
        .append('<i class="fa fa-pencil"></i>');
    $row.find("td:eq(4) button").switchClass("remove-filling-edit", "delete-filling")
        .animate({width: "34px"}, {duration: 300, queue: false}).empty()
        .append('<i class="fa fa-trash-o"></i>');
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
        try {
          object = JSON.parse(response);
          $token = object.token;
          if (object.status == "success") {
            $row.find("td").wrapInner('<div style="display: block;" />')
            .parent().find("td > div")
            .slideUp("fast", function() {
              $(this).parent().parent().remove();
            });;
            $("#success_modal .alert").html("<i class='fa fa-check-circle'></i>   Successfully deleted filling");
            $("#success_modal").modal("show");
            setTimeout(function() {
              $("#success_modal").modal("hide");
            }, 1500);
          } else {
            $("#error_modal .alert").html("<i class='fa fa-times-circle'></i>   " + object.error);
            $("#error_modal").modal("show");
          }
        } catch(error) {
          $("#error_modal .alert").html("<i class='fa fa-times-circle'></i>   Oops! Something went wrong. Try again<br>" +
            "<b>Error: " + error.message + "</b>");
          $("#error_modal").modal("show");
          setTimeout(function() {
            $("#error_modal").modal("hide");
          }, 1500);
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
          try {
            var object = JSON.parse(response);
            $token = object.token;
            if (object.status == "success") {
              $("#success_modal .alert").html("<i class='fa fa-check-circle'></i>   Successfully added filling");
              $("#success_modal").modal("show");
              $row.find("td:eq(1)").html($filling_name);
              $row.find("td:eq(2)").html("&pound;" + $filling_price);
              $row.find("td:eq(3)").html("<button class='btn btn-primary btn-sm edit-filling'><i class='fa fa-pencil'></i></button>");
              $row.find("td:eq(4)").html("<button class='btn btn-danger btn-sm delete-filling'><i class='fa fa-trash-o'></i></button>");
              setTimeout(function() {
                $("#success_modal").modal("hide");
              }, 1500);
            } else {
              $("#error_modal .alert").html("<i class='fa fa-times-circle'></i>   " + object.error);
              $("#error_modal").modal("show");
            }
          } catch(error) {
            $("#error_modal .alert").html("<i class='fa fa-times-circle'></i>   Oops! Something went wrong. Try again<br>" +
              "<b>Error: " + error.message + "</b>");
            $("#error_modal").modal("show");
            setTimeout(function() {
              $("#error_modal").modal("hide");
            }, 1500);
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
    $("<tr data-fillingid='" + $filling_id + "'>" +
      "<td>" + $filling_id + "</td>" +
      "<td><input name='filling_name' class='form-control new-input' placeholder='Filling name'><i class='validate-error name-error'></i></td>" +
      "<td><i style='display:inline-block;'>&pound;</i><input name='filling_price' class='form-control new-input' style='display:inline-block;width:90%;margin-left:9px;' placeholder='Filling price'><i class='validate-error price-error'></i></td>" +
      "<td><button class='btn btn-success btn-sm add-new-filling'><i class='fa fa-check'></i>   Add</button></td>" +
      "<td><button class='btn btn-danger btn-sm remove-new-filling'><i class='fa fa-times'></i>   Cancel</button></td>" +
      "</tr>")
    .appendTo($("#fillings tbody"))
    .find("td").wrapInner('<div style="display: none;" />')
    .parent().find("td > div")
    .slideDown("fast");

    $(".remove-new-filling").click(function() {
      $(this).closest("tr")
      .find("td").wrapInner('<div style="display: block;" />')
      .parent().find("td > div")
      .slideUp("fast", function() {
        $(this).parent().parent().remove();
      });
    });
  });

  $("body").on('click', '.edit-decor', function() {
    var $row         = $(this).closest("tr"),
        $decor_id    = $row.data("decorid"),
        $decor_name  = $row.find("td:eq(1)").html(),
        $decor_price = $row.find("td:eq(2)").html().replace(/[^\d]/g, "");
    $row.find("td:eq(1)").html("<input name='decor_name' class='form-control' value='" + $decor_name + "' data-original='" + $decor_name + "'><i class='validate-error name-error'></i>");
    $row.find("td:eq(2)").html("<i style='display:inline-block;'>&pound;</i><input name='decor_price' class='form-control' value='" + $decor_price + "' data-original='" + $decor_price + "' style='width:90%;margin-left:9px;display:inline-block;'><i class='validate-error price-error'></i>");
    $row.find("td:eq(3) button").switchClass("btn-primary", "btn-success").switchClass("edit-decor", "submit-decor-edit")
        .animate({width: "58px"}, {duration: 300, queue: false}).append("   Edit")
        .find("span").switchClass("fa-pencil", "fa-check");
    $row.find("td:eq(4) button").switchClass("delete-decor", "remove-decor-edit")
        .animate({width: "75px"}, {duration: 300, queue: false}).append("   Cancel")
        .find("span").switchClass("fa-trash-o", "fa-times");
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
          try {
            var object = JSON.parse(response);
            $token = object.token;
            if (object.status == "success") {
              $("#success_modal .alert").html("<i class='fa fa-check-circle'></i>   Successfully edited decoration");
              $("#success_modal").modal("show");
              $row.find("td:eq(1)").html($decor_name);
              $row.find("td:eq(2)").html("&pound;" + $decor_price);
              $row.find("td:eq(3)").html("<button class='btn btn-primary btn-sm edit-decor'><i class='fa fa-pencil'></i></button>");
              $row.find("td:eq(4)").html("<button class='btn btn-danger btn-sm delete-decor'><i class='fa fa-trash-o'></i></button>");
              setTimeout(function() {
                $("#success_modal").modal("hide");
              }, 1500);
            } else {
              $("#error_modal .alert").html("<i class='fa fa-times-circle'></i>   " + object.error);
              $("#error_modal").modal("show");
            }
          } catch(error) {
            $("#error_modal .alert").html("<i class='fa fa-times-circle'></i>   Oops! Something went wrong. Try again<br>" +
              "<b>Error: " + error.message + "</b>");
            $("#error_modal").modal("show");
            setTimeout(function() {
              $("#error_modal").modal("hide");
            }, 1500);
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
    $row.find("td:eq(3) button").switchClass("btn-success", "btn-primary").switchClass("submit-decor-edit", "edit-decor")
        .animate({width: "34px"}, {duration: 300, queue: false}).empty()
        .append('<i class="fa fa-pencil"></i>');
    $row.find("td:eq(4) button").switchClass("remove-decor-edit", "delete-decor")
        .animate({width: "34px"}, {duration: 300, queue: false}).empty()
        .append('<i class="fa fa-trash-o"></i>');
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
        try {
          object = JSON.parse(response);
          if (object.status == "success") {
            $row.find("td").wrapInner('<div style="display: block;" />')
            .parent().find("td > div")
            .slideUp("fast", function() {
              $(this).parent().parent().remove();
            });;
            $token = object.token;
            $("#success_modal .alert").html("<i class='fa fa-check-circle'></i>   Successfully deleted decoration");
            $("#success_modal").modal("show");
            setTimeout(function() {
              $("#success_modal").modal("hide");
            }, 1500);
          } else {
            $("#error_modal .alert").html("<i class='fa fa-times-circle'></i>   " + object.error);
            $("#error_modal").modal("show");
          }
        } catch(error) {
          $("#error_modal .alert").html("<i class='fa fa-times-circle'></i>   Oops! Something went wrong. Try again<br>" +
            "<b>Error: " + error.message + "</b>");
          $("#error_modal").modal("show");
          setTimeout(function() {
            $("#error_modal").modal("hide");
          }, 1500);
        }
      }
    });
  });

  $("#add-decor").click(function() {
    var $row      = $(this).closest("tr"),
        $decor_id = $("#decorations tbody tr:last").data("decorid") + 1;
    $("<tr data-decorid='" + $decor_id + "'>" +
      "<td>" + $decor_id + "</td>" +
      "<td><input name='decor_name' class='form-control new-input' placeholder='Decoration name'><span class='validate-error name-error'></span></td>" +
      "<td><span style='display:inline-block;'>&pound;</span><input name='decor_price' class='form-control new-input' style='display:inline-block;width:90%;margin-left:9px;' placeholder='Decoration price'><span class='validate-error price-error'></span></td>" +
      "<td><button class='btn btn-success btn-sm add-new-decor'><i class='fa fa-check'></i>   Add</button></td>" +
      "<td><button class='btn btn-danger btn-sm remove-new-decor'><i class='fa fa-times'></i>   Cancel</button></td>" +
      "</tr>")
    .appendTo($("#decorations tbody"))
    .find("td").wrapInner('<div style="display: none;" />')
    .parent().find("td > div")
    .slideDown("fast");

    $(".remove-new-decor").click(function() {
      $(this).closest("tr")
      .find("td").wrapInner('<div style="display: block;" />')
      .parent().find("td > div")
      .slideUp("fast", function() {
        $(this).parent().parent().remove();
      });
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
          try {
            var object = JSON.parse(response);
            $token = object.token;
            if (object.status == "success") {
              $("#success_modal .alert").html("<i class='fa fa-check-circle'></i>   Successfully added decoration");
              $("#success_modal").modal("show");
              $row.find("td:eq(1)").html($decor_name);
              $row.find("td:eq(2)").html("&pound;" + $decor_price);
              $row.find("td:eq(3)").html("<button class='btn btn-primary btn-sm edit-decor'><i class='fa fa-pencil'></i></button>");
              $row.find("td:eq(4)").html("<button class='btn btn-danger btn-sm delete-decor'><i class='fa fa-trash-o'></i></button>");
              setTimeout(function() {
                $("#success_modal").modal("hide");
              }, 1500);
            } else {
              $("#error_modal .alert").html("<i class='fa fa-times-circle'></i>   " + object.error);
              $("#error_modal").modal("show");
            }
          } catch(error) {
            $("#error_modal .alert").html("<i class='fa fa-times-circle'></i>   Oops! Something went wrong. Try again<br>" +
              "<b>Error: " + error.message + "</b>");
            $("#error_modal").modal("show");
            setTimeout(function() {
              $("#error_modal").modal("hide");
            }, 1500);
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

  $("#add-gallery").click(function() {
    var $row        = $(this).closest("tr"),
        $gallery_id = $("#gallery tbody tr:last").data("galleryid") + 1;
    $("<tr data-galleryid='" + $gallery_id + "'>" +
      "<td>" + $gallery_id + "</td>" +
      "<td><input name='gallery_name' class='form-control new-input' placeholder='Gallery name'><span class='validate-error name-error'></span></td>" +
      "<td><button class='btn btn-success btn-sm add-new-gallery'><i class='fa fa-check'></i>   Add</button></td>" +
      "<td><button class='btn btn-danger btn-sm remove-new-gallery'><i class='fa fa-times'></i>   Cancel</button></td>" +
      "</tr>")
    .appendTo($("#gallery tbody"))
    .find("td").wrapInner('<div style="display: none;" />')
    .parent().find("td > div")
    .slideDown("fast");

    $(".remove-new-gallery").click(function() {
      $(this).closest("tr")
      .find("td").wrapInner('<div style="display: block;" />')
      .parent().find("td > div")
      .slideUp("fast", function() {
        $(this).parent().parent().remove();
      });
    });
  });

  $("body").on('click', '.add-new-gallery', function() {
    var $row           = $(this).closest("tr");
        $gallery_name  = $row.find("input[name=gallery_name]").val(),
        name_regex     = /^[A-Za-z ]+$/;
    if (name_regex.test($gallery_name)) {
      $row.find(".name-error").slideUp("fast");
      $.ajax({
        type: 'post',
        url: '../lib/edit-gallery.php',
        data: {
          token: $token,
          command: 'add',
          gallery_name: $gallery_name
        },
        success: function(response) {
          try {
            var object = JSON.parse(response);
            $token = object.token;
            if (object.status == "success") {
              $("#success_modal .alert").html("<i class='fa fa-check-circle'></i>   Successfully added gallery");
              $("#success_modal").modal("show");
              $row.find("td:eq(1)").html($gallery_name);
              $row.find("td:eq(2)").html("<button class='btn btn-primary btn-sm edit-gallery'><i class='fa fa-pencil'></i></button>");
              $row.find("td:eq(3)").html("<button class='btn btn-danger btn-sm delete-gallery'><i class='fa fa-trash-o'></i></button>");
              setTimeout(function() {
                $("#success_modal").modal("hide");
              }, 1500);
            } else {
              $("#error_modal .alert").html("<i class='fa fa-times-circle'></i>   " + object.error);
              $("#error_modal").modal("show");
            }
          } catch(error) {
            $("#error_modal .alert").html("<i class='fa fa-times-circle'></i>   Oops! Something went wrong. Try again<br>" +
              "<b>Error: " + error.message + "</b>");
            $("#error_modal").modal("show");
            setTimeout(function() {
              $("#error_modal").modal("hide");
            }, 1500);
          }
        }
      });
    } else {
      if ($gallery_name == "") {
        $row.find(".name-error").html("Please enter a decoration name").slideDown("fast");
      } else if (!name_regex.test($gallery_name)) {
        $row.find(".name-error").html("Gallery names are letters only").slideDown("fast");
      }
    }
  });

  $("body").on('click', '.delete-gallery', function() {
    var $row        = $(this).closest("tr"),
        $gallery_id = $row.data("galleryid");
    $.ajax({
      type: 'post',
      url: '../lib/edit-gallery.php',
      data: {token: $token,
             command: 'delete',
             id: $gallery_id},
      success: function(response) {
        try {
          object = JSON.parse(response);
          $token = object.token;
          if (object.status == "success") {
            $row.remove();
            $token = object.token;
            $("#success_modal .alert").html("<i class='fa fa-check-circle'></i>   Successfully deleted gallery");
            $("#success_modal").modal("show");
            setTimeout(function() {
              $("#success_modal").modal("hide");
            }, 1500);
          } else {
            $("#error_modal .alert").html("<i class='fa fa-times-circle'></i>   " + object.error);
            $("#error_modal").modal("show");
          }
        } catch(error) {
          $("#error_modal .alert").html("<i class='fa fa-times-circle'></i>   Oops! Something went wrong. Try again<br>" +
            "<b>Error: " + error.message + "</b>");
          $("#error_modal").modal("show");
          setTimeout(function() {
            $("#error_modal").modal("hide");
          }, 1500);
        }
      }
    });
  });

  $(".gallery_container").each(function() {
    var $li = $(this).find("li"),
        $container = $(this);
    $li.wookmark({
      autoResize: true,
      container: $container,
      itemWidth: 200,
      align: "center",
      offset: 3,
      verticalOffset: 10,
      fillEmptySpace: false
    });
  });

  $("body").on('click', '.edit-gallery', function() {
    var $row        = $(this).closest("tr"),
        $gallery_id = $row.data("galleryid");

    $("#gallery_modal_" + $gallery_id).modal("show").on('shown.bs.modal', function(e) {
      $li = $(this).find("li");
      $(this).find("img.lazy").unveil(1, function() {
        $li.trigger("refreshWookmark");
      }).trigger("unveil");
    });
  });
  
  $("body").on('click', '.delete-image', function() {
    var $gallery_id = $(this).data("gallery"),
        $image_name = $(this).data("image"),
        $img        = $(this).siblings("img"),
        $li         = $(this).closest("li")
        $container  = $(this).closest(".gallery_container");
    $.ajax({
      type: 'post',
      url: '../lib/edit-gallery.php',
      data: {
        token: $token,
        command: 'delete-image',
        gallery_id: $gallery_id,
        image: $image_name
      },
      success: function(response) {
        try {
          object = JSON.parse(response);
          $token = object.token;
          if (object.status == "success") {
            $img.fadeOut(function() {
              $li.remove();
              $container.find("li").wookmark({
                autoResize: true,
                container: $container,
                itemWidth: 200,
                align: "center",
                offset: 3,
                verticalOffset: 10,
                fillEmptySpace: false
              });
            });
          } else {
            $("#error_modal .alert").html("<i class='fa fa-times-circle'></i>   " + object.error);
            $("#error_modal").modal("show");
          }
        } catch(error) {
          $("#error_modal .alert").html("<i class='fa fa-times-circle'></i>   Oops! Something went wrong. Try again<br>" +
            "<b>Error: " + error.message + "</b>");
          $("#error_modal").modal("show");
          setTimeout(function() {
            $("#error_modal").modal("hide");
          }, 1500);
        }
      }
    });
  });

  $(".fileupload").each(function() {
    $(this).fileupload({
      autoUpload: true,
      dropZone: $(this),
      url: '../lib/form/fileuploads3.php',
      maxFileSize: 5000000,
      acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
      previewMaxWidth: 150,
      previewMaxHeight: 150,
      previewMinWidth: 150,
      sequentialUploads: true,
      filesContainer: $(this).find(".gallery_container ul")
    })
    .bind('fileuploadadd', function (e, data) {
      var $container = $(this).find(".gallery_container");
      $container.find("li").wookmark({
        autoResize: true,
        container: $container,
        itemWidth: 200,
        align: "center",
        offset: 3,
        verticalOffset: 10,
        fillEmptySpace: false
      });
    })
    .bind('fileuploaddone', function (e, data) {
      var $gallery_id = $(this).find("input[name=upload_dir]").val(),
          $image      = data.result.files[0].name,
          $container  = $(this).find(".gallery_container");
      $.ajax({
        type: 'post',
        url: '../lib/edit-gallery.php',
        data: {
          token: $token,
          command: 'add-image',
          id: $gallery_id,
          image: $image
        },
        success: function(response) {
          try {
            var object = JSON.parse(response);
            if (object.status == "success") {
              $token = object.token;
              setTimeout(function() {
                $("img.new_lazy").unveil().trigger("unveil");
                $("img.new_lazy").imagesLoaded(function() {
                  $container.find("li").wookmark({
                    autoResize: true,
                    container: $container,
                    itemWidth: 200,
                    align: "center",
                    offset: 3,
                    verticalOffset: 10,
                    fillEmptySpace: false
                  });
                });
              }, 500);
            }
          } catch(error) {
            $("#error_modal .alert").html("<i class='fa fa-times-circle'></i>   Oops! Something went wrong. Try again<br>" +
              "<b>Error: " + error.message + "</b>");
            $("#error_modal").modal("show");
            setTimeout(function() {
              $("#error_modal").modal("hide");
            }, 1500);
          }
        }
      });
    });
  });

  $("body").on('mouseenter', '.gallery-thumb', function() {
    $(this).find(".delete-image").stop().fadeIn();
  });
  $("body").on('mouseleave', '.gallery-thumb', function() {
    $(this).find(".delete-image").stop().fadeOut();
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
