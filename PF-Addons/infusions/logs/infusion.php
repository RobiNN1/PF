<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: infusion.php
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

$locale = fusion_get_locale('', LOG_LOCALE);

// Infusion general information
$inf_title = $locale['log_title'];
$inf_description = '';
$inf_version = '1.0.0';
$inf_developer = 'RobiNN';
$inf_email = '';
$inf_weburl = 'https://github.com/RobiNN1';
$inf_folder = 'logs';
$inf_image = 'logs.svg';

// Create tables
$inf_newtable[] = DB_LOGS." (
    log_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    log_user MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    log_ip VARCHAR(45) NOT NULL DEFAULT '0.0.0.0',
    log_url TEXT NOT NULL,
    log_code INT(3) UNSIGNED NOT NULL DEFAULT '0',
    log_time INT(10) UNSIGNED NOT NULL DEFAULT '0',
    log_useragent TEXT NOT NULL,
    PRIMARY KEY (log_id),
    KEY log_user (log_user)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

// Insert settings
$settings = [
    'online_maxcount' => 0,
    'online_maxtime'  => 0
];

foreach ($settings as $name => $value) {
    $inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES ('".$name."', '".$value."', '".$inf_folder."')";
}

$inf_adminpanel[] = [
    'rights'   => 'LOG',
    'image'    => $inf_image,
    'title'    => $locale['log_title'],
    'panel'    => 'admin.php',
    'page'     => 5,
    'language' => LANGUAGE
];

// Uninstallation
$inf_droptable[] = DB_LOGS;
$inf_deldbrow[] = DB_ADMIN." WHERE admin_rights='LOG'";
$inf_deldbrow[] = DB_SETTINGS_INF." WHERE settings_inf='".$inf_folder."'";
