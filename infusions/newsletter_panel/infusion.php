<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
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

$locale = fusion_get_locale('', NSL_LOCALE);
$settings = fusion_get_settings();

// Infusion general information
$inf_title = $locale['nsl_title'];
$inf_description = $locale['nsl_desc'];
$inf_version = '1.0.1';
$inf_developer = 'RobiNN';
$inf_email = 'robinn@php-fusion.eu';
$inf_weburl = 'https://github.com/RobiNN1';
$inf_folder = 'newsletter_panel';
$inf_image = 'newsletter.svg';

// Create tables
$inf_newtable[] = DB_NEWSLETTER_HEADERS." (
    header_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    header_name VARCHAR(255) NOT NULL DEFAULT '',
    header_value VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY (header_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[] = DB_NEWSLETTER_SMTP." (
    smtp_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    smtp_host VARCHAR(255) NOT NULL DEFAULT '',
    smtp_port VARCHAR(3) NOT NULL DEFAULT '',
    smtp_name VARCHAR(255) NOT NULL DEFAULT '',
    smtp_pass VARCHAR(255) NOT NULL DEFAULT '',
    smtp_secure VARCHAR(3) NOT NULL DEFAULT '',
    smtp_timeout MEDIUMINT(100) UNSIGNED NOT NULL DEFAULT '0',
    smtp_active TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (smtp_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[] = DB_NEWSLETTER_SUBS." (
    sub_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    sub_email VARCHAR(255) NOT NULL DEFAULT '',
    sub_token VARCHAR(32) NOT NULL DEFAULT '',
    sub_active TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    sub_datestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (sub_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[] = DB_NEWSLETTER_TEMPLATES." (
    tpl_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    tpl_name VARCHAR(255) NOT NULL DEFAULT '',
    tpl_body TEXT NOT NULL,
    tpl_style TEXT NOT NULL,
    tpl_datestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
    tpl_priority TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    tpl_file VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY (tpl_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

// Insert panel
$inf_insertdbrow[] = DB_PANELS." (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_url_list, panel_restriction, panel_languages) VALUES('".$locale['nsl_title']."', 'newsletter_panel', '', '3', '5', 'file', '0', '1', '1', '', '3', '".fusion_get_settings('enabled_languages')."')";

// Insert settings
$settings = [
    'sender_name'     => $settings['sitename'],
    'sender_email'    => $settings['siteemail'],
    'show_email'      => 1,
    'add_dkim'        => 0,
    'dkim_domain'     => $settings['siteurl'],
    'dkim_private'    => '',
    'dkim_selector'   => 'newsletter',
    'dkim_passphrase' => '',
    'dkim_identity'   => '',
    'how_to_send'     => 'php',  // php|smtp|sendmail
    'sendmail_path'   => '/usr/sbin/sendmail',
    'charset'         => 'utf-8',
    'content_type'    => 'html', // html|plain
    'test_email'      => '',
    'visibility'      => 0
];

foreach ($settings as $name => $value) {
    $inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('".$name."', '".$value."', '".$inf_folder."')";
}

if (!column_exists('users', 'user_newsletter')) {
    $inf_altertable[] = DB_USERS." ADD user_newsletter TINYINT(1) NOT NULL DEFAULT 0";
    $last_category = dbresult(dbquery("SELECT MAX(field_cat_id) FROM ".DB_USER_FIELD_CATS), 0);
    $inf_insertdbrow[] = DB_USER_FIELDS." (field_title, field_name, field_cat, field_type, field_order) VALUES ('Newsletter', 'user_newsletter', ".$last_category.", 'file', 4)";
}

$inf_adminpanel[] = [
    'rights'   => 'NSL',
    'image'    => $inf_image,
    'title'    => $locale['nsl_title'],
    'panel'    => 'admin.php',
    'page'     => 5,
    'language' => LANGUAGE
];

// Uninstallation
$inf_droptable[] = DB_NEWSLETTER_HEADERS;
$inf_droptable[] = DB_NEWSLETTER_SMTP;
$inf_droptable[] = DB_NEWSLETTER_SUBS;
$inf_droptable[] = DB_NEWSLETTER_TEMPLATES;
$inf_deldbrow[] = DB_ADMIN." WHERE admin_rights='NSL'";
$inf_deldbrow[] = DB_PANELS." WHERE panel_filename='".$inf_folder."'";
$inf_deldbrow[] = DB_SETTINGS_INF." WHERE settings_inf='".$inf_folder."'";

if (column_exists('users', 'user_newsletter')) {
    $inf_dropcol[] = ['table' => DB_USERS, 'column' => 'user_newsletter'];
    $inf_deldbrow[] = DB_USER_FIELDS." WHERE field_name='user_newsletter'";
}
