<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: infusion_db.php
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

use PHPFusion\Admins;

// Locales
if (!defined('JB_LOCALE')) {
    if (file_exists(INFUSIONS.'jobs/locale/'.LOCALESET.'jobs.php')) {
        define('JB_LOCALE', INFUSIONS.'jobs/locale/'.LOCALESET.'jobs.php');
    } else {
        define('JB_LOCALE', INFUSIONS.'jobs/locale/English/jobs.php');
    }
}

// Paths
const JOBS = INFUSIONS.'jobs/';

// Database
const DB_JOBS = DB_PREFIX.'jobs';
const DB_JOB_FAQ = DB_PREFIX.'job_faq';
const DB_JOB_LOCATIONS = DB_PREFIX.'job_locations';
const DB_JOB_CATS = DB_PREFIX.'job_cats';
const DB_JOB_APPLICANTS = DB_PREFIX.'job_applicants';

// Admin Settings
Admins::getInstance()->setAdminPageIcons('JB', '<i class="admin-ico fa fa-fw fa-briefcase"></i>');

Admins::getInstance()->setFolderPermissions('jobs', [
    'infusions/jobs/cv_files/' => TRUE,
]);

Admins::getInstance()->setCustomFolder('JB', [
    [
        'path'  => JOBS.'cv_files/',
        'URL'   => fusion_get_settings('siteurl').'infusions/jobs/cv_files/',
        'alias' => 'jobs'
    ]
]);
