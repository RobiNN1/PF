function checkedCheckbox() {
    let checkList = '';
    $("input[type=checkbox]").each(function () {
        if (this.checked) {
            checkList += $(this).val() + ",";
        }
    });
    return checkList;
}

$(function () {
    "use strict";

    $("#check_all").click(function () {
        $(".thread-card").addClass("thread-card-checked");
    });

    $("#check_none").click(function () {
        $(".thread-card").removeClass("thread-card-checked");
    });

    $('[data-check="post"]').click(function () {
        let id = $(this).val();
        if ($(this).prop("checked")) {
            $('[data-postid="' + id + '"]').addClass("thread-card-checked");
        } else {
            $('[data-postid="' + id + '"]').removeClass("thread-card-checked");
        }
    });

    let unread_checkbox = $(".unread").find(":checkbox");
    let read_checkbox = $(".read").find(":checkbox");

    $("#check_all_pm").bind("click", function () {
        let action = $(this).data("action");
        if (action === "check") {
            unread_checkbox.prop("checked", true);
            read_checkbox.prop("checked", true);
            $(".unread").addClass("table-warning");
            $(".read").addClass("table-warning");
            $("#chkv").removeClass("fas fa-square").addClass("fas fa-minus-square");
            $(this).data("action", "uncheck");
            $("#selectedPM").val(checkedCheckbox());
        } else {
            unread_checkbox.prop("checked", false);
            read_checkbox.prop("checked", false);
            $(".unread").removeClass("table-warning");
            $(".read").removeClass("table-warning");
            $("#chkv").removeClass("fas fa-minus-square").addClass("fas fa-square");
            $(this).data("action", "check");
            $("#selectedPM").val(checkedCheckbox());
        }
    });

    $("#check_read_pm").bind("click", function () {
        let action = $(this).data("action");
        if (action === "check") {
            read_checkbox.prop("checked", true);
            $(".read").addClass("table-warning");
            $("#chkv").removeClass("fas fa-square").addClass("fas fa-minus-square");
            $(this).data("action", "uncheck");
            $("#selectedPM").val(checkedCheckbox());
        } else {
            read_checkbox.prop("checked", false);
            $(".read").removeClass("table-warning");
            $("#chkv").removeClass("fas fa-minus-square").addClass("fas fa-square");
            $(this).data("action", "check");
            $("#selectedPM").val(checkedCheckbox());
        }
    });

    $("#check_unread_pm").bind("click", function () {
        let action = $(this).data("action");
        if (action === "check") {
            unread_checkbox.prop("checked", true);
            $(".unread").addClass("table-warning");
            $("#chkv").removeClass("fas fa-square").addClass("fas fa-minus-square");
            $(this).data("action", "uncheck");
            $("#selectedPM").val(checkedCheckbox());
        } else {
            unread_checkbox.prop("checked", false);
            $(".unread").removeClass("table-warning");
            $("#chkv").removeClass("fas fa-minus-square").addClass("fas fa-square");
            $(this).data("action", "check");
            $("#selectedPM").val(checkedCheckbox());
        }
    });

    $(".select-msg input[type=checkbox]").bind("click", function () {
        let checkList = $("#selectedPM").val();
        if ($(this).is(":checked")) {
            $(this).parents("tr").addClass("table-warning");
            checkList += $(this).val() + ",";
        } else {
            $(this).parents("tr").removeClass("table-warning");
            checkList = checkList.replace($(this).val() + ",", "");
        }
        $("#selectedPM").val(checkList);
    });

    // Username check
    let r_username = $("#userfieldsform #user_name");
    r_username.keyup(function () {
        $.ajax({
            url: site_path + "includes/api/?api=username-check",
            method: "GET",
            data: $.param({"name": $(this).val()}),
            dataType: "json",
            success: function (e) {
                $(".username-checker").remove();

                if (e.result === "valid") {
                    r_username.addClass("is-valid").removeClass("is-invalid");
                } else if (e.result === "invalid") {
                    r_username.addClass("is-invalid").removeClass("is-valid");
                    let feedback_html = "<div class=\"username-checker invalid-feedback help-block\">" + e.response + "</div>";
                    r_username.after(feedback_html);
                }
            }
        });
    });

    // Password check
    let r_userpass1 = $("#userfieldsform #user_password1");
    let r_userpass1_field = $("#userfieldsform #user_password1-field"); // BS3
    r_userpass1.keyup(function () {
        $.ajax({
            url: site_path + "includes/api/?api=userpass-check",
            method: "GET",
            data: $.param({"pass": $(this).val()}),
            dataType: "json",
            success: function (e) {
                $(".userpass-checker").remove();

                if (e.result === "valid") {
                    r_userpass1.addClass("is-valid").removeClass("is-invalid");
                } else if (e.result === "invalid") {
                    r_userpass1.addClass("is-invalid").removeClass("is-valid");
                    r_userpass1_field.addClass("has-error").removeClass("has-success"); // BS3
                    let feedback_html = "<div class=\"userpass-checker invalid-feedback help-block\">" + e.response + "</div>";
                    if (r_userpass1_field.find(".input-group").length > 0) {
                        r_userpass1_field.find(".input-group").after(feedback_html);
                    } else {
                        r_userpass1.after(feedback_html);
                    }
                }
            }
        });
    });

    // Admin Password check
    let r_adminpass1 = $("#userfieldsform #user_admin_password1");
    let r_adminpass1_field = $("#userfieldsform #user_admin_password1-field"); // BS3
    r_adminpass1.keyup(function () {
        $.ajax({
            url: site_path + "includes/api/?api=userpass-check",
            method: "GET",
            data: $.param({"pass": $(this).val()}),
            dataType: "json",
            success: function (e) {
                $(".userpass-checker").remove();

                if (e.result === "valid") {
                    r_adminpass1.addClass("is-valid").removeClass("is-invalid");
                } else if (e.result === "invalid") {
                    r_adminpass1.addClass("is-invalid").removeClass("is-valid");
                    r_adminpass1_field.addClass("has-error").removeClass("has-success"); // BS3
                    let feedback_html = "<div class=\"userpass-checker invalid-feedback help-block\">" + e.response + "</div>";
                    if (r_adminpass1_field.find(".input-group").length > 0) {
                        r_adminpass1_field.find(".input-group").after(feedback_html);
                    } else {
                        r_adminpass1.after(feedback_html);
                    }
                }
            }
        });
    });

    $("[id$='pwdToggle']").html('<i class="fas fa-eye"></i>');
});

function togglePasswordInput(button_id, field_id) {
    let button = $("#" + button_id);
    let input = $("#" + field_id);
    if (input.attr("type") === "password") {
        input.attr("type", "text");
        button.html('<i class="fas fa-eye-slash"></i>');
    } else {
        input.attr("type", "password");
        button.html('<i class="fas fa-eye"></i>');
    }
}
