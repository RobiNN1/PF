<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: infusion_db.php
| Author: YOUR_NAME
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

if (!defined('ADMIN_RIGHTS_LOCALE')) {
    if (file_exists(INFUSIONS.'folder_name/locale/'.LOCALESET.'.php')) {
        define('ADMIN_RIGHTS_LOCALE', INFUSIONS.'folder_name/locale/'.LOCALESET.'.php');
    } else {
        define('ADMIN_RIGHTS_LOCALE', INFUSIONS.'folder_name/locale/English.php');
    }
}

if (!defined('INF_EXISTS')) {
    define('INF_EXIST', INFUSIONS.'folder_name/');
}

if (!defined('DB_INFUSION_TABLE')) {
    define('DB_INFUSION_TABLE', DB_PREFIX.'infusion_table');
}

// Admin Settings
\PHPFusion\Admins::getInstance()->setAdminPageIcons('ADMIN_RIGHTS', '<i class="admin-ico fa fa-fw fa-play"></i>'); // FontAwesomwe icon
// \PHPFusion\Admins::getInstance()->setCommentType('ADMIN_RIGHTS', fusion_get_locale('LOCALE_PREFIX_title', ADMIN_RIGHTS_LOCALE)); // Comments
// \PHPFusion\Admins::getInstance()->setLinkType('ADMIN_RIGHTS', fusion_get_settings('siteurl').'infusions/folder_name/folder_name.php?item_id=%s'); // Ratings

// Submissions
/*$inf_settings = get_settings('folder_name');
if (!empty($inf_settings['infusion_allow_submission']) && $inf_settings['infusion_allow_submission']) {
    \PHPFusion\Admins::getInstance()->setSubmitData('x', [
        'folder_name'   => 'folder_name',
        'link'          => INFUSIONS.'folder_name/infusion_submit.php',
        'submit_link'   => 'submit.php?stype=x',
        'submit_locale' => fusion_get_locale('LOCALE_PREFIX_title', ADMIN_RIGHTS_LOCALE),
        'title'         => fusion_get_locale('Submit', ADMIN_RIGHTS_LOCALE),
        'admin_link'    => INFUSIONS.'folder_name/admin.php'.fusion_get_aidlink().'&amp;section=submissions&amp;submit_id=%s'
    ]);
}*/

// Shows CHMOD in Admin Dashboard > System Admin > PHP Info: Folder Permissions
/*\PHPFusion\Admins::getInstance()->setFolderPermissions('folder_name', [
    'infusions/folder_name/images/'      => TRUE,
    'infusions/folder_name/submissions/' => TRUE
]);*/

// Shows folder in File Manager
/*if (method_exists(\PHPFusion\Admins::getInstance(), 'setCustomFolder')) {
    \PHPFusion\Admins::getInstance()->setCustomFolder('ADMIN_RIGHTS', [
        [
            'path'  => INF_EXIST.'folder_name/',
            'URL'   => fusion_get_settings('siteurl').'infusions/folder_name/images/',
            'alias' => 'folder_name'
        ],
    ]);
}*/
