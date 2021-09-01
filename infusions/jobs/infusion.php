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

$locale = fusion_get_locale('', JB_LOCALE);

// Infusion general information
$inf_title = $locale['jb_title'];
$inf_description = $locale['jb_desc'];
$inf_version = '1.0.0';
$inf_developer = 'RobiNN';
$inf_email = 'robinn@php-fusion.eu';
$inf_weburl = 'https://github.com/RobiNN1';
$inf_folder = 'jobs';
$inf_image = 'jobs.svg';

// Create tables
$inf_newtable[] = DB_JOBS." (
    job_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    job_cat MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    job_location MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    job_title VARCHAR(200) NOT NULL DEFAULT '',
    job_description TEXT NOT NULL,
    PRIMARY KEY (job_id),
    KEY job_cat (job_cat),
    KEY job_location (job_location)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[] = DB_JOB_APPLICANTS." (
    job_applicant_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    job_applicant_datestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
    job_applicant_firstname VARCHAR(20) NOT NULL DEFAULT '',
    job_applicant_lastname VARCHAR(20) NOT NULL DEFAULT '',
    job_applicant_email VARCHAR(100) NOT NULL DEFAULT '',
    job_applicant_phone VARCHAR(20) NOT NULL DEFAULT '',
    job_applicant_cv VARCHAR(100) NOT NULL DEFAULT '',
    job_applicant_message TEXT NOT NULL,
    job_applicant_hearaboutus VARCHAR(50) NOT NULL DEFAULT '',
    job_applicant_internship TINYINT(1) NOT NULL DEFAULT '0',
    job_applicant_job TINYINT(1) NOT NULL DEFAULT '0',
    job_applicant_status TINYINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY(job_applicant_id),
    KEY job_applicant_job (job_applicant_job)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[] = DB_JOB_FAQ." (
    job_faq_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    job_faq_job MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    job_faq_question VARCHAR(200) NOT NULL DEFAULT '',
    job_faq_answer TEXT NOT NULL,
    job_faq_order SMALLINT(3) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY(job_faq_id),
    KEY job_cat (job_faq_job)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[] = DB_JOB_LOCATIONS." (
    job_location_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    job_location_name VARCHAR(200) NOT NULL DEFAULT '',
    PRIMARY KEY(job_location_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[] = DB_JOB_CATS." (
    job_cat_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    job_cat_name VARCHAR(200) NOT NULL DEFAULT '',
    job_cat_language VARCHAR(50) NOT NULL DEFAULT '".LANGUAGE."',
    PRIMARY KEY(job_cat_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

// Insert settings
$settings = [
    'cv_max_b'    => 5242880,
    'cv_types'    => '.doc,.docx,.docm,.pdf',
    'internship'  => 1,
    'email'       => fusion_get_settings('siteemail'),
    'captcha'     => 1,
    'required_cv' => 1
];

foreach ($settings as $name => $value) {
    $inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES ('".$name."', '".$value."', '".$inf_folder."')";
}

// Multilanguage table
$inf_mlt[] = [
    'title'  => $locale['jb_title'],
    'rights' => 'JB',
];

// Multilanguage links
$enabled_languages = makefilelist(LOCALE, '.|..', TRUE, 'folders');
if (!empty($enabled_languages)) {
    foreach ($enabled_languages as $language) {
        if (file_exists(INFUSIONS.'jobs/locale/'.$language.'/jobs.php')) {
            include INFUSIONS.'jobs/locale/'.$language.'/jobs.php';
        } else {
            include INFUSIONS.'jobs/locale/English/jobs.php';
        }

        $mlt_adminpanel[$language][] = [
            'rights'   => 'JB',
            'image'    => $inf_image,
            'title'    => $locale['jb_title'],
            'panel'    => 'admin.php',
            'page'     => 5,
            'language' => $language
        ];

        // Add
        $mlt_insertdbrow[$language][] = DB_SITE_LINKS." (link_name, link_url, link_visibility, link_position, link_window, link_order, link_status, link_language) VALUES('".$locale['jb_title']."', 'infusions/jobs/jobs.php', '0', '2', '0', '2', '1', '".$language."')";

        // Delete
        $mlt_deldbrow[$language][] = DB_SITE_LINKS." WHERE link_url='infusions/jobs/jobs.php' AND link_language='".$language."'";
        $mlt_deldbrow[$language][] = DB_ADMIN." WHERE admin_rights='JB' AND admin_language='".$language."'";
    }
} else {
    $inf_adminpanel[] = [
        'rights'   => 'JB',
        'image'    => $inf_image,
        'title'    => $locale['jb_title'],
        'panel'    => 'admin.php',
        'page'     => 5,
        'language' => LANGUAGE
    ];

    $inf_insertdbrow[] = DB_SITE_LINKS." (link_name, link_url, link_visibility, link_position, link_window, link_order, link_status, link_language) VALUES('".$locale['jb_title']."', 'infusions/jobs/jobs.php', '0', '2', '0', '2', '1', '".LANGUAGE."')";
}

// Uninstallation
$inf_droptable[] = DB_JOBS;
$inf_droptable[] = DB_JOB_APPLICANTS;
$inf_droptable[] = DB_JOB_FAQ;
$inf_droptable[] = DB_JOB_LOCATIONS;
$inf_droptable[] = DB_JOB_CATS;
$inf_deldbrow[] = DB_ADMIN." WHERE admin_rights='JB'";
$inf_deldbrow[] = DB_SETTINGS_INF." WHERE settings_inf='".$inf_folder."'";
$inf_deldbrow[] = DB_SITE_LINKS." WHERE link_url='infusions/jobs/jobs.php'";
$inf_deldbrow[] = DB_LANGUAGE_TABLES." WHERE mlt_rights='JB'";
$inf_delfiles[] = JOBS.'cv_files/';
