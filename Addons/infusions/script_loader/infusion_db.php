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

// Locales
if (!defined('SCL_LOCALE')) {
    if (file_exists(INFUSIONS.'script_loader/locale/'.LANGUAGE.'.php')) {
        define('SCL_LOCALE', INFUSIONS.'script_loader/locale/'.LANGUAGE.'.php');
    } else {
        define('SCL_LOCALE', INFUSIONS.'script_loader/locale/English.php');
    }
}

// Paths
const SCRIPT_LOADER = INFUSIONS.'script_loader/';

// Database
const DB_SCRIPT_LOADER = DB_PREFIX.'script_loader';

// Admin Settings
\PHPFusion\Admins::getInstance()->setAdminPageIcons('SCL', '<i class="admin-ico fa fa-fw fa-code"></i>');

if (!defined('ADMIN_PANEL') && defined('SCRIPT_LOADER_EXISTS')) {
    $result = dbquery("SELECT * FROM ".DB_SCRIPT_LOADER);
    if (dbrows($result) > 0) {
        while ($data = dbarray($result)) {
            if ($data['type'] == 'head') {
                if (!empty($data['code'])) {
                    add_to_head(html_entity_decode(stripslashes($data['code']), ENT_QUOTES, fusion_get_locale('charset')));
                }
            }

            if ($data['type'] == 'footer') {
                if (!empty($data['code'])) {
                    add_to_footer(html_entity_decode(stripslashes($data['code']), ENT_QUOTES, fusion_get_locale('charset')));
                }
            }
        }
    }
}
