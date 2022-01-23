<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: UsersOnlinePanel.php
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

class UsersOnlinePanel {
    public $key;
    public $title;
    public $check;

    public function __construct() {
        $locale = fusion_get_locale();
        $theme_settings = atomcp_settings();
        $this->key = 'usersonline';
        $this->title = $locale['cp_110'];
        $this->check = !empty($theme_settings['usersonline']);
    }

    public function install() {
        dbquery("INSERT INTO ".DB_SETTINGS_THEME." (settings_name, settings_value, settings_theme) VALUES ('usersonline', '1', 'AtomCP')");
    }

    public function uninstall() {
        dbquery("DELETE FROM ".DB_SETTINGS_THEME." WHERE settings_theme='AtomCP' AND settings_name='usersonline'");
    }

    public function sidePanel() {
        $locale = fusion_get_locale();

        if ($this->check) {
            echo '<div class="openside panel panel-default">';
                echo '<div class="panel-heading"><i class="fa fa-users"></i> '.$locale['cp_110'].'</div>';
                echo '<div class="panel-body">';

                $result = dbquery("SELECT user_id, user_name, user_status, user_lastvisit FROM ".DB_USERS." ORDER BY user_lastvisit DESC LIMIT 15");

                if (dbrows($result) > 0) {
                    while ($data = dbarray($result)) {
                        echo '<div class="m-b-5">';
                        echo profile_link($data['user_id'], $data['user_name'], $data['user_status']);
                        echo '<span class="pull-right">'.timer($data['user_lastvisit']).'</span>';
                        echo '</div>';
                    }
                }

                echo '</div>';
            echo '</div>';
        }
    }
}
