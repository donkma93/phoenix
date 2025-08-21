window._ = require("lodash");

window.Popper = require("popper.js").default;
window.$ = window.jQuery = require("jquery");
require("./datepicker.min.js");

window.filterInput = require("./autocomplete.js").default;

window.loading = (isLoading = true) => {
    if (isLoading) {
        $("#loading").show();
        $("body").addClass("overflow-hidden");
    } else {
        $("#loading").hide();
        $("body").removeClass("overflow-hidden");
    }
};

window.createFlash = (messages) => {
    const flashEl = $("<div>").addClass("flash");
    for (const message of messages) {
        const flash = $("<div>")
            .addClass(
                `flash-message flash-${
                    message.type === "error" ? "error" : "success"
                }`
            )
            .text(message.content);
        flashEl.append(flash);
    }
    $("body").append(flashEl);

    setTimeout(() => {
        flashEl.addClass("show");
        setTimeout(() => {
            flashEl.removeClass("show");
            setTimeout(function () {
                flashEl.remove();
            }, 1000);
        }, 5000);
    }, 50);
};

$(document).ready(function () {
    $(".date-picker").datepicker({
        autoclose: true,
        format: "yyyy-mm-dd",
    });

    $(".img-picker").change(function (e) {
        const tg = $($(this).data("target"));
        const { size, bgSize } = tg.data();
        const file = e.target.files[0];
        if (file) {
            const fr = new FileReader();
            fr.onload = () => {
                tg.addClass("show").css({
                    backgroundImage: `url(${fr.result})`,
                    backgroundSize: bgSize || "cover",
                    height: size || "120px",
                    width: size || "120px",
                });
            };
            fr.readAsDataURL(file);
        }
    });

    if ($(".img-preview").data("init")) {
        const { init, bgSize, size } = $(".img-preview").data();
        $(".img-preview").css({
            backgroundImage: `url(${init})`,
            backgroundSize: bgSize || "cover",
            height: size || "120px",
            width: size || "120px",
            margin: "auto",
        });
    }

    const initFlash = $(".flash");
    initFlash.addClass("show");
    setTimeout(function () {
        initFlash.removeClass("show");
        setTimeout(function () {
            initFlash.remove();
        }, 1000);
    }, 5000);

    searchSelectInit();
});

function searchSelectInit() {
    const initEls = $(".search-select");
    initEls.each(function () {
        const el = $(this);
        const options = el.children();
        const optionBoxEl = $("<div>").addClass("search-select-option-box");
        const valueEl = $("<div>").addClass("search-select-val");

        const selectEl = $("<div>")
            .addClass("search-select-box form-control")
            .append($("<span>").addClass("fa fa-angle-down search-select-icon"))
            .append(valueEl);

        const input = $("<input>")
            .addClass("form-control")
            .on("keyup", function () {
                const val = $(this).val();
                optionBoxEl.children(".search-select-option").each(function () {
                    if ($(this).text().includes(val)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        optionBoxEl.append(
            $("<div>").addClass("search-select-input-box").append(input)
        );
        options.each(function () {
            const option = $(this);

            const optionEl = $("<div>")
                .addClass("search-select-option")
                .text(option.text())
                .attr("value", option.attr("value"))
                .on("click", function (e) {
                    e.stopPropagation();
                    el.val(option.attr("value"));
                    options.attr("selected", false);
                    option.attr("selected", true);
                    optionBoxEl
                        .children(".search-select-option")
                        .removeClass("selected");
                    optionEl.addClass("selected");
                    optionBoxEl.removeClass("show");
                    valueEl.text(option.text());
                });
            if (option.attr("selected")) {
                valueEl.text(option.text());
                optionEl.addClass("selected");
            }
            optionBoxEl.append(optionEl);
        });

        selectEl.append(optionBoxEl).on("click", function (e) {
            optionBoxEl.addClass("show");
            input.focus();
        });

        $(document).on("click", function (e) {
            if (!selectEl.is(e.target) && selectEl.has(e.target).length === 0) {
                optionBoxEl.removeClass("show");
            }
        });
        el.hide();
        el.after(selectEl);
    });
}

$(".js-box-chat").on("click", function () {
    $(".box-chat").toggleClass('d-flex');
    document.querySelector("#chat-content").scrollTo(0,document.querySelector("#chat-content").scrollHeight);
});

$(".js-close-chat").on("click", function () {
    $(".box-chat").removeClass("d-flex");
});
