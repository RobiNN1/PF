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

// Locales
if (!defined('TM_LOCALE')) {
    if (file_exists(INFUSIONS.'team/locale/'.LOCALESET.'team.php')) {
        define('TM_LOCALE', INFUSIONS.'team/locale/'.LOCALESET.'team.php');
    } else {
        define('TM_LOCALE', INFUSIONS.'team/locale/English/team.php');
    }
}

// Paths
const TEAM = INFUSIONS.'team/';

// Database
const DB_TEAM = DB_PREFIX.'team';

// Admin Settings
Admins::getInstance()->setAdminPageIcons('TM', '<i class="admin-ico fa fa-fw fa-users"></i>');

Admins::getInstance()->setFolderPermissions('team', [
    'infusions/team/images/' => TRUE
]);

Admins::getInstance()->setCustomFolder('TM', [
    [
        'path'  => TEAM.'images/',
        'URL'   => fusion_get_settings('siteurl').'infusions/team/images/',
        'alias' => 'team'
    ]
]);
