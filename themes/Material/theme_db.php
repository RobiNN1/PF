<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: theme_db.php
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

if (file_exists(THEMES.'Material/locale/'.LANGUAGE.'.php')) {
    $locale = fusion_get_locale('', THEMES.'Material/locale/'.LANGUAGE.'.php');
} else {
    $locale = fusion_get_locale('', THEMES.'Material/locale/English.php');
}

$theme_title       = $locale['mt_title'];
$theme_description = $locale['mt_description'];
$theme_screenshot  = 'screenshot.png';
$theme_author      = 'RobiNN';
$theme_web         = 'https://github.com/RobiNN1';
$theme_license     = 'AGPL3';
$theme_version     = '1.0.3';
$theme_folder      = 'Material';

if (!defined('DB_MT_NETWORKS')) {
    define('DB_MT_NETWORKS', DB_PREFIX.'mt_networks');
}

$theme_newtable[] = DB_MT_NETWORKS." (
    network_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    network_title VARCHAR(200) NOT NULL DEFAULT '',
    network_icon VARCHAR(40) NOT NULL,
    network_link VARCHAR(200) NOT NULL DEFAULT '',
    network_visible SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
    network_order SMALLINT(3) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (network_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$theme_insertdbrow[] = DB_SETTINGS_THEME." (settings_name, settings_value, settings_theme) VALUES
    ('social_links', 1, '".$theme_folder."'),
    ('logo', 0, '".$theme_folder."'),
    ('footer_exlude', '', '".$theme_folder."'),
    ('footer_col_1', 'AboutUs', '".$theme_folder."'),
    ('footer_col_2', 'LatestNews', '".$theme_folder."'),
    ('footer_col_3', 'LatestComments', '".$theme_folder."'),
    ('footer_col_4', 'UsersOnline', '".$theme_folder."')
";

$theme_droptable[] = DB_MT_NETWORKS;
$theme_deldbrow[] = DB_SETTINGS_THEME." WHERE settings_theme='".$theme_folder."'";
