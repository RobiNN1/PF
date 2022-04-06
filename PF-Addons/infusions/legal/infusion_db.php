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
if (!defined('LG_LOCALE')) {
    if (file_exists(INFUSIONS.'legal/locale/'.LOCALESET.'legal.php')) {
        define('LG_LOCALE', INFUSIONS.'legal/locale/'.LOCALESET.'legal.php');
    } else {
        define('LG_LOCALE', INFUSIONS.'legal/locale/English/legal.php');
    }
}

// Paths
const LEGAL = INFUSIONS.'legal/';

// Database
const DB_LEGAL = DB_PREFIX.'legal';

// Admin Settings
\PHPFusion\Admins::getInstance()->setAdminPageIcons('LG', '<i class="admin-ico fa fa-fw fa-gavel"></i>');
