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
if (!defined('CG_LOCALE')) {
    if (file_exists(INFUSIONS.'content_generator/locale/'.LANGUAGE.'.php')) {
        define('CG_LOCALE', INFUSIONS.'content_generator/locale/'.LANGUAGE.'.php');
    } else {
        define('CG_LOCALE', INFUSIONS.'content_generator/locale/English.php');
    }
}

// Admin Settings
\PHPFusion\Admins::getInstance()->setAdminPageIcons('CG', '<i class="admin-ico fa fa-fw fa-microphone"></i>');
