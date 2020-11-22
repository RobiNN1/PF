<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.phpfusion.com/
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

if (!defined('SS_LOCALE')) {
    if (file_exists(INFUSIONS.'server_status_panel/locale/'.LOCALESET.'.php')) {
        define('SS_LOCALE', INFUSIONS.'server_status_panel/locale/'.LOCALESET.'.php');
    } else {
        define('SS_LOCALE', INFUSIONS.'server_status_panel/locale/English.php');
    }
}

if (!defined('S_STATUS')) {
    define('S_STATUS', INFUSIONS.'server_status_panel/');
}

if (!defined('DB_SERVER_STATUS')) {
    define('DB_SERVER_STATUS', DB_PREFIX.'server_status');
}

// Admin Settings
\PHPFusion\Admins::getInstance()->setAdminPageIcons('SS', '<i class="admin-ico fa fa-fw fa-server"></i>');

\PHPFusion\Admins::getInstance()->setFolderPermissions('server_status_panel', [
    'infusions/server_status_panel/cache/' => TRUE
]);
