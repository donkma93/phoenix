function updateNotification() {
    $.ajax({
        type: "GET",
        url: "/staff/notification",
        success: function (data) {
            $(".notification_unread").text(data.request);
            $("#notification-new").text(data.request);
            $(".message_unread").text(data.message);
            $("#message-new").text(data.message);
        },
    });
}
$(document).ready(function () {
    updateNotification();

    setInterval(function () {
        updateNotification();
    }, 10000);
});
