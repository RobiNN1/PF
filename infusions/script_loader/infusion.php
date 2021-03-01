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

$locale = fusion_get_locale('', SCL_LOCALE);

// Infusion general information
$inf_title = $locale['scl_title'];
$inf_description = '';
$inf_version = '1.0.0';
$inf_developer = 'RobiNN';
$inf_email = 'robinn@php-fusion.eu';
$inf_weburl = 'https://github.com/RobiNN1';
$inf_folder = 'script_loader';
$inf_image = 'script_loader.svg';

// Create tables
$inf_newtable[] = DB_SCRIPT_LOADER." (
    type VARCHAR(200) NOT NULL DEFAULT '',
    code LONGTEXT NOT NULL,
    PRIMARY KEY (type)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_insertdbrow[] = DB_SCRIPT_LOADER." (type, code) VALUES ('head', '')";
$inf_insertdbrow[] = DB_SCRIPT_LOADER." (type, code) VALUES ('footer', '')";

$inf_adminpanel[] = [
    'rights'   => 'SCL',
    'image'    => $inf_image,
    'title'    => $locale['scl_title'],
    'panel'    => 'admin.php',
    'page'     => 5,
    'language' => LANGUAGE
];

// Uninstallation
$inf_droptable[] = DB_SCRIPT_LOADER;
$inf_deldbrow[] = DB_ADMIN." WHERE admin_rights='SCL'";
