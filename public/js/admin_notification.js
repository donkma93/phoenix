/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!********************************************!*\
  !*** ./resources/js/admin_notification.js ***!
  \********************************************/
function updateNotification() {
  $.ajax({
    type: "GET",
    url: "/admin/notification",
    success: function success(data) {
      $(".notification_unread").text(data.request);
      $("#notification-new").text(data.request);
    }
  });
}

$(document).ready(function () {
  updateNotification();
  setInterval(function () {
    updateNotification();
  }, 10000);
});
/******/ })()
;