function updateNotification() {
    $.ajax({
        type: "GET",
        url: "/admin/notification",
        success: function (data) {
            $(".notification_unread").text(data.request);
            $("#notification-new").text(data.request);
        },
    });
}
$(document).ready(function () {
    updateNotification();
    
    setInterval(function () {
        updateNotification();
    }, 10000);
});
