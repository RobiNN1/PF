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

if (!defined('NSL_LOCALE')) {
    if (file_exists(INFUSIONS.'newsletter_panel/locale/'.LOCALESET.'newsletter.php')) {
        define('NSL_LOCALE', INFUSIONS.'newsletter_panel/locale/'.LOCALESET.'newsletter.php');
    } else {
        define('NSL_LOCALE', INFUSIONS.'newsletter_panel/locale/English/newsletter.php');
    }
}

if (!defined('NEWSLETTER')) {
    define('NEWSLETTER', INFUSIONS.'newsletter_panel/');
}

if (!defined('DB_NEWSLETTER_HEADERS')) {
    define('DB_NEWSLETTER_HEADERS', DB_PREFIX.'newsletter_headers');
}

if (!defined('DB_NEWSLETTER_SMTP')) {
    define('DB_NEWSLETTER_SMTP', DB_PREFIX.'newsletter_smtp');
}

if (!defined('DB_NEWSLETTER_SUBS')) {
    define('DB_NEWSLETTER_SUBS', DB_PREFIX.'newsletter_subs');
}

if (!defined('DB_NEWSLETTER_TEMPLATES')) {
    define('DB_NEWSLETTER_TEMPLATES', DB_PREFIX.'newsletter_templates');
}

require_once NEWSLETTER.'includes/functions.php';

// Admin Settings
\PHPFusion\Admins::getInstance()->setAdminPageIcons('NSL', '<i class="admin-ico fa fa-fw fa-newspaper"></i>');

\PHPFusion\Admins::getInstance()->setFolderPermissions('newsletter_panel', [
    'infusions/newsletter_panel/email_templates/'         => TRUE,
    'infusions/newsletter_panel/email_templates/uploads/' => TRUE
]);

if (method_exists(\PHPFusion\Admins::getInstance(), 'setCustomFolder')) {
    \PHPFusion\Admins::getInstance()->setCustomFolder('NSL', [
        [
            'path'  => NEWSLETTER.'email_templates/',
            'URL'   => fusion_get_settings('siteurl').'infusions/newsletter_panel/email_templates/',
            'alias' => 'newsletter'
        ]
    ]);
}
