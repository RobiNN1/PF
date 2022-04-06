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

$ignore_ips = [
    '127.0.0.1'
];

/**
 * @param array $ignore_ips
 */
function save_log($ignore_ips = []) {
    $data = [
        'log_user'      => iGUEST ? 0 : fusion_get_userdata('user_id'),
        'log_ip'        => isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'],
        'log_url'       => (string)htmlspecialchars($_SERVER['REQUEST_URI']),
        'log_code'      => http_response_code(),
        'log_time'      => time(),
        'log_useragent' => !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''
    ];

    $count = dbcount("(log_id)", DB_LOGS, "log_user=".$data['log_user']." AND log_time =".$data['log_time']) != 1;
    if ($count && $data['log_user'] !== 1 && !ip_inarray($data['log_ip'], $ignore_ips)) {
        dbquery_insert(DB_LOGS, $data, 'save');
    }
}

/**
 * @param $ip
 * @param $array
 *
 * @return bool
 */
function ip_inarray($ip, $array) {
    foreach ($array as $ar) {
        if (strpos($ip, $ar) === 0) {
            return TRUE;
        }
    }

    return FALSE;
}

/**
 * @param $useragent
 *
 * @return \UAParser\Result\Client
 * @throws \UAParser\Exception\FileNotFoundException
 */
function get_data_from_ua($useragent) {
    $parser = UAParser\Parser::create();
    return $parser->parse($useragent);
}

function max_users_online() {
    $settings = get_settings('logs');
    $count = dbcount('(online_user)', DB_ONLINE);

    if ($settings['online_maxcount'] < $count) {
        $data = [
            'online_maxcount' => $count,
            'online_maxtime'  => time()
        ];

        foreach ($data as $settings_name => $settings_value) {
            $db = [
                'settings_name'  => $settings_name,
                'settings_value' => $settings_value,
                'settings_inf'   => 'logs'
            ];

            dbquery_insert(DB_SETTINGS_INF, $db, 'update', ['primary_key' => 'settings_name']);
        }
    }
}
