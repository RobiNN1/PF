<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: newsletter.php
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
require_once __DIR__.'/../../maincore.php';

if (!defined('NEWSLETTER_PANEL_EXISTS')) {
    redirect(BASEDIR.'error.php?code=404');
}

require_once THEMES.'templates/header.php';

$locale = fusion_get_locale('', NSL_LOCALE);

add_to_title($locale['nsl_title']);

$subscribe = isset($_GET['subscribe']) ? $_GET['subscribe'] : NULL;
$unsubscribe = isset($_GET['unsubscribe']) ? $_GET['unsubscribe'] : NULL;

if ($subscribe) {
    $data = [
        'sub_token'     => $subscribe,
        'sub_active'    => 1,
        'sub_datestamp' => time()
    ];

    dbquery_insert(DB_NEWSLETTER_SUBS, $data, 'update');

    echo '<div class="well">'.$locale['nsl_notice_22'].'</div>';
}

if ($unsubscribe) {
    dbquery("DELETE FROM ".DB_NEWSLETTER_SUBS." WHERE sub_token=:token", [':token' => $unsubscribe]);

    $result = dbquery("SELECT * FROM ".DB_NEWSLETTER_SUBS." WHERE sub_token=:token", [':token' => $unsubscribe]);
    if (dbrows($result) !== 1) {
        echo '<div class="well">'.$locale['nsl_notice_23'].'</div>';
    }
}

require_once THEMES.'templates/footer.php';
