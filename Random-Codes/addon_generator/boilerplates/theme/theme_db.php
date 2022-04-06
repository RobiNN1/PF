<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: theme_db.php
| Author: YOUR_NAME
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

$theme_title       = 'ADDON_NAME';
$theme_description = 'Description';
$theme_screenshot  = 'screenshot.png';
$theme_author      = 'YOUR_NAME';
$theme_web         = 'YOUR_WEBSITE';
$theme_license     = 'AGPL3';
$theme_version     = '1.0.0';
$theme_folder      = 'folder_name';

// Optional for theme settings
$theme_insertdbrow[] = DB_SETTINGS_THEME." (settings_name, settings_value, settings_theme) VALUES
    ('facebook_url', '', '".$theme_folder."')
";

$theme_deldbrow[] = DB_SETTINGS_THEME." WHERE settings_theme='".$theme_folder."'";
