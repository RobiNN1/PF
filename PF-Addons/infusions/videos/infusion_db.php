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
if (!defined('VID_LOCALE')) {
    if (file_exists(INFUSIONS.'videos/locale/'.LOCALESET.'videos.php')) {
        define('VID_LOCALE', INFUSIONS.'videos/locale/'.LOCALESET.'videos.php');
    } else {
        define('VID_LOCALE', INFUSIONS.'videos/locale/English/videos.php');
    }
}

// Paths
const VIDEOS = INFUSIONS.'videos/';

// Database
const DB_VIDEOS = DB_PREFIX.'videos';
const DB_VIDEO_LIKES = DB_PREFIX.'video_likes';
const DB_VIDEO_CATS = DB_PREFIX.'video_cats';

if (!defined('IS_V910')) {
    define('IS_V910', (bool)version_compare(fusion_get_settings('version'), '9.03', (strpos(fusion_get_settings('version'), '9.10') === 0 ? '>' : '<')));
}

// Admin Settings
Admins::getInstance()->setAdminPageIcons('VID', '<i class="admin-ico fa fa-fw fa-play"></i>');
Admins::getInstance()->setCommentType('VID', fusion_get_locale('vid_title', VID_LOCALE));
Admins::getInstance()->setLinkType('VID', fusion_get_settings('siteurl').'infusions/videos/videos.php?video_id=%s');

$inf_settings = get_settings('videos');
if (!empty($inf_settings['video_allow_submission']) && $inf_settings['video_allow_submission']) {
    Admins::getInstance()->setSubmitData('v', [
        'infusion_name' => 'videos',
        'link'          => INFUSIONS.'videos/video_submit.php',
        'submit_link'   => 'submit.php?stype=v',
        'submit_locale' => fusion_get_locale('vid_title', VID_LOCALE),
        'title'         => fusion_get_locale('video_submit', VID_LOCALE),
        'admin_link'    => INFUSIONS.'videos/admin.php'.fusion_get_aidlink().'&section=submissions&submit_id=%s'
    ]);
}

Admins::getInstance()->setFolderPermissions('videos', [
    'infusions/videos/videos/'             => TRUE,
    'infusions/videos/images/'             => TRUE,
    'infusions/videos/submissions/'        => TRUE,
    'infusions/videos/submissions/images/' => TRUE
]);

Admins::getInstance()->setCustomFolder('VID', [
    [
        'path'  => VIDEOS.'videos/',
        'URL'   => fusion_get_settings('siteurl').'infusions/videos/videos/',
        'alias' => 'videos'
    ],
    [
        'path'  => VIDEOS.'images/',
        'URL'   => fusion_get_settings('siteurl').'infusions/videos/images/',
        'alias' => 'videos_images'
    ]
]);
