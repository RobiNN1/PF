<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: infusion_db.php
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
defined('IN_FUSION') || exit;

use PHPFusion\Admins;

// Paths
const CP_ICON_SETS = INFUSIONS.'cp_icon_sets/packs/';

// Admin Settings
Admins::getInstance()->setAdminPageIcons('CIS', '<i class="admin-ico far fa-folder-open"></i>');

$cp_settings = get_settings('cp_icon_sets');
if (!empty($cp_settings['icon_set'])) {
    $pages_data = Admins::getInstance()->getAdminPages();

    foreach ($pages_data as $section) {
        foreach ($section as $page) {
            $img = '';
            $path = CP_ICON_SETS.$cp_settings['icon_set'].'/'.$page['admin_rights'];

            foreach (['.svg', '.png', '.gif', '.jpg'] as $ext) {
                if (file_exists($path.$ext)) {
                    $img = $path.$ext;
                    break;
                }
            }

            set_image('ac_'.$page['admin_rights'], $img);
        }
    }
}
