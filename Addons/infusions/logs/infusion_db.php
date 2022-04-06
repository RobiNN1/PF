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

if (!defined('LOG_LOCALE')) {
    if (file_exists(INFUSIONS.'logs/locale/'.LANGUAGE.'.php')) {
        define('LOG_LOCALE', INFUSIONS.'logs/locale/'.LANGUAGE.'.php');
    } else {
        define('LOG_LOCALE', INFUSIONS.'logs/locale/English.php');
    }
}

if (!defined('LOGS')) {
    define('LOGS', INFUSIONS.'logs/');
}

if (!defined('DB_LOGS')) {
    define('DB_LOGS', DB_PREFIX.'logs');
}

\PHPFusion\Admins::getInstance()->setAdminPageIcons('LG', '<i class="admin-ico fa fa-fw fa-file-signature"></i>');
