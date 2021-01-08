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

$locale = fusion_get_locale('', RC3_LOCALE);

// Infusion general information
$inf_title       = $locale['rc3_title'];
$inf_description = $locale['rc3_desc'];
$inf_version     = '1.0.0';
$inf_developer   = 'RobiNN';
$inf_email       = 'robinn@php-fusion.eu';
$inf_weburl      = 'https://github.com/RobiNN1';
$inf_folder      = 'grecaptcha3';
$inf_image       = 'recaptcha.svg';

// Insert settings
$settings = [
    'private_key' => '',
    'public_key'  => '',
    'score'       => '0.5'
];

foreach ($settings as $name => $value) {
    $inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES ('".$name."', '".$value."', '".$inf_folder."')";
}

$inf_adminpanel[] = [
    'rights'   => 'RC3',
    'image'    => $inf_image,
    'title'    => $locale['rc3_title'],
    'panel'    => 'admin.php',
    'page'     => 5,
    'language' => LANGUAGE
];

// Uninstallation
$inf_deldbrow[] = DB_ADMIN." WHERE admin_rights='RC3'";
$inf_deldbrow[] = DB_SETTINGS_INF." WHERE settings_inf='".$inf_folder."'";
