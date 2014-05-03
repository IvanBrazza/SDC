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
$.prototype.extend({tablePagination:function(e){var t={rowsPerPage:10,topNav:false};t=$.extend(t,e);return this.each(function(){function u(e){var n=/^\d+$/;if(!n.test(e)||e<1||e>s)return;var r=(e-1)*t.rowsPerPage,o=r+t.rowsPerPage-1;$(i).show();for(var u=0;u<i.length;u++){if(u<r||u>o){$(i[u]).hide()}}}function a(e){var n=Math.round(e/t.rowsPerPage),r=n*t.rowsPerPage<e?n+1:n;return r}function f(t){var i=/^\d+$/;if(!i.test(t)||t<1||t>s)return;o=t;u(o);$(e)[r]().find(n).html(o)}function l(){var e="";e+="<div id='tablePagination' style='text-align: center; margin-top: 15px; padding-bottom: 25px;'>";e+="<ul class='pager'>";e+="<li class='previous'><a id='tablePagination_prevPage' href='javascript:;' class='button right'>&larr; Newer</a></li>";e+="Page <span id='tablePagination_currPage'>"+o+"</span> of "+s+"&nbsp;&nbsp;&nbsp;";e+="<li class='next'><a id='tablePagination_nextPage' href='javascript:;' class='button left'>Older &rarr;</a></li>";e+="</ul>";e+="</div>";return e}var e=$(this)[0],n="#tablePagination_currPage",r=t.topNav?"prev":"next",i=$.makeArray($("tbody tr",e)),s=a(i.length),o=1;$(this).before("<style type='text/css'>a.button {color: #023042;font: bold 12px 'Open Sans', sans-serif;text-decoration: none;padding: 3px 7px;position: relative;display: inline-block;text-shadow: 0 1px 0 #fff;-webkit-transition: border-color .218s;-moz-transition: border .218s;-o-transition: border-color .218s;transition: border-color .218s;background: #d0edeb;border: solid 1px #023042;border-radius: 2px;-webkit-border-radius: 2px;-moz-border-radius: 2px;margin-right: 10px;}a.button:hover {color: #247FCA;border-color: #247FCA;-moz-box-shadow: 0 2px 0 rgba(0, 0, 0, 0.2) -webkit-box-shadow:0 2px 5px rgba(0, 0, 0, 0.2);box-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);}a.button:active {color: #000;border-color: #444;}a.left {margin: 0;}a.right:hover { border-left: solid 1px #999 }</style>");if(t.topNav&&s>1){$(this).before(l())}else if(s>1){$(this).after(l())}u(o);$(e)[r]().find("#tablePagination_prevPage").click(function(e){f(parseInt(o)-1)});$(e)[r]().find("#tablePagination_nextPage").click(function(e){f(parseInt(o)+1)})})}})