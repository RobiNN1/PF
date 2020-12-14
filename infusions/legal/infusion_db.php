<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP Fusion Inc
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

if (!defined('LG_LOCALE')) {
    if (file_exists(INFUSIONS.'legal/locale/'.LOCALESET.'legal.php')) {
        define('LG_LOCALE', INFUSIONS.'legal/locale/'.LOCALESET.'legal.php');
    } else {
        define('LG_LOCALE', INFUSIONS.'legal/locale/English/legal.php');
    }
}

if (!defined('LEGAL')) {
    define('LEGAL', INFUSIONS.'legal/');
}

if (!defined('DB_LEGAL')) {
    define('DB_LEGAL', DB_PREFIX.'legal');
}

\PHPFusion\Admins::getInstance()->setAdminPageIcons('LG', '<i class="admin-ico fa fa-fw fa-gavel"></i>');
