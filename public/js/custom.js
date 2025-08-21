// Tạo hộp thoại gợi ý cho ô input
function createSuggestBlock(inp, arr, id) {
    let i = 0,
        len = arr.length,
        dl = document.createElement("datalist");

    dl.id = id;
    for (; i < len; i += 1) {
        var option = document.createElement("option");
        option.value = arr[i];
        dl.appendChild(option);
    }
    inp.appendChild(dl);
}

// Tạo hiệu ứng loading...
function loading() {
    var isLoading =
        arguments.length > 0 && arguments[0] !== undefined
            ? arguments[0]
            : true;

    if (isLoading) {
        $("#loading").show();
        $("body").addClass("overflow-hidden");
    } else {
        $("#loading").hide();
        $("body").removeClass("overflow-hidden");
    }
}

// Xử lý ô chọn Select all input
$("#select_all_order").on("change", function () {
    if ($(this).prop("checked") === true) {
        $(".select_item_order").each(function () {
            $(this).prop("checked", true);
        });
    } else {
        $(".select_item_order").each(function () {
            $(this).prop("checked", false);
        });
    }

    $(".select_item_order").click(function () {
        let is_check_all = true;
        $(".select_item_order").each(function () {
            if ($(this).prop("checked") === false) {
                is_check_all = false;
            }
        });

        if (is_check_all) {
            $("#select_all_order").prop("checked", true);
        } else {
            $("#select_all_order").prop("checked", false);
        }
    });
});

// Load text-color and background-color mặc định nếu có
$(document).ready(function () {
    $sidebar = $(".sidebar");
    $full_page = $(".full-page");
    $sidebar_responsive = $("body > .navbar-collapse");

    let textColor = localStorage.getItem("text-color");
    let backgroundColor = localStorage.getItem("background-color");

    if (!!textColor) {
        if ($sidebar.length != 0) {
            $sidebar.attr("data-active-color", textColor);
        }
        if ($full_page.length != 0) {
            $full_page.attr("data-active-color", textColor);
        }
        if ($sidebar_responsive.length != 0) {
            $sidebar_responsive.attr("data-active-color", textColor);
        }
    }

    if (!!backgroundColor) {
        if ($sidebar.length != 0) {
            $sidebar.attr("data-color", backgroundColor);
        }
        if ($full_page.length != 0) {
            $full_page.attr("filter-color", backgroundColor);
        }
        if ($sidebar_responsive.length != 0) {
            $sidebar_responsive.attr("data-color", backgroundColor);
        }
    }
});

// Auto hidden alert
$(document).ready(function () {
    $(".alert.auto-hide").delay(5000).slideUp(300);
});


// Ngăn Double click
$('form.prevent-double-click').submit(function () {
    if (this.querySelector('[type="submit"]')) {
        this.querySelector('[type="submit"]').disabled = true;
    }
})
