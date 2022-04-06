<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: theme_db.php
| Author: YOUR_NAMELICENSE_TEXT
+--------------------------------------------------------*/
defined('IN_FUSION') || exit;

$theme_title       = 'ADDON_NAME';
$theme_description = 'Description';
$theme_screenshot  = 'screenshot.png';
$theme_author      = 'YOUR_NAME';
$theme_web         = 'YOUR_WEBSITE';
$theme_license     = 'LICENSE_TITLE';
$theme_version     = '1.0.0';
$theme_folder      = 'folder_name';

// Optional for theme settings
$theme_insertdbrow[] = DB_SETTINGS_THEME." (settings_name, settings_value, settings_theme) VALUES
    ('facebook_url', '', '".$theme_folder."')
";

$theme_deldbrow[] = DB_SETTINGS_THEME." WHERE settings_theme='".$theme_folder."'";
