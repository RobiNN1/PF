<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: infusion_db.php
| Author: YOUR_NAMELICENSE_TEXT
+--------------------------------------------------------*/
defined('IN_FUSION') || exit;

// Locales
define('ADMIN_RIGHTS_LOCALE', fusion_get_inf_locale_path('', INFUSIONS.'folder_name/locale/', FALSE));

// Paths
const INF_EXIST = INFUSIONS.'folder_name/';

// Database
const DB_INFUSION_TABLE = DB_PREFIX.'infusion_table';

// Admin Settings
\PHPFusion\Admins::getInstance()->setAdminPageIcons('ADMIN_RIGHTS', '<i class="admin-ico fa fa-fw fa-play"></i>');
// \PHPFusion\Admins::getInstance()->setCommentType('ADMIN_RIGHTS', fusion_get_locale('LOCALE_PREFIX_title', ADMIN_RIGHTS_LOCALE));
// \PHPFusion\Admins::getInstance()->setLinkType('ADMIN_RIGHTS', fusion_get_settings('siteurl').'infusions/folder_name/folder_name.php?item_id=%s');

// Submissions
/*$inf_settings = get_settings('folder_name');
if (
    (!empty($inf_settings['infusion_allow_submission']) && $inf_settings['infusion_allow_submission']) &&
    (!empty($inf_settings['infusion_submission_access']) && checkgroup($inf_settings['infusion_submission_access']))
) {

    \PHPFusion\Admins::getInstance()->setSubmitData('x', [
        'folder_name'   => 'folder_name',
        'link'          => INFUSIONS.'folder_name/infusion_submit.php',
        'submit_link'   => 'submit.php?stype=x',
        'submit_locale' => fusion_get_locale('LOCALE_PREFIX_title', ADMIN_RIGHTS_LOCALE),
        'title'         => fusion_get_locale('Submit', ADMIN_RIGHTS_LOCALE),
        'admin_link'    => INFUSIONS.'folder_name/admin.php'.fusion_get_aidlink().'&section=submissions&submit_id=%s'
    ]);
}*/

/*\PHPFusion\Admins::getInstance()->setFolderPermissions('folder_name', [
    'infusions/folder_name/images/'      => TRUE,
    'infusions/folder_name/submissions/' => TRUE
]);*/

/*if (method_exists(\PHPFusion\Admins::getInstance(), 'setCustomFolder')) {
    \PHPFusion\Admins::getInstance()->setCustomFolder('ADMIN_RIGHTS', [
        [
            'path'  => INF_EXIST.'folder_name/',
            'URL'   => fusion_get_settings('siteurl').'infusions/folder_name/images/',
            'alias' => 'folder_name'
        ],
    ]);
}*/
