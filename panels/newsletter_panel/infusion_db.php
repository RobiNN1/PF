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
if (!defined('NSL_LOCALE')) {
    if (file_exists(INFUSIONS.'newsletter_panel/locale/'.LOCALESET.'newsletter.php')) {
        define('NSL_LOCALE', INFUSIONS.'newsletter_panel/locale/'.LOCALESET.'newsletter.php');
    } else {
        define('NSL_LOCALE', INFUSIONS.'newsletter_panel/locale/English/newsletter.php');
    }
}

// Paths
const NEWSLETTER = INFUSIONS.'newsletter_panel/';

// Database
const DB_NEWSLETTER_HEADERS = DB_PREFIX.'newsletter_headers';
const DB_NEWSLETTER_SMTP = DB_PREFIX.'newsletter_smtp';
const DB_NEWSLETTER_SUBS = DB_PREFIX.'newsletter_subs';
const DB_NEWSLETTER_TEMPLATES = DB_PREFIX.'newsletter_templates';

require_once NEWSLETTER.'includes/functions.php';

// Admin Settings
Admins::getInstance()->setAdminPageIcons('NSL', '<i class="admin-ico fa fa-fw fa-newspaper"></i>');

Admins::getInstance()->setFolderPermissions('newsletter_panel', [
    'infusions/newsletter_panel/email_templates/'         => TRUE,
    'infusions/newsletter_panel/email_templates/uploads/' => TRUE
]);

Admins::getInstance()->setCustomFolder('NSL', [
    [
        'path'  => NEWSLETTER.'email_templates/',
        'URL'   => fusion_get_settings('siteurl').'infusions/newsletter_panel/email_templates/',
        'alias' => 'newsletter'
    ]
]);
