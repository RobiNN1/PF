<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
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

if (!defined('RC3_LOCALE')) {
    if (file_exists(INFUSIONS.'grecaptcha3/locale/'.LANGUAGE.'.php')) {
        define('RC3_LOCALE', INFUSIONS.'grecaptcha3/locale/'.LANGUAGE.'.php');
    } else {
        define('RC3_LOCALE', INFUSIONS.'grecaptcha3/locale/English.php');
    }
}

\PHPFusion\Admins::getInstance()->setAdminPageIcons('RC3', '<i class="admin-ico fa fa-fw fa-shield-alt"></i>');
