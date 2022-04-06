<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: chat.php
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
require_once '../../../maincore.php';

if (!defined('ATOMCP_LOCALE')) {
    if (file_exists(THEMES.'admin_themes/AtomCP/locale/'.LANGUAGE.'.php')) {
        define('ATOMCP_LOCALE', THEMES.'admin_themes/AtomCP/locale/'.LANGUAGE.'.php');
    } else {
        define('ATOMCP_LOCALE', THEMES.'admin_themes/AtomCP/locale/English.php');
    }
}

if (!defined('DB_ACPCHAT')) {
    define('DB_ACPCHAT', DB_PREFIX.'acpchat');
}

$locale = fusion_get_locale('', ATOMCP_LOCALE);

function authorize_aid() {
    if (defined('iAUTH') && isset($_GET['aid']) && $_GET['aid'] == iAUTH) {
        return TRUE;
    }

    return FALSE;
}

if (db_exists(DB_ACPCHAT)) {
    $userdata = fusion_get_userdata();

    if (authorize_aid()) {
        if (isset($_GET['data'])) {
            require_once INCLUDES.'core_functions_include.php';
            require_once INCLUDES.'theme_functions_include.php';

            $result = dbquery("
                SELECT * FROM (
                    SELECT acp.*, u.user_id, u.user_name, u.user_status, u.user_avatar, u.user_level, u.user_lastvisit
                    FROM ".DB_ACPCHAT." acp
                    LEFT JOIN ".DB_USERS." AS u ON acp.msg_user=u.user_id
                    ORDER BY msg_datestamp DESC
                    LIMIT 15
                ) AS acp
                ORDER BY msg_datestamp ASC
            ");

            if (dbrows($result)) {
                while ($data = dbarray($result)) {
                    $my_msg = $userdata['user_id'] == $data['msg_user'];
                    echo '<div id="msg-'.$data['msg_id'].'" class="message clearfix '.($my_msg ? ' my-msg' : '').'">';
                    echo '<div class="info clearfix">';
                    echo '<span class="name">'.$data['user_name'].'</span>';
                    echo '<span class="time">'.timer($data['msg_datestamp']).'</span>';
                    echo '</div>';
                    echo '<div class="display-inline-block user-img">';
                    $status = $data['user_lastvisit'] >= time() - 120;
                    echo '<span class="status"><i class="fa fa-circle '.($status ? 'text-success' : 'text-danger').'"></i></span>';
                    echo display_avatar($data, '35px', '', FALSE, 'img-circle');
                    echo '</div>';
                    echo $my_msg ? '<span data-msg-id="'.$data['msg_id'].'" class="options" title="'.$locale['delete'].'"><i class="fa fa-trash"></i></span>' : '';
                    echo '<div class="text">'.parsesmileys(fusion_parse_user(nl2br($data['msg_message']))).'</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="text-center">'.$locale['cp_001'].'</div>';
            }
        }

        if (isset($_GET['insert'])) {
            $message = filter_input(INPUT_POST, 'message');
            $message = htmlspecialchars(addslashes($message));
            if (!empty($message)) {
                dbquery("INSERT INTO ".DB_ACPCHAT." (msg_user, msg_message, msg_datestamp) VALUES (".$_POST['user'].", '".$message."', ".time().");");
            }
        }

        if (isset($_GET['delete']) && isset($_POST['msg_id'])) {
            dbquery("DELETE FROM ".DB_ACPCHAT." WHERE msg_id=:msg AND msg_user=:user", [':msg' => $_POST['msg_id'], ':user' => $userdata['user_id']]);
        }
    }
}
