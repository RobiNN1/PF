<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: infusion.php
| Author: YOUR_NAMELICENSE_TEXT
+--------------------------------------------------------*/
defined('IN_FUSION') || exit;

$locale = fusion_get_locale('', ADMIN_RIGHTS_LOCALE);

// Infusion general information
$inf_title = $locale['LOCALE_PREFIX_title'];
$inf_description = $locale['LOCALE_PREFIX_desc'];
$inf_version = '1.0.0';
$inf_developer = 'YOUR_NAME';
$inf_email = 'YOUR_EMAIL';
$inf_weburl = 'YOUR_WEBSITE';
$inf_folder = 'folder_name';
$inf_image = 'icon.svg';

// Create tables
/*$inf_newtable[] = DB_INFUSION_TABLE." (
    id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    field2 TINYINT(5) UNSIGNED DEFAULT '1' NOT NULL,
    field3 VARCHAR(200) DEFAULT '' NOT NULL,
    field4 VARCHAR(50) DEFAULT '' NOT NULL,
    PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";*/

// Insert data
//$inf_insertdbrow[] = DB_INFUSION_TABLE." (field1, field2, field3, field4) VALUES('', '', '', '')";

// Insert panel
//$inf_insertdbrow[] = DB_PANELS." (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_url_list, panel_restriction, panel_languages) VALUES ('Panel Name', 'new_infusion_panel', '', '3', '1', 'file', '0', '1', '1', '', '3', '".fusion_get_settings('enabled_languages')."')";

// Insert settings
/*$settings = [
    'setting1' => 1,
    'setting2' => 43200
];

foreach ($settings as $name => $value) {
    $inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('".$name."', '".$value."', '".$inf_folder."')";
}*/

$inf_adminpanel[] = [
    'rights'   => 'ADMIN_RIGHTS',
    'image'    => $inf_image,
    'title'    => $locale['LOCALE_PREFIX_title'],
    'panel'    => 'admin.php',
    'page'     => 5,
    'language' => LANGUAGE
];

// Multilanguage table
/*$inf_mlt[] = [
    'title'  => $locale['LOCALE_PREFIX_title'],
    'rights' => 'ADMIN_RIGHTS'
];*/

// Multilanguage links
$enabled_languages = makefilelist(LOCALE, '.|..', TRUE, 'folders');
if (!empty($enabled_languages)) {
    foreach ($enabled_languages as $language) {
        if (file_exists(INFUSIONS.$inf_folder.'/locale/'.$language.'.php')) {
            include INFUSIONS.$inf_folder.'/locale/'.$language.'.php';
        } else {
            include INFUSIONS.$inf_folder.'/locale/English.php';
        }

        /*$mlt_adminpanel[$language][] = [
            'rights'   => 'ADMIN_RIGHTS',
            'image'    => $inf_image,
            'title'    => $locale['LOCALE_PREFIX_title'],
            'panel'    => 'admin.php',
            'page'     => 5,
            'language' => $language
        ];*/

        // Add
        $mlt_insertdbrow[$language][] = DB_SITE_LINKS." (link_name, link_url, link_visibility, link_position, link_window, link_order, link_status, link_language) VALUES('".$locale['LOCALE_PREFIX_link1']."', 'infusions/folder_name/file.php', '0', '2', '0', '2', '1', '".$language."')";

        // Delete
        $mlt_deldbrow[$language][] = DB_SITE_LINKS." WHERE link_url='infusions/folder_name/file.php' AND link_language='".$language."'";
        $mlt_deldbrow[$language][] = DB_ADMIN." WHERE admin_rights='ADMIN_RIGHTS' AND admin_language='".$language."'";
    }
} else {
    /*$inf_adminpanel[] = [
        'rights'   => 'ADMIN_RIGHTS',
        'image'    => $inf_image,
        'title'    => $locale['LOCALE_PREFIX_title'],
        'panel'    => 'admin.php',
        'page'     => 5,
        'language' => LANGUAGE
    ];*/

    $inf_insertdbrow[] = DB_SITE_LINKS." (link_name, link_url, link_visibility, link_position, link_window, link_order, link_status, link_language) VALUES('".$locale['LOCALE_PREFIX_link1']."', 'infusions/folder_name/file.php', '0', '2', '0', '2', '1', '".LANGUAGE."')";
}

// Uninstallation
$inf_droptable[] = DB_INFUSION_TABLE;
$inf_deldbrow[] = DB_ADMIN." WHERE admin_rights='ADMIN_RIGHTS'";
//$inf_deldbrow[] = DB_COMMENTS." WHERE comment_type='ADMIN_RIGHTS'";
//$inf_deldbrow[] = DB_RATINGS." WHERE rating_type='ADMIN_RIGHTS'";
//$inf_deldbrow[] = DB_PANELS." WHERE panel_filename='new_infusion_panel'";
//$inf_deldbrow[] = DB_SETTINGS_INF." WHERE settings_inf='".$inf_folder."'";
$inf_deldbrow[] = DB_SITE_LINKS." WHERE link_url='infusions/folder_name/file.php'";
//$inf_deldbrow[] = DB_SUBMISSIONS." WHERE submit_type='xx'";
//$inf_deldbrow[] = DB_LANGUAGE_TABLES." WHERE mlt_rights='ADMIN_RIGHTS'";
$inf_delfiles[] = INFUSIONS.'folder_name/images/';
