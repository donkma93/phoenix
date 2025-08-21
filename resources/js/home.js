window.Popper = require("popper.js").default;
window.$ = window.jQuery = require("jquery");
require("bootstrap");

$(document).ready(function () {
    checkHeader();
    checkScrollTopButton();
    $(window).scroll(function () {
        checkHeader();
        checkScrollTopButton();
    });
    $("#scroll-top-btn").click(function () {
        $("html, body").animate(
            {
                scrollTop: 0,
            },
            500
        );
    });
    $(".nav-link").click(function (e) {
        const target = $(this).attr("href");
        if (target.indexOf("#") > -1) {
            e.preventDefault();
            if ($(this).parents("#mobile-menu").length) {
                $("#mobile-menu").modal("hide");
            }
            $("html, body").stop();
            $("html, body").animate(
                {
                    scrollTop: $(target).offset().top - 143,
                },
                500
            );
        }
    });
    $("#mobile-menu").on("hide.bs.modal", function (e) {
        $("#mobile-menu-btn").removeClass("fa-close").addClass("fa-bars");
    });
    $("#mobile-menu").on("show.bs.modal", function (e) {
        $("#mobile-menu-btn").removeClass("fa-bars").addClass("fa-close");
    });
    $(window).on('activate.bs.scrollspy', function() {
        history.pushState('', document.title, window.location.pathname + $('.nav-menu').find('a.active').attr('href'))
    })
    if(location.hash) {
        $("html, body").animate(
            {
                scrollTop: $(location.hash).offset().top - 143,
            },
            500
        );
    }
});

function checkHeader() {
    if (window.pageYOffset > 10) {
        $(".header").addClass("sticky");
    } else {
        $(".header").removeClass("sticky");
    }
}

function checkScrollTopButton() {
    if (window.pageYOffset > 500) {
        $("#scroll-top-btn").addClass("show");
    } else {
        $("#scroll-top-btn").removeClass("show");
    }
}
