<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: stats.php
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
require_once __DIR__.'/../../../../maincore.php';

$result_msg = [];
$page_id = (int)filter_input(INPUT_GET, 'page_id', FILTER_VALIDATE_INT);

if (!empty($page_id) && isnum($page_id)) {
    $result = dbquery("SELECT wiki_id FROM ".DB_WIKI." WHERE wiki_id=:page_id", [':page_id' => $page_id]);

    if (dbrows($result) > 0) {
        $data = dbarray($result);

        if (iMEMBER) {
            $userdata = fusion_get_userdata();
            $action = (string)filter_input(INPUT_GET, 'action');

            if (!empty($action)) {
                if (\defender::safe()) {
                    switch ($action) {
                        case 'yes':
                            $result_msg = update('yes');
                            break;
                        case 'no':
                            $result_msg = update('no');
                            break;
                        case 'remove_yes':
                        case 'remove_no':
                            dbquery("DELETE FROM ".DB_WIKI_STATS." WHERE stat_page=:wiki_id AND stat_user=:user_id", [':wiki_id' => $data['wiki_id'], ':user_id' => $userdata['user_id']]);
                            $result_msg['status'] = 'Removed!';
                            break;
                        default:
                            break;
                    }
                }
            }
        } else {
            $result_msg['status'] = 'You must login';
        }
    } else {
        $result_msg['status'] = 'Page not found';
    }
} else {
    $result_msg['status'] = 'Missing/Wrong ID';
}

function update($action) {
    global $userdata, $data;

    $result2 = dbquery("SELECT * FROM ".DB_WIKI_STATS." WHERE stat_page=:wiki_id AND stat_user=:user_id", [
        ':wiki_id' => $data['wiki_id'],
        ':user_id' => $userdata['user_id']
    ]);

    $data2 = dbarray($result2);
    $stat_option = $data2['stat_option'];

    $opt = NULL;
    $msg = '';
    if ($action === 'yes') {
        $opt = 1;
        $msg = 'Yes!';
    } else if ($action === 'no') {
        $opt = 0;
        $msg = 'No!';
    }

    $result_msg = [];

    if (dbrows($result2) == 0) {
        dbquery_insert(DB_WIKI_STATS, [
            'stat_page'   => $data['wiki_id'],
            'stat_user'   => $userdata['user_id'],
            'stat_option' => $opt
        ], 'save');

        $result_msg['status'] = $msg;
    }

    if ($stat_option !== $opt) {
        dbquery("UPDATE ".DB_WIKI_STATS." SET stat_option=:opt WHERE stat_page=:wiki_id AND stat_user=:user_id", [
            ':wiki_id' => $data['wiki_id'],
            ':user_id' => $userdata['user_id'],
            ':opt'     => $opt
        ]);

        $result_msg['status'] = $msg;
    }

    return $result_msg;
}

header('Content-Type: application/json');

echo json_encode($result_msg);
