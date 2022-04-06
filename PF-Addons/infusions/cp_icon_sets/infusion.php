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

// Infusion general information
$inf_title = 'Icon sets';
$inf_description = '';
$inf_version = '1.0.0';
$inf_developer = 'RobiNN';
$inf_email = '';
$inf_weburl = 'https://github.com/RobiNN1';
$inf_folder = 'cp_icon_sets';
$inf_image = 'cis.svg';

// Insert settings
$settings = [
    'icon_set' => 'fluency'
];

foreach ($settings as $name => $value) {
    $inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('".$name."', '".$value."', '".$inf_folder."')";
}

$inf_adminpanel[] = [
    'rights'   => 'CIS',
    'image'    => $inf_image,
    'title'    => $inf_title,
    'panel'    => 'admin.php',
    'page'     => 5,
    'language' => LANGUAGE
];

// Uninstallation
$inf_deldbrow[] = DB_ADMIN." WHERE admin_rights='CIS'";
$inf_deldbrow[] = DB_SETTINGS_INF." WHERE settings_inf='".$inf_folder."'";
