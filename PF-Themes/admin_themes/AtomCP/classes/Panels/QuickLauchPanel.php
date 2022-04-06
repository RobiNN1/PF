<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: QuickLauchPanel.php
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

class QuickLauchPanel {
    public $key;
    public $title;
    public $check;

    public function __construct() {
        $locale = fusion_get_locale();
        $theme_settings = atomcp_settings();
        $this->key = 'quicklauch';
        $this->title = $locale['cp_102'];
        $this->check = !empty($theme_settings['quicklauch']);
    }

    public function install() {
        dbquery("INSERT INTO ".DB_SETTINGS_THEME." (settings_name, settings_value, settings_theme) VALUES ('quicklauch', '1', 'AtomCP')");
    }

    public function uninstall() {
        dbquery("DELETE FROM ".DB_SETTINGS_THEME." WHERE settings_theme='AtomCP' AND settings_name='quicklauch'");
    }

    public function mainPanel() {
        $locale = fusion_get_locale();
        $aidlink = fusion_get_aidlink();

        if ($this->check) {
            openside('<i class="fa fa-rocket"></i> '.$locale['cp_102'], '', ['id' => 101, 'collapse' => TRUE]);
            $quick_launch = [
                ['link' => ADMIN.'members.php', 'icon' => 'far fa-user-circle', 'title' => $locale['M'], 'rights' => 'M'],
                ['link' => ADMIN.'blacklist.php', 'icon' => 'fa fa-ban', 'title' => $locale['B'], 'rights' => 'B'],
                ['link' => ADMIN.'comments.php', 'icon' => 'fa fa-comments', 'title' => $locale['C'], 'rights' => 'C'],
                ['link' => ADMIN.'site_links.php', 'icon' => 'fa fa-link', 'title' => $locale['SL'], 'rights' => 'SL'],
                ['link' => ADMIN.'errors.php', 'icon' => 'fa fa-bug', 'title' => $locale['ERRO'], 'rights' => 'ERRO'],
                ['link' => ADMIN.'settings_main.php', 'icon' => 'fa fa-cog', 'title' => $locale['S1'], 'rights' => 'S1'],
                ['link' => ADMIN.'infusions.php', 'icon' => 'fa fa-cubes', 'title' => $locale['I'], 'rights' => 'I'],
                ['link' => ADMIN.'settings_security.php', 'icon' => 'fa fa-shield-alt', 'title' => $locale['S12'], 'rights' => 'S12']
            ];

            foreach ($quick_launch as $item) {
                if (checkrights($item['rights'])) {
                    echo '<a href="'.$item['link'].$aidlink.'" class="btn btn-info m-t-5 m-b-5 m-l-5"><i class="'.$item['icon'].'"></i> '.$item['title'].'</a>';
                }
            }
            closeside('', TRUE);
        }
    }
}
