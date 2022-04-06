$(function () {
    "use strict";

    var body = $("body"),
        dropdown = $(".dropdown");

    dropdown.on("show.bs.dropdown", function () {
        $(this).find(".dropdown-menu").first().stop(true, true).slideDown(200);
    });
    dropdown.on("hide.bs.dropdown", function () {
        $(this).find(".dropdown-menu").first().stop(true, true).slideUp(100);
    });

    $("#toggle-leftmenu").on("click", function (e) {
        e.preventDefault();
        body.toggleClass("leftmenu-toggled");
        body.css("padding-right", getScrollbarWidth() + "px");
        $("#backtotop").css("padding-right", getScrollbarWidth() + "px");

        if ($("#main-menu").hasClass("affix")) {
            $("#main-menu").css("width", "calc(100% - " + getScrollbarWidth() + "px)");
        }
    });

    $(".overlay").bind("click", function () {
        body.removeClass("leftmenu-toggled");
        body.css("padding-right", "0px");
        $("#backtotop").css("padding-right", "0px");
        $("#main-menu").css("width", "100%");
    });

    $("#main-menu").affix({
        offset: {
            top: $(".theme-header").outerHeight()
        }
    });
});

function getScrollbarWidth() {
    var outer = document.createElement("div");
    outer.style.visibility = "hidden";
    outer.style.width = "100px";
    outer.style.msOverflowStyle = "scrollbar";

    document.body.appendChild(outer);

    var widthNoScroll = outer.offsetWidth;
    outer.style.overflow = "scroll";

    var inner = document.createElement("div");
    inner.style.width = "100%";
    outer.appendChild(inner);

    var widthWithScroll = inner.offsetWidth;
    outer.parentNode.removeChild(outer);

    return widthNoScroll - widthWithScroll;
}
