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

$locale = fusion_get_locale('', GH_LOCALE);

$inf_title       = $locale['gh_title'];
$inf_description = $locale['gh_desc'];
$inf_version     = '1.0.1';
$inf_developer   = 'RobiNN';
$inf_email       = 'robinn@php-fusion.eu';
$inf_weburl      = 'https://github.com/RobiNN1';
$inf_folder      = 'github';
$inf_image       = 'github.svg';

// Multilanguage links
$enabled_languages = makefilelist(LOCALE, '.|..', TRUE, 'folders');
if (!empty($enabled_languages)) {
    foreach ($enabled_languages as $language) {
        if (file_exists(INFUSIONS.'github/locale/'.$language.'.php')) {
            include INFUSIONS.'github/locale/'.$language.'.php';
        } else {
            include INFUSIONS.'github/locale/English.php';
        }

        $mlt_adminpanel[$language][] = [
            'title'    => $locale['gh_title'],
            'image'    => $inf_image,
            'panel'    => 'github.php',
            'rights'   => 'GH',
            'page'     => 5,
            'language' => $language
        ];

        // Delete
        $mlt_deldbrow[$language][] = DB_ADMIN." WHERE admin_rights='GH' AND admin_language='".$language."'";
    }
} else {
    $inf_adminpanel[] = [
        'title'    => $inf_title,
        'image'    => $inf_image,
        'panel'    => 'github.php',
        'rights'   => 'GH',
        'page'     => 5,
        'language' => LANGUAGE
    ];
}

$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES ('owner', 'PHPFusion', '".$inf_folder."')";

if (!column_exists('users', 'user_github_access_token')) {
    $inf_altertable[] = DB_USERS." ADD user_github_access_token VARCHAR(50)";
}

$inf_deldbrow[] = DB_ADMIN." WHERE admin_rights='GH'";
$inf_deldbrow[] = DB_SETTINGS_INF." WHERE settings_inf='".$inf_folder."'";
$inf_dropcol[] = ['table' => DB_USERS, 'column' => 'user_github_access_token'];
