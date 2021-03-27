<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: UsersOnline.php
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
namespace MaterialTheme\Footer;

use MaterialTheme\Core;

class UsersOnline extends Core {
    private static $guests = 0;
    private static $members = [];

    public static function panel() {
        $locale = fusion_get_locale();

        ob_start();

        echo '<h3 class="title">'.self::setLocale('uo_01').'</h3>';

        $result = dbquery("SELECT o.*, u.user_id, u.user_name, u.user_status, u.user_level
            FROM ".DB_ONLINE." o
            LEFT JOIN ".DB_USERS." u ON o.online_user=u.user_id
        ");

        while ($data = dbarray($result)) {
            if ($data['online_user'] == 0) {
                self::$guests++;
            } else {
                self::$members[$data['user_id']] = [
                    $data['user_id'],
                    $data['user_name'],
                    $data['user_status'],
                    $data['user_level']
                ];
            }
        }

        // Total users
        echo str_replace([
            '[TOTAL_ONLINE]',
            '[MEMBERS]',
            '[GUESTS]'
        ], [
            format_word(self::$guests + count(self::$members), $locale['fmt_user']),
            format_word(number_format(count(self::$members), 0), $locale['fmt_member']),
            format_word(self::$guests, $locale['fmt_guest'])
        ], self::setLocale('uo_02'));
        echo '<br/>';

        // Current online
        echo '<i class="fa fa-user fa-fw"></i> '.self::setLocale('uo_03').': ';

        if (!empty(self::$members)) {
            echo implode(', ', array_map(function ($member) {
                return profile_link($member[0], $member[1], $member[2], self::color($member[3]));
            }, self::$members));
        } else {
            echo self::setLocale('uo_04');
        }

        echo '<br/>';

        // New members
        $data = dbarray(dbquery("SELECT user_id, user_name, user_status FROM ".DB_USERS." WHERE user_status='0' ORDER BY user_joined DESC LIMIT 0,1"));
        echo '<i class="fa fa-user-plus fa-fw"></i> ';
        echo self::setLocale('uo_05').': '.profile_link($data['user_id'], $data['user_name'], $data['user_status']);
        echo '<br/>';

        // Visited
        $visited = number_format(dbcount("(user_id)", DB_USERS, "user_status<='1' AND user_lastvisit > UNIX_TIMESTAMP(CURDATE())"));
        $total_members = number_format(dbcount("(user_id)", DB_USERS, "user_status<='1'"), 0);
        echo '<i class="fa fa-users fa-fw"></i> '.self::setLocale('uo_06').': '.$visited.'/'.$total_members;
        echo '<br/>';

        $i = 0;
        $result = dbquery("SELECT user_id, user_name, user_level, user_status, user_lastvisit, user_avatar
            FROM ".DB_USERS."
            WHERE user_lastvisit > UNIX_TIMESTAMP(CURDATE()) AND user_status = '0'
            ORDER BY user_lastvisit DESC
        ");

        if (dbrows($result) != 0) {
            while ($data = dbarray($result)) {
                echo $i > 0 ? ', ' : '';
                echo profile_link($data['user_id'], $data['user_name'], $data['user_status'], self::color($data['user_level']));
                $i++;
            }
        }

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    private static function color($user_level) {
        switch ($user_level) {
            case -103:
                $class = 'superadmin';
                break;
            case -102:
                $class = 'admin';
                break;
            case -101:
                $class = 'member';
                break;
            default:
                $class = '';
        }

        return $class.'-color';
    }
}
