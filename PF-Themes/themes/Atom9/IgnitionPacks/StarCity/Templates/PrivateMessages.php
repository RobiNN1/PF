<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: PrivateMessages.php
| Author: Frederick MC Chan
| Author: RobiNN
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
namespace Atom9Theme\IgnitionPacks\StarCity\Templates;

use Atom9Theme\Core;
class PrivateMessages extends Core {
    public static function displayInbox($info) {
        $locale = fusion_get_locale('', ATOM9_LOCALE);

        self::setParam('panels', FALSE);

        add_to_head('<link rel="stylesheet" href="'.IGNITION_PACK.'Templates/css/messages.css">');

        echo '<div class="starmail">';
            echo '<div class="row equal-height mail-top">';
                echo '<div class="col-xs-12 col-sm-3"><div class="mail-left">';
                    $icons = [
                        'inbox'   => 'inbox',
                        'outbox'  => 'send-o',
                        'archive' => 'archive',
                        'options' => 'cog'
                    ];

                    echo '<ul class="mail-menu">';
                        $i = 0;
                        foreach ($info['folders'] as $key => $folder) {
                            $active = (isset($_GET['folder']) && $_GET['folder'] == $key) || (isset($_GET['msg_send']) && $_GET['msg_send'] == $key) ? ' class="active"' : '';
                            echo '<li'.$active.'><a href="'.$folder['link'].'" title="'.$folder['title'].'">';
                                echo '<i class="fa fa-'.$icons[$key].'"></i>';
                                if ($i < count($info['folders']) - 1) {
                                    $total_key = $key."_total";
                                    echo '<span class="badge">'.$info[$total_key].'</span>';
                                }
                            echo '</a></li>';
                            $i++;
                        }
                    echo '</ul>';
                echo '</div></div>';
                echo '<div class="col-xs-12 col-sm-9"><div class="mail-right clearfix">';
                    if (!isset($_GET['msg_send']) && (!empty($info['actions_form']) || isset($_GET['msg_read']))) {
                        if (isset($_GET['msg_read'])) {
                            echo '<a class="btn btn-default pull-left m-r-10" href="'.$info['button']['back']['link'].'" title="'.$info['button']['back']['title'].'"><i class="fa fa-long-arrow-left"></i></a>';
                        }

                        echo '<div class="display-inline-block pull-left">';
                            if (is_array($info['actions_form'])) {
                                echo $info['actions_form']['openform'];

                                if (isset($_GET['msg_read']) && isset($info['items'][$_GET['msg_read']])) {
                                    echo '<div class="btn-group display-inline-block m-r-10">';
                                        if ($_GET['folder'] == 'archive') {
                                            echo $info['actions_form']['unlockbtn'];
                                        } else if ($_GET['folder'] == 'inbox') {
                                            echo $info['actions_form']['lockbtn'];
                                        }
                                        echo $info['actions_form']['deletebtn'];
                                    echo '</div>';
                                } else {
                                    echo '<div class="dropdown display-inline-block m-r-10">';
                                        echo '<a id="ddactions" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-default btn-sm dropdown-toggle"><i id="chkv" class="fa fa-square-o"></i><span class="caret m-l-5"></span></a>';
                                        echo '<ul class="dropdown-menu" aria-labelledby="ddactions">';
                                            foreach ($info['actions_form']['check'] as $id => $title) {
                                                echo '<li><a id="'.$id.'" data-action="check" class="pointer">'.$title.'</a></li>';
                                            }
                                        echo '</ul>';
                                    echo '</div>';

                                    echo '<div class="btn-group display-inline-block m-r-10">';
                                        if ($_GET['folder'] == 'archive') {
                                            echo $info['actions_form']['unlockbtn'];
                                        } else if ($_GET['folder'] !== 'outbox') {
                                            echo $info['actions_form']['lockbtn'];
                                        }
                                        echo $info['actions_form']['deletebtn'];
                                    echo '</div>';

                                    echo '<div class="dropdown display-inline-block m-r-10">';
                                        echo '<a id="ddactions2" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-default btn-sm dropdown-toggle">'.$locale['444'].'&hellip; <span class="caret"></span></a>';
                                        echo '<ul class="dropdown-menu" aria-labelledby="ddactions2">';
                                            echo '<li>'.$info['actions_form']['mark_all'].'</li>';
                                            echo '<li>'.$info['actions_form']['mark_read'].'</li>';
                                            echo '<li>'.$info['actions_form']['mark_unread'].'</li>';
                                            echo '<li>'.$info['actions_form']['unmark_all'].'</li>';
                                        echo '</ul>';
                                    echo '</div>';
                                }
                                echo $info['actions_form']['closeform'];
                            } else {
                                echo $info['actions_form'];
                            }
                        echo '</div>';

                        echo !empty($info['pagenav']) ? '<div class="display-inline-block">'.$info['pagenav'].'</div>' : '';
                    }

                    echo '<a class="btn btn-primary pull-right" href="'.$info['button']['new']['link'].'">'.$locale['401'].'</a>';
                echo '</div></div>';
            echo '</div>';

            echo '<div class="row equal-height mail-body">';

            if (isset($_GET['msg_send'])) {
                echo '<div class="col-xs-12 mail-form">';
                    echo $info['reply_form'];
                echo '</div>';
            } else if ($_GET['folder'] == "options") {
                echo '<div class="col-xs-12 mail-options">';
                    echo $info['options_form'];
                echo '</div>';
            } else {
                echo '<div class="col-xs-12 col-sm-3"><div class="mail-left">';
                    self::inbox($info);
                echo '</div></div>';
                echo '<div class="col-xs-12 col-sm-9"><div class="mail-right">';
                    if (!empty($info['items'])) {
                        $message = !empty($_GET['msg_read']) && isset($info['items'][$_GET['msg_read']]) ? $info['items'][$_GET['msg_read']] : current($info['items']);

                        echo '<div class="message-header">';
                            echo '<div class="overflow-hide">';
                                echo '<div class="pull-right m-t-10">';
                                    echo showdate('longdate', $message['message_datestamp']).' '.timer($message['message_datestamp']);

                                    if (!isset($_GET['msg_read']) && isset($_GET['folder']) && $_GET['folder'] == 'inbox') {
                                        echo '<a class="btn btn-primary m-l-10" href="'.$message['message']['link'].'">'.$locale['433'].'</a>';
                                    }
                                echo '</div>';

                                echo '<div class="pull-left m-t-10">'.display_avatar($message, '40px', '', FALSE, 'img-rounded m-r-10').'</div>';
                                echo '<h4 class="m-b-0">'.$message['message_subject'].'</h4>';
                                echo '<span>'. $locale['406'].': '.profile_link($message['contact_user']['user_id'], $message['contact_user']['user_name'], $message['contact_user']['user_status']).'</span>';
                            echo '</div>';
                        echo '</div>';

                        echo '<div class="message-detail">'.$message['message']['message_text'].'</div>';

                        if (!empty($info['reply_form'])) {
                            echo '<div class="message-detail">'.$info['reply_form'].'</div>';
                        }
                    }
                echo '</div></div>';
            }
            echo '</div>';
        echo '</div>'; // .starmail
    }

    private static function inbox($info) {
        $locale = fusion_get_locale();

        if (!empty($info['items'])) {
            add_to_jquery('
                let unread_checkbox = $(".unread").find(":checkbox");
                let read_checkbox = $(".read").find(":checkbox");

                $("#check_all_pm").off("click").on("click", function () {
                    let action = $(this).data("action");
                    if (action === "check") {
                        unread_checkbox.prop("checked", true);
                        read_checkbox.prop("checked", true);
                        $(".unread").addClass("selected");
                        $(".read").addClass("selected");
                        $("#chkv").removeClass("fa fa-square-o").addClass("fa fa-minus-square-o");
                        $(this).data("action", "uncheck");
                        $("#selectedPM").val(checkedCheckbox());
                    } else {
                        unread_checkbox.prop("checked", false);
                        read_checkbox.prop("checked", false);
                        $(".unread").removeClass("selected");
                        $(".read").removeClass("selected");
                        $("#chkv").removeClass("fa fa-minus-square-o").addClass("fa fa-square-o");
                        $(this).data("action", "check");
                        $("#selectedPM").val(checkedCheckbox());
                    }
                });

                $("#check_read_pm").off("click").on("click", function () {
                    let action = $(this).data("action");
                    if (action === "check") {
                        read_checkbox.prop("checked", true);
                        $(".read").addClass("selected");
                        $("#chkv").removeClass("fa fa-square-o").addClass("fa fa-minus-square-o");
                        $(this).data("action", "uncheck");
                        $("#selectedPM").val(checkedCheckbox());
                    } else {
                        read_checkbox.prop("checked", false);
                        $(".read").removeClass("selected");
                        $("#chkv").removeClass("fa fa-minus-square-o").addClass("fa fa-square-o");
                        $(this).data("action", "check");
                        $("#selectedPM").val(checkedCheckbox());
                    }
                });

                $("#check_unread_pm").off("click").on("click", function () {
                    let action = $(this).data("action");
                    if (action === "check") {
                        unread_checkbox.prop("checked", true);
                        $(".unread").addClass("selected");
                        $("#chkv").removeClass("fa fa-square-o").addClass("fa fa-minus-square-o");
                        $(this).data("action", "uncheck");
                        $("#selectedPM").val(checkedCheckbox());
                    } else {
                        unread_checkbox.prop("checked", false);
                        $(".unread").removeClass("selected");
                        $("#chkv").removeClass("fa fa-minus-square-o").addClass("fa fa-square-o");
                        $(this).data("action", "check");
                        $("#selectedPM").val(checkedCheckbox());
                    }
                });
            ');

            echo '<ul class="mail-list">';
                $i = 0;
                foreach ($info['items'] as $id => $data) {
                    $active = !empty($_GET['msg_read']) && $_GET['msg_read'] == $id;

                    echo '<li class="item'.($active == TRUE || $data['message_read'] == 0 ? ' active unread' : ' read').'">';
                        echo '<a href="'.$data['message']['link'].'">';
                            echo '<div class="pull-left">'.form_checkbox('pmID', '', '', [
                                'input_id' => 'pmID-'.$id,
                                'value'    => $id,
                                'class'    => 'm-t-10 m-r-5'
                            ]).'</div>';

                            echo '<div class="overflow-hide">';
                                echo '<div class="msg-list-heading">';
                                    echo '<span class="text-uppercase pull-right">'.date('d M', $data['message_datestamp']).'</span>';
                                    echo '<span>'.$data['contact_user']['user_name'].'</span>';
                                echo '</div>';
                                echo '<strong>'.trim_text($data['message']['name'], 20).'</strong>';
                            echo '</div>';
                        echo '</a>';
                    echo '</li>';
                    $i++;
                }
            echo '</ul>';
        } else {
            echo '<div class="no-messages text-center">'.$locale['471'].'</div>';
        }
    }
}
