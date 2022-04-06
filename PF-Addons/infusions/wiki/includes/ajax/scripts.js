let WIKI = document.location.origin + site_path + "/infusions/wiki/";

function search_ajax() {
    let url = WIKI + "includes/ajax/search.php";

    $("#wiki_search").bind("keyup", function () {
        $.ajax({
            url: url,
            get: "GET",
            data: $.param({"searchstring": $(this).val()}),
            dataType: "json",
            success: function (e) {
                if ($("#wiki_search").val() === "") {
                    $(".search-dropdown").removeClass("open");
                } else {
                    let result = "";

                    if (!e.status) {
                        $.each(e, function (i, data) {
                            if (data) {
                                result += '<li><a href="' + data.link + '">' + data.title + '</a><small class="p-l-20 p-r-20"><i class="fa fa-hashtag"></i> <a href="' + data.cat_link + '">' + data.cat_title + '</a></small></li>';
                            }
                        });
                    } else {
                        result = '<li class="p-10"><span>' + e.status + '</span></li>';
                    }

                    $("#wiki_search_results").html(result);
                    $(".search-dropdown").addClass("open");
                }
            }
        });
    });
}

search_ajax();

function stats_ajax(action) {
    if (!$(".is-helpful-stat").hasClass("disabled")) {
        let url = WIKI + "includes/ajax/stats.php",
            currenturl = new URL(window.location.href);
        let page_id = currenturl.searchParams.get("page_id");

        $.ajax({
            url: url,
            get: "GET",
            data: {
                "page_id": page_id,
                "action": action
            },
            dataType: "json",
            success: function () {
                let el, el2;

                if (action === "yes") {
                    el = $(".thumbs-up");
                    el2 = $(".thumbs-down");

                } else if (action === "no") {
                    el = $(".thumbs-down");
                    el2 = $(".thumbs-up");
                } else {
                    el = $(".thumbs-up");
                    el2 = $(".thumbs-down");
                }

                if (!el.hasClass("active")) {
                    $(el).find(".count").text(parseInt($(el).find(".count").text()) + 1);
                    $(el).addClass("active");

                    let new_value = parseInt(el2.find(".count").text()) !== 0 ? parseInt(el2.find(".count").text()) - 1 : 0;
                    el2.find(".count").text(new_value);
                    el2.removeClass("active");
                }
            }
        });
    }
}

$(function () {
    $(".thumbs-up").on("click", function (e) {
        e.preventDefault();
        stats_ajax("yes");
    });

    $(".thumbs-down").on("click", function (e) {
        e.preventDefault();
        stats_ajax("no");
    });

    $(document.body).scrollspy({
        target: "#scrollspy",
        offset: $("#main-menu, #DefaultMenu, #vl-menu").height()
    });

    $(window).on("load", function () {
        $(document.body).scrollspy("refresh");
    });

    /*setTimeout(function () {
        let scrollspy = $("#scrollspy");
        scrollspy.affix({
            offset: {
                top: function () {
                    let offset_top = scrollspy.offset().top,
                        nav_outer_height = $("#main-menu, #DefaultMenu, #vl-menu").height();

                    return (this.top = offset_top - nav_outer_height);
                }
            }
        });

        let select = $("#versions_select");
        select.affix({
            offset: {
                top: function () {
                    let offset_top = select.offset().top,
                        nav_outer_height = $("#main-menu, #DefaultMenu, #vl-menu").height();

                    return (this.top = offset_top - nav_outer_height);
                }
            }
        });
    }, 100);*/
});
