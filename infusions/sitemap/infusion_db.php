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

// Locales
if (!defined('SMG_LOCALE')) {
    if (file_exists(INFUSIONS.'sitemap/locale/'.LANGUAGE.'.php')) {
        define('SMG_LOCALE', INFUSIONS.'sitemap/locale/'.LANGUAGE.'.php');
    } else {
        define('SMG_LOCALE', INFUSIONS.'sitemap/locale/English.php');
    }
}

// Database
const DB_SITEMAP = DB_PREFIX.'sitemap';
const DB_SITEMAP_LINKS = DB_PREFIX.'sitemap_links';

// Admin Settings
\PHPFusion\Admins::getInstance()->setAdminPageIcons('SMG', '<i class="admin-ico fa fa-fw fa-sitemap"></i>');

if (defined('SITEMAP_EXISTS')) {
    require_once INFUSIONS.'sitemap/includes/SitemapGenerator.php';

    $smg = new SitemapGenerator();

    if (is_file($smg->sitemap_file) && $smg->sitemap_settings['auto_update'] == 1) {
        if ((time() - filemtime($smg->sitemap_file)) > $smg->sitemap_settings['update_interval']) {
            $smg->generateXml();
        }
    }
}
