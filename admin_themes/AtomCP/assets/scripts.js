$(function () {
    "use strict";

    $('[data-toggle="tooltip"]').tooltip();

    let body = $("body");

    $("#navbar-search").on("click", function (e) {
        e.preventDefault();
        $(this).addClass("open");
        $(".nav-link").addClass("search-open");
        $("#navbar-search .form-control").focus();
    });

    $("#navbar-search .form-control").blur(function () {
        $("#navbar-search").removeClass("open");
        $(".nav-link").removeClass("search-open");
    });

    $(document).click(function () {
        $(".nav-search-dropdown").removeClass("open");
    });

    $(".sidebar-toggle").on("click", function (e) {
        e.preventDefault();
        if (body.hasClass("sidebar-toggled")) {
            body.removeClass("sidebar-toggled");
            Cookies.set("sidebar-toggled", 0);
        } else {
            body.addClass("sidebar-toggled");
            Cookies.set("sidebar-toggled", 1);
        }
    });

    $(".dark-mode").on("click", function (e) {
        e.preventDefault();
        if (body.hasClass("darkmode")) {
            body.removeClass("darkmode");
            Cookies.set("darkmode", 0);
        } else {
            body.addClass("darkmode");
            Cookies.set("darkmode", 1);
        }
    });

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        const newColorScheme = e.matches ? body.addClass("darkmode") : body.removeClass("darkmode");
    });

    $("#closedebugger").on("click", function () {
        $(".debugger-content").hide();
        $(this).hide();
    });

    $(".debugger-navbar-nav [data-toggle=\"tab\"]").on("click", function () {
        $(".debugger-content").show();
        $("#closedebugger").show();
    });

    $("#showerrorlog").on("click", function (e) {
        e.preventDefault();
        $("#tbody-Modal").modal();
    });

    $("[data-panel-id]").find(".panel-collapsed-indicator").on("click", function () {
        $(this).toggleClass("fa-plus fa-minus");
    });

    var panel_collapse = $(".panel .panel-collapse");

    panel_collapse.on("hidden.bs.collapse", function () {
        var id = $(this).attr("id");
        $('[data-target="# + id +"]').addClass("fa-plus");
        Cookies.set(id, 1);
    });

    panel_collapse.on("shown.bs.collapse", function () {
        var id = $(this).attr("id");
        $('[data-target="# + id +"]').addClass("fa-minus");
        Cookies.remove(id);
    });

    let url = new URL(window.location.href),
        pagenum = url.searchParams.get("pagenum");
    pagenum = pagenum !== null ? pagenum : 0;
    let section = $("[data-panel-id=\"panel-" + pagenum + "\"]");

    if (section.length) {
        $("html, body").animate({scrollTop: section.offset().top - 100}, 1000);
    }

    $("#chat > a").on("click", function (e) {
        e.preventDefault();
        $("#chat").toggleClass("open");
        $(".direct-chat").toggleClass("open");
    });

    $("[id$='pwdToggle']").html('<i class="fa fa-eye"></i>');
});

function togglePasswordInput(button_id, field_id) {
    let button = $("#" + button_id);
    let input = $("#" + field_id);
    if (input.attr("type") === "password") {
        input.attr("type", "text");
        button.html('<i class="fa fa-eye-slash"></i>');
    } else {
        input.attr("type", "password");
        button.html('<i class="fa fa-eye"></i>');
    }
}

function search_ajax(url) {
    $("#search_pages").bind("keyup", function () {
        $.ajax({
            url: url,
            get: "GET",
            data: $.param({"pagestring": $(this).val()}),
            dataType: "json",
            success: function (e) {
                if ($("#search_pages").val() === "") {
                    $(".nav-search-dropdown").removeClass("open");
                } else {
                    var result = "";

                    if (!e.status) {
                        $.each(e, function (i, data) {
                            if (data) {
                                result += "<li><a href=\"" + data.link + "\"><img class=\"admin-image\" alt=\"" + data.title + "\" src=\"" + data.icon + "\"/> " + data.title + "</a></li>";
                            }
                        });
                    } else {
                        result = "<li class=\"p-10\"><span>" + e.status + "</span></li>";
                    }

                    $("#search_result").html(result);
                    $(".nav-search-dropdown").addClass("open");
                }
            }
        });
    });
}

let options = {
        user_id: 0,
        url: '',
        ajax_preload: '',
        messages: {}
    },
    chat_message = $("#msg_message");

function chat_ajax(options) {
    let messages = $("#chat-messages");

    $(".chat-form").submit(function (e) {
        e.preventDefault();
        let submit_data = {
            "user": options.user_id,
            "message": chat_message.val()
        };

        if (chat_message.val() !== "") {
            $.ajax({
                url: options.url + "&insert",
                type: "POST",
                data: submit_data,
                beforeSend: function () {
                    messages.append(options.ajax_preload).find("#message").html(chat_message.val());
                    messages.scrollTop(messages[0].scrollHeight);
                    $("#chat-error-msg").hide();
                },
                success: function () {
                    chat_message.val("");
                    msg_load();
                }
            });
        } else {
            $("#chat-error-msg").show().append(options.messages.empty + "<br/>");
        }
    });

    $("body").on("click", "[data-msg-id]", function () {
        var msg_id = $(this).data("msg-id");

        $.ajax({
            url: options.url + "&delete",
            type: "POST",
            data: {msg_id: msg_id},
            success: function () {
                $("#msg-" + msg_id).remove();
            }
        });
    });

    $("#msg_message").keypress(function (e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if (code === 13) {
            $("#submit-chat-msg").trigger("click");
            return true;
        }
    });

    function msg_load() {
        messages.load(options.url + "&data", function () {
            $("#ajax-loader").hide();
            messages.scrollTop(messages[0].scrollHeight);
            $('[data-toggle="user-tooltip"]').popover();
            $('[data-toggle="tooltip"]').tooltip();
        });
    }

    $("#ajax-loader").show();
    msg_load();
    setInterval(msg_load, 2000);
}
