$(function () {
    "use strict";

    let body = $("body"),
        main_menu = $("#main-menu");

    $("[data-toggle=\"tooltip\"]").tooltip();

    $("#search-box").on("click", function (e) {
        e.preventDefault();
        $(".searchform").addClass("show");
        $("#stext").focus();
    });

    $("#search-back").on("click", function () {
        $(".searchform").removeClass("show");
        $(".search-box #stext").val("");
    });

    $(window).scroll(function () {
        let hamburger = main_menu.find(".hamburger");
        if ($(this).scrollTop() > $(".top-nav").outerHeight()) {
            hamburger.show();
        } else {
            hamburger.hide();
        }
    });

    main_menu.affix({
        offset: {
            top: $(".top-nav").outerHeight(true)
        }
    });

    let nav_brand = main_menu.find(".navbar-brand");

    if (nav_brand.text().length >= 30) {
        nav_brand.css("font-size", "19px");
    }

    $(".hamburger").on("click", function () {
        body.toggleClass("menu-toggled");

        if (body.hasScrollBar()) {
            body.css("padding-right", getScrollbarWidth() + "px");

            if (main_menu.hasClass("affix")) {
                main_menu.css("width", "calc(100% - " + getScrollbarWidth() + "px)");
            }
        }
    });

    $(".overlay").bind("click", function () {
        body.removeClass("menu-toggled").css("padding-right", "0px");
        main_menu.css("width", "100%");
    });

    $(".leftmenu .left-content").mCustomScrollbar({
        theme: "minimal-dark",
        axis: "y",
        scrollInertia: 550,
        mouseWheel: {
            enable: !0,
            axis: "y",
            preventDefault: !0
        }
    });

    body.on("click", "[data-action]", function (e) {
        switch ($(this).data("action")) {
            case "dark-mode":
                e.preventDefault();
                let style_attr = $(".header").attr("style");

                if (body.hasClass("dark")) {
                    body.removeClass("dark");
                    $(this).removeClass("active");
                    Cookies.set("dark-mode", 0);
                    if (typeof style_attr !== typeof undefined && style_attr !== false) {
                        $(".header").attr("style", style_attr.replace("dark", "light"));
                    }
                } else {
                    body.addClass("dark");
                    $(this).addClass("active");
                    Cookies.set("dark-mode", 1);
                    if (typeof style_attr !== typeof undefined && style_attr !== false) {
                        $(".header").attr("style", style_attr.replace("light", "dark"));
                    }
                }
                break;
        }
    });
});

$.fn.extend({
    hasScrollBar: function () {
        return this.get(0).scrollHeight > this.height();
    }
});

function getScrollbarWidth() {
    let inner = document.createElement("p");
    inner.style.width = "100%";
    inner.style.height = "200px";

    let outer = document.createElement("div");
    outer.style.position = "absolute";
    outer.style.top = "0px";
    outer.style.left = "0px";
    outer.style.visibility = "hidden";
    outer.style.width = "200px";
    outer.style.height = "150px";
    outer.style.overflow = "hidden";
    outer.appendChild(inner);

    document.body.appendChild(outer);
    let w1 = inner.offsetWidth;
    outer.style.overflow = "scroll";
    let w2 = inner.offsetWidth;

    if (w1 === w2) {
        w2 = outer.clientWidth;
    }

    document.body.removeChild(outer);

    return (w1 - w2);
}
