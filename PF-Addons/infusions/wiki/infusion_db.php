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
define('WIKI_LOCALE', fusion_get_inf_locale_path('wiki.php', INFUSIONS.'wiki/locale/'));

// Paths
const WIKI = INFUSIONS.'wiki/';
const IMAGES_WIKI = INFUSIONS.'wiki/images/';

// Database
const DB_WIKI = DB_PREFIX.'wiki';
const DB_WIKI_CATS = DB_PREFIX.'wiki_cats';
const DB_WIKI_CHANGELOG = DB_PREFIX.'wiki_changelog';
const DB_WIKI_STATS = DB_PREFIX.'wiki_stats';

// Admin Settings
Admins::getInstance()->setAdminPageIcons('WIKI', '<i class="admin-ico fa fa-fw fa-wikipedia-w"></i>');

$inf_settings = get_settings('wiki');
if (
    (!empty($inf_settings['wiki_allow_submission']) && $inf_settings['wiki_allow_submission']) &&
    (!empty($inf_settings['wiki_submission_access']) && checkgroup($inf_settings['wiki_submission_access']))
) {
    Admins::getInstance()->setSubmitData('w', [
        'infusion_name' => 'wiki',
        'link'          => INFUSIONS.'wiki/wiki_submit.php',
        'submit_link'   => 'submit.php?stype=w',
        'submit_locale' => fusion_get_locale('wiki_title', WIKI_LOCALE),
        'title'         => fusion_get_locale('docs_submit', WIKI_LOCALE),
        'admin_link'    => INFUSIONS.'wiki/admin.php'.fusion_get_aidlink().'&section=submissions&submit_id=%s'
    ]);
}

Admins::getInstance()->setFolderPermissions('wiki', [
    'infusions/wiki/images/' => TRUE
]);

Admins::getInstance()->setCustomFolder('WIKI', [
    [
        'path'  => IMAGES_WIKI,
        'URL'   => fusion_get_settings('siteurl').'infusions/wiki/images/',
        'alias' => 'wiki'
    ]
]);
