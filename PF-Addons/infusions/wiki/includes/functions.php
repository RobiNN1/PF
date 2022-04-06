<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: functions.php
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

function get_wiki_query(array $filters = []) {
    return "SELECT w.*, wc.*, u.user_id, u.user_name, u.user_status, u.user_avatar, u.user_level, u.user_joined
        FROM ".DB_WIKI." AS w
        LEFT JOIN ".DB_USERS." AS u ON w.wiki_user=u.user_id
        LEFT JOIN ".DB_WIKI_CATS." AS wc ON w.wiki_cat=wc.wiki_cat_id
        ".(multilang_table('WIKI') ? "WHERE ".in_group('w.wiki_language', LANGUAGE)." AND ".in_group('wc.wiki_cat_language', LANGUAGE)." AND " : 'WHERE ').groupaccess('w.wiki_access')."
        AND w.wiki_status=1 AND wc.wiki_cat_status=1 AND ".groupaccess('wc.wiki_cat_access')."
        ".(!empty($filters['condition']) ? ' AND '.$filters['condition'] : '')."
        GROUP BY w.wiki_id
        ".(!empty($filters['order']) ? 'ORDER BY '.$filters['order'] : '')."
        ".(!empty($filters['limit']) ? 'LIMIT '.$filters['limit'] : '')."
    ";
}

function validate_wiki($id) {
    if (isnum($id)) {
        if ($id < 1) {
            return 1;
        } else {
            return dbcount("('wiki_id')", DB_WIKI, "wiki_id='".intval($id)."'");
        }
    }

    return FALSE;
}

function validate_wiki_cat($id) {
    if (isnum($id)) {
        if ($id < 1) {
            return 1;
        } else {
            return dbcount("('wiki_cat_id')", DB_WIKI_CATS, "wiki_cat_id='".intval($id)."'");
        }
    }

    return FALSE;
}

function parse_wiki_text($text) {
    require_once WIKI.'includes/Parsedown.php';
    $parsedown = new Parsedown;

    $text = stripslashes($text);
    $text = $parsedown->text($text);
    $text = htmlspecialchars_decode($text);
    $text = parsesmileys($text);
    $text = parseubb($text, '', FALSE);
    $text = nl2br($text);

    return $text;
}
