$(function () {
    var body = $("body");

    $('[data-toggle="tooltip"]').tooltip();

    $(".sidebar .sidebar-content").mCustomScrollbar({
        theme: "minimal-dark",
        axis: "y",
        scrollInertia: 550,
        mouseWheel: {
            enable: !0,
            axis: "y",
            preventDefault: !0
        }
    });

    $(".btn").ripple();

    $("#toggle-sidebar").on("click", function (e) {
        e.preventDefault();

        if (body.hasClass("sidebar-toggled")) {
            body.removeClass("sidebar-toggled");
            Cookies.set("sidebar-toggled", 0);
        } else {
            body.addClass("sidebar-toggled");
            Cookies.set("sidebar-toggled", 1);
        }
    });

    $("#messages-box").on("click", function (e) {
        e.preventDefault();
        $("#pms-box").toggle();
        $("#sidebar-menu").toggle();
    });

    $(window).scroll(function() {
        if ($(window).scrollTop() > 0) {
            $(".topnav, .sidebar .logo").addClass("active");
        } else {
            $(".topnav, .sidebar .logo").removeClass("active");
        }
    });
});

$.fn.extend({
    ripple: function (options, callback) {
        var self = this;

        self.defaults = {
            debug: false,
            on: "mousedown",
            opacity: 0.4,
            color: "auto",
            multi: false,
            duration: 0.5,
            rate: function (pxPerSecond) {
                return pxPerSecond;
            },
            easing: "linear"
        };

        self.defaults = $.extend({}, self.defaults, options);

        $(document).on(self.defaults.on, self.selector, function (e) {
            var ripple,
                settings;

            $(this).addClass("has-ripple");

            settings = $.extend({}, self.defaults, $(this).data());

            if (settings.multi || (!settings.multi && $(this).find(".ripple").length === 0)) {
                ripple = $("<span></span>").addClass("ripple");
                ripple.appendTo($(this));

                if (!ripple.height() && !ripple.width()) {
                    var size = Math.max($(this).outerWidth(), $(this).outerHeight());

                    ripple.css({
                        height: size,
                        width: size
                    });
                }

                if (settings.rate && typeof settings.rate === "function") {
                    var rate = Math.round(ripple.width() / settings.duration),
                        filteredRate = settings.rate(rate),
                        newDuration = (ripple.width() / filteredRate);

                    if (settings.duration.toFixed(2) !== newDuration.toFixed(2)) {
                        settings.duration = newDuration;
                    }
                }

                var color = (settings.color === "auto") ? $(this).css("color") : settings.color,
                    css = {
                        animationDuration: (settings.duration).toString() + "s",
                        animationTimingFunction: settings.easing,
                        background: color,
                        opacity: settings.opacity
                    };

                ripple.css(css);
            }

            if (!settings.multi) {
                ripple = $(this).find(".ripple");
            }

            ripple.removeClass("ripple-animate");

            var x = e.pageX - $(this).offset().left - ripple.width() / 2,
                y = e.pageY - $(this).offset().top - ripple.height() / 2;

            if (settings.multi) {
                ripple.one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend", function () {
                    $(this).remove();
                });
            }

            ripple.css({
                top: y + "px",
                left: x + "px"
            }).addClass("ripple-animate");

            if (callback) {
                callback();
            }
        });
    }
});
