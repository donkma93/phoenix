/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*******************************************!*\
  !*** ./resources/js/user_notification.js ***!
  \*******************************************/
function fetchRequestDone() {
  $.ajax({
    type: 'GET',
    url: '/requests/notify',
    success: function success(data) {
      $(".noti-diplay").html('');
      var total = data ? data.length : 0;
      var maxDisplay = 5;

      if (total) {
        for (var i = 0; i < total; i++) {
          if (i >= maxDisplay) {
            $(".noti-diplay").append("<a class=\"dropdown-item\" href=\"#\">\n                                You have ".concat(total - maxDisplay, " other new notification.\n                            </div>"));
            break;
          }

          $("#notification_unread_total").text(total);
          $(".noti-diplay").append("<a class=\"dropdown-item\" href=\"/requests/notify/".concat(data[i]['id'], "\">\n                            Request ").concat(data[i]['data']['type'], " has been done\n                        </a>\n                        <div class=\"dropdown-divider\"></div>"));
        }
      } else {
        $("#notification_unread_total").text();
        $(".noti-diplay").append("<div class=\"text-nowrap ap-8\">\n                    You have no new notification!\n                </div>");
      }
    }
  });
}

$(document).ready(function () {
  fetchRequestDone();
  setInterval(function () {
    fetchRequestDone();
  }, 60000);
});
/******/ })()
;