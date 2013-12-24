/**
 * tablePagination
 * - A small piece of JS code which does simple table pagination.
 *   The original is based on Ryan Zielke's tablePagination (http://neoalchemy.org/tablePagination.html)
 *   which is licensed under the MIT licenses: http://www.opensource.org/licenses/mit-license.php
 *   Button designs are based on Google+ Buttons in CSS design from Pixify
 *   (http://pixify.com/blog/use-google-plus-to-improve-your-ui/).
 *
 * - This script was modified from Chun Lin's oneSimpleTablePagination
 *   (https://code.google.com/p/one-simple-table-paging/) for better use with the Star
 *   Dream Cakes website.
 *
 *
 * @author Ivan Brazza
 * @name tablePagination
 * @type jQuery
 * @param Object userConfigurations:
 *        rowsPerPage - Number - used to determine the starting rows per page. Default: 10
 *        topNav - Boolean - This specifies the desire to have the navigation be a top nav bar
 * @requires jQuery v1.2.3 or above
 */


$.prototype.extend(
  {
    'tablePagination': function(userConfigurations) {
      var defaults = {
        rowsPerPage : 10,
        topNav : false
      };
      defaults = $.extend(defaults, userConfigurations);
      
      return this.each(function() {
        var table = $(this)[0],
            currPageId = '#tablePagination_currPage',
            tblLocation = (defaults.topNav) ? "prev" : "next",
            tableRows = $.makeArray($('tbody tr', table)),
            totalPages = countNumberOfPages(tableRows.length),
            currPageNumber = 1;

        function hideOtherPages(pageNum) {
          var intRegex = /^\d+$/;
          if (!intRegex.test(pageNum) || pageNum < 1 || pageNum > totalPages)
            return;
          var startIndex = (pageNum - 1) * defaults.rowsPerPage,
              endIndex = (startIndex + defaults.rowsPerPage - 1);
          $(tableRows).show();
          for (var i = 0; i < tableRows.length; i++) {
            if (i < startIndex || i > endIndex) {
              $(tableRows[i]).hide();
            }
          }
        }

        function countNumberOfPages(numRows) {
          var preTotalPages = Math.round(numRows / defaults.rowsPerPage),
              totalPages = (preTotalPages * defaults.rowsPerPage < numRows) ? preTotalPages + 1 : preTotalPages;
          return totalPages;
        }

        function resetCurrentPage(currPageNum) {
          var intRegex = /^\d+$/;
          if (!intRegex.test(currPageNum) || currPageNum < 1 || currPageNum > totalPages)
            return;
          currPageNumber = currPageNum;
          hideOtherPages(currPageNumber);
          $(table)[tblLocation]().find(currPageId).html(currPageNumber);
        }

        function createPaginationElements() {
          var paginationHTML = "";
          paginationHTML += "<div id='tablePagination' style='text-align: center; margin-top: 15px; padding-bottom: 25px;'>";
          paginationHTML += "<a id='tablePagination_prevPage' href='javascript:;' class='button right'>&lt;</a>";
          paginationHTML += "Page <span id='tablePagination_currPage'>" + currPageNumber + "</span> of " + totalPages + "&nbsp;&nbsp;&nbsp;";
          paginationHTML += "<a id='tablePagination_nextPage' href='javascript:;' class='button left'>&gt;</a>";
          paginationHTML += "</div>";
          return paginationHTML;
        }

        $(this).before("<style type='text/css'>a.button {color: #023042;font: bold 12px 'Open Sans', sans-serif;text-decoration: none;padding: 3px 7px;position: relative;display: inline-block;text-shadow: 0 1px 0 #fff;-webkit-transition: border-color .218s;-moz-transition: border .218s;-o-transition: border-color .218s;transition: border-color .218s;background: #d0edeb;border: solid 1px #023042;border-radius: 2px;-webkit-border-radius: 2px;-moz-border-radius: 2px;margin-right: 10px;}a.button:hover {color: #247FCA;border-color: #247FCA;-moz-box-shadow: 0 2px 0 rgba(0, 0, 0, 0.2) -webkit-box-shadow:0 2px 5px rgba(0, 0, 0, 0.2);box-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);}a.button:active {color: #000;border-color: #444;}a.left {margin: 0;}a.right:hover { border-left: solid 1px #999 }</style>");

        if (defaults.topNav && totalPages > 1) {
          $(this).before(createPaginationElements());
        } else if (totalPages > 1) {
          $(this).after(createPaginationElements());
        }

        hideOtherPages(currPageNumber);

        $(table)[tblLocation]().find('#tablePagination_prevPage').click(function (e) {
          resetCurrentPage(parseInt(currPageNumber) - 1);
        });

        $(table)[tblLocation]().find('#tablePagination_nextPage').click(function (e) {
          resetCurrentPage(parseInt(currPageNumber) + 1);
        });
      })
    }
  })
