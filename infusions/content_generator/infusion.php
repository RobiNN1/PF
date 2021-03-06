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

$locale = fusion_get_locale('', CG_LOCALE);

// Infusion general information
$inf_title       = $locale['cg_title'];
$inf_description = $locale['cg_desc'];
$inf_version     = '1.1.6';
$inf_developer   = 'RobiNN';
$inf_email       = 'robinn@php-fusion.eu';
$inf_weburl      = 'https://github.com/RobiNN1';
$inf_folder      = 'content_generator';
$inf_image       = 'content_generator.svg';

// Multilanguage links
$enabled_languages = makefilelist(LOCALE, '.|..', TRUE, 'folders');
if (!empty($enabled_languages)) {
    foreach ($enabled_languages as $language) {
        if (file_exists(INFUSIONS.'content_generator/locale/'.$language.'.php')) {
            include INFUSIONS.'content_generator/locale/'.$language.'.php';
        } else {
            include INFUSIONS.'content_generator/locale/English.php';
        }

        $mlt_adminpanel[$language][] = [
            'rights'   => 'CG',
            'image'    => $inf_image,
            'title'    => $locale['cg_title'],
            'panel'    => 'content_generator.php',
            'page'     => 5,
            'language' => $language
        ];

        // Delete
        $mlt_deldbrow[$language][] = DB_ADMIN." WHERE admin_rights='CG' AND admin_language='".$language."'";
    }
} else {
    $inf_adminpanel[] = [
        'rights'   => 'CG',
        'image'    => $inf_image,
        'title'    => $inf_title,
        'panel'    => 'content_generator.php',
        'page'     => 5,
        'language' => LANGUAGE
    ];
}

// Uninstallation
$inf_deldbrow[] = DB_ADMIN." WHERE admin_rights='CG'";
