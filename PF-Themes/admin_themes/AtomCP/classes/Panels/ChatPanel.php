<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: ChatPanel.php
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
namespace AtomCP\Panels;

class ChatPanel {
    public $key;
    public $title;
    public $check;

    public function __construct() {
        $locale = fusion_get_locale();
        $this->key = 'chat';
        $this->title = $locale['cp_205'];
        $this->check = db_exists(DB_ACPCHAT);
    }

    public function install() {
        dbquery("CREATE TABLE ".DB_ACPCHAT." (
            msg_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
            msg_user MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '1',
            msg_message VARCHAR(250) NOT NULL DEFAULT '',
            msg_datestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
            PRIMARY KEY (msg_id)
        ) ENGINE=MyISAM");
    }

    public function uninstall() {
        dbquery("DROP TABLE ".DB_ACPCHAT);
    }

    public function displayChat() {
        $locale = fusion_get_locale();
        $userdata = fusion_get_userdata();

        if ($this->check) {
            echo '<aside class="direct-chat">';
                echo '<div id="ajax-loader" style="display: none;position: absolute;z-index: 1;" class="center-xy"><img style="width: 80px;" class="img-responsive" alt="Ajax Loader" src="'.IMAGES.'loader.svg"/></div>';
                echo '<div id="chat-messages"></div>';

                echo openform('chat-form', 'post', FUSION_REQUEST, ['class' => 'chat-form']);
                    echo '<div id="chat-error-msg" class="text-danger m-b-10" style="display: none;"></div>';
                    echo form_textarea('msg_message', '', '', [
                        'placeholder' => $locale['cp_090'],
                        'class'       => 'm-0',
                        'height'      => '34px'
                    ]);
                    echo '<input type="submit" id="submit-chat-msg" class="hidden" value="'.$locale['send_message'].'">';
                echo closeform();
            echo '</aside>';

            $ajax_preload = '<div class="message clearfix my-msg" id="ajax-preload">';
                $ajax_preload .= '<div class="info clearfix">';
                    $ajax_preload .= '<span class="name">'.$userdata['user_name'].'</span>';
                    $ajax_preload .= '<span class="time">'.$locale['just_now'].'</span>';
                $ajax_preload .= '</div>';
                $ajax_preload .= '<div class="display-inline-block user-img">';
                    $ajax_preload .= '<span class="status"><i class="fa fa-circle text-success"></i></span>';
                    $ajax_preload .= '<div class="display-inline-block avatar uder-image"><i class="fa fa-circle" style="font-size: 35px;"></i></div>';
                    $ajax_preload .= '</div>';
                $ajax_preload .= '<span class="options" title="'.$locale['delete'].'"><i class="fa fa-trash"></i></span>';
                $ajax_preload .= '<div class="text" id="message"></div>';
            $ajax_preload .= '</div>';
            $ajax_preload = str_replace('"', "'", $ajax_preload);
            $ajax_preload = str_replace("\n", "", $ajax_preload);

            add_to_jquery('
                chat_ajax({
                    user_id: '.$userdata['user_id'].',
                    url: "'.ATOMCP.'chat.php'.fusion_get_aidlink().'",
                    ajax_preload: "'.$ajax_preload.'",
                    messages: {
                        empty: "'.$locale['cp_091'].'"
                    }
                });
            ');
        }
    }
}
