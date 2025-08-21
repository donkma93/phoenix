function fetchRequestDone() {
    $.ajax({
        type: 'GET',
        url: '/requests/notify',
        success:function(data)
        {
            $(".noti-diplay").html('');

            let total = data ? data.length : 0;
            let maxDisplay = 5;

            if (total) {
                for (let i = 0; i < total; i++) {
                    if (i >= maxDisplay) {
                        $(".noti-diplay").append(`<a class="dropdown-item" href="#">
                                You have ${total - maxDisplay} other new notification.
                            </div>`);
                        break;
                    }

                    $("#notification_unread_total").text(total);
                    $(".noti-diplay").append(`<a class="dropdown-item" href="/requests/notify/${data[i]['id']}">
                            Request ${data[i]['data']['type']} has been done
                        </a>
                        <div class="dropdown-divider"></div>`);

                }
            } else {
                $("#notification_unread_total").text();
                $(".noti-diplay").append(`<div class="text-nowrap ap-8">
                    You have no new notification!
                </div>`);
            }
        }
    });
}
$(document).ready(function() {
    fetchRequestDone();

    setInterval(function() {
        fetchRequestDone();
    }, 60000);
});
