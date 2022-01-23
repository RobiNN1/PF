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

$locale = fusion_get_locale('', WIKI_LOCALE);

// Infusion general information
$inf_title = $locale['wiki_title'];
$inf_description = $locale['wiki_desc'];
$inf_version = '2.0.0';
$inf_developer = 'RobiNN';
$inf_email = 'robinn@php-fusion.eu';
$inf_weburl = 'https://github.com/RobiNN1';
$inf_folder = 'wiki';
$inf_image = 'wiki.svg';

// Create tables
$inf_newtable[] = DB_WIKI." (
    wiki_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    wiki_name VARCHAR(50) NOT NULL DEFAULT '',
    wiki_type VARCHAR(5) NOT NULL DEFAULT '',
    wiki_cat MEDIUMINT(8) NOT NULL DEFAULT '0',
    wiki_parent MEDIUMINT(8) NOT NULL DEFAULT '0',
    wiki_description TEXT NOT NULL,
    wiki_datestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
    wiki_order MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '1',
    wiki_user MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    wiki_status TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    wiki_access VARCHAR(50) NOT NULL DEFAULT '0',
    wiki_edited MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    wiki_edited_datestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
    wiki_versions VARCHAR(50) NOT NULL DEFAULT '',
    wiki_language VARCHAR(50) NOT NULL DEFAULT '".LANGUAGE."',
    PRIMARY KEY (wiki_id),
    KEY wiki_cat (wiki_cat)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[] = DB_WIKI_CATS." (
    wiki_cat_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    wiki_cat_name VARCHAR(50) NOT NULL DEFAULT '',
    wiki_cat_parent MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    wiki_cat_description TEXT NOT NULL,
    wiki_cat_status TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    wiki_cat_access VARCHAR(50) NOT NULL DEFAULT '0',
    wiki_cat_order SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
    wiki_cat_language VARCHAR(50) NOT NULL DEFAULT '".LANGUAGE."',
    PRIMARY KEY (wiki_cat_id),
    KEY wiki_cat_parent (wiki_cat_parent)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[] = DB_WIKI_CHANGELOG." (
    wiki_changelog_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    wiki_changelog_version VARCHAR(10) NOT NULL DEFAULT '',
    wiki_changelog_codename VARCHAR(10) NOT NULL DEFAULT '',
    wiki_changelog_published INT(10) UNSIGNED NOT NULL DEFAULT '0',
    wiki_changelog_changes TEXT NOT NULL,
    wiki_changelog_download VARCHAR(50) NOT NULL DEFAULT '',
    wiki_changelog_status TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    wiki_changelog_access VARCHAR(50) NOT NULL DEFAULT '0',
    wiki_changelog_language VARCHAR(50) NOT NULL DEFAULT '".LANGUAGE."',
    PRIMARY KEY (wiki_changelog_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[] = DB_WIKI_STATS." (
    stat_id MEDIUMINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    stat_page MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    stat_user MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    stat_option SMALLINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (stat_id),
    KEY stat_page (stat_page),
    KEY stat_user (stat_user)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

// Insert settings
$settings = [
    'wiki_allow_submission'  => 1,
    'wiki_submission_access' => USER_LEVEL_MEMBER,
    'is_helpful_stat'        => 1
];

foreach ($settings as $name => $value) {
    $inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES ('".$name."', '".$value."', '".$inf_folder."')";
}

// Multilanguage table
$inf_mlt[] = [
    'title'  => $locale['wiki_title'],
    'rights' => 'WIKI'
];

// Multilanguage links
$enabled_languages = makefilelist(LOCALE, '.|..', TRUE, 'folders');
if (!empty($enabled_languages)) {
    foreach ($enabled_languages as $language) {
        if (file_exists(INFUSIONS.'wiki/locale/'.$language.'/wiki.php')) {
            include INFUSIONS.'wiki/locale/'.$language.'/wiki.php';
        } else {
            include INFUSIONS.'wiki/locale/English/wiki.php';
        }

        $mlt_adminpanel[$language][] = [
            'rights'   => 'WIKI',
            'image'    => $inf_image,
            'title'    => $locale['wiki_title'],
            'panel'    => 'admin.php',
            'page'     => 1,
            'language' => $language
        ];

        // Add
        $mlt_insertdbrow[$language][] = DB_SITE_LINKS." (link_name, link_url, link_visibility, link_position, link_window, link_order, link_status, link_language) VALUES ('".$locale['wiki_title']."', 'infusions/wiki/wiki.php', '0', '2', '0', '2', '1', '".$language."')";

        // Delete
        $mlt_deldbrow[$language][] = DB_SITE_LINKS." WHERE link_url='infusions/wiki/wiki.php' AND link_language='".$language."'";
        $mlt_deldbrow[$language][] = DB_WIKI_CATS." WHERE wiki_cat_language='".$language."'";
        $mlt_deldbrow[$language][] = DB_ADMIN." WHERE admin_rights='WIKI' AND admin_language='".$language."'";
    }
} else {
    $inf_adminpanel[] = [
        'rights'   => 'WIKI',
        'image'    => $inf_image,
        'title'    => $locale['wiki_title'],
        'panel'    => 'admin.php',
        'page'     => 1,
        'language' => LANGUAGE
    ];

    $inf_insertdbrow[] = DB_SITE_LINKS." (link_name, link_url, link_visibility, link_position, link_window, link_order, link_status, link_language) VALUES ('".$locale['wiki_title']."', 'infusions/wiki/wiki.php', '0', '2', '0', '2', '1', '".LANGUAGE."')";
}

// Uninstallation
$inf_droptable[] = DB_WIKI;
$inf_droptable[] = DB_WIKI_CATS;
$inf_droptable[] = DB_WIKI_CHANGELOG;
$inf_droptable[] = DB_WIKI_STATS;
$inf_deldbrow[] = DB_ADMIN." WHERE admin_rights='WIKI'";
$inf_deldbrow[] = DB_SETTINGS_INF." WHERE settings_inf='".$inf_folder."'";
$inf_deldbrow[] = DB_SITE_LINKS." WHERE link_url='infusions/wiki/wiki.php'";
$inf_deldbrow[] = DB_SUBMISSIONS." WHERE submit_type='w'";
$inf_deldbrow[] = DB_LANGUAGE_TABLES." WHERE mlt_rights='WIKI'";
$inf_delfiles[] = IMAGES_WIKI;
