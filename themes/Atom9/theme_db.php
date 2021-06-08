<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: theme_db.php
| Author: Frederick MC Chan
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

$theme_title = 'Atom9';
$theme_description = 'Atom 9 Ignition Pack.';
$theme_screenshot = 'screenshot.png';
$theme_author = 'Frederick MC Chan & RobiNN';
$theme_web = 'https://phpfusion.com';
$theme_license = 'AGPL3';
$theme_version = '1.5.2';
$theme_folder = 'Atom9';

if (!defined('ATOM9_LOCALE')) {
    if (file_exists(THEME.'locale/'.LANGUAGE.'.php')) {
        define('ATOM9_LOCALE', THEME.'locale/'.LANGUAGE.'.php');
    } else {
        define('ATOM9_LOCALE', THEME.'locale/English.php');
    }
}

$theme_insertdbrow[] = DB_SETTINGS_THEME." (settings_name, settings_value, settings_theme) VALUES
    ('ignition_pack', 'StarCity', '".$theme_folder."'),
    ('facebook_url', '', '".$theme_folder."'),
    ('twitter_url', '', '".$theme_folder."'),
    ('panel_exlude', '', '".$theme_folder."'),
    ('footer_col1', 'AboutUs.php', '".$theme_folder."'),
    ('footer_col2', 'LatestArticles.php', '".$theme_folder."'),
    ('footer_col3', 'LatestNews.php', '".$theme_folder."'),
    ('footer_col4', 'Users.php', '".$theme_folder."'),
    ('2columns_layout', 0, '".$theme_folder."'),
    ('column_side', 'LEFT', '".$theme_folder."')
";

$theme_deldbrow[] = DB_SETTINGS_THEME." WHERE settings_theme='".$theme_folder."'";
