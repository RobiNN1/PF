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

$locale = fusion_get_locale('', SS_LOCALE);

// Infusion general information
$inf_title = $locale['ss_title'];
$inf_description = $locale['ss_desc'];
$inf_version = '1.0.1';
$inf_developer = 'RobiNN';
$inf_email = 'robinn@php-fusion.eu';
$inf_weburl = 'https://github.com/RobiNN1';
$inf_folder = 'server_status_panel';
$inf_image = 'server.svg';

// Create tables
$inf_newtable[] = DB_SERVER_STATUS." (
    server_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    server_ip VARCHAR(45) NOT NULL DEFAULT '',
    server_port INT(12) UNSIGNED NOT NULL DEFAULT '0',
    server_qport INT(12) UNSIGNED NOT NULL DEFAULT '0',
    server_type VARCHAR(20) NOT NULL DEFAULT '',
    server_order SMALLINT(3) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (server_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

// Insert panel
$inf_insertdbrow[] = DB_PANELS." (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_url_list, panel_restriction, panel_languages) VALUES('".$locale['ss_title']."', 'server_status_panel', '', '3', '5', 'file', '0', '1', '1', '', '3', '".fusion_get_settings('enabled_languages')."')";

// Multilanguage links
$enabled_languages = makefilelist(LOCALE, '.|..', TRUE, 'folders');
if (!empty($enabled_languages)) {
    foreach ($enabled_languages as $language) {
        if (file_exists(INFUSIONS.'server_status_panel/locale/'.$language.'.php')) {
            include INFUSIONS.'server_status_panel/locale/'.$language.'.php';
        } else {
            include INFUSIONS.'server_status_panel/locale/English.php';
        }

        $mlt_adminpanel[$language][] = [
            'rights'   => 'SS',
            'image'    => $inf_image,
            'title'    => $locale['ss_title'],
            'panel'    => 'admin.php',
            'page'     => 5,
            'language' => $language
        ];

        // Delete
        $mlt_deldbrow[$language][] = DB_ADMIN." WHERE admin_rights='SS' AND admin_language='".$language."'";
    }
} else {
    $inf_adminpanel[] = [
        'rights'   => 'SS',
        'image'    => $inf_image,
        'title'    => $locale['ss_title'],
        'panel'    => 'admin.php',
        'page'     => 5,
        'language' => LANGUAGE
    ];
}

// Uninstallation
$inf_droptable[] = DB_SERVER_STATUS;
$inf_deldbrow[] = DB_ADMIN." WHERE admin_rights='SS'";
$inf_deldbrow[] = DB_PANELS." WHERE panel_filename='server_status_panel'";
