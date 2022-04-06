<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: urls.php
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

require_once LOGS.'vendor/autoload.php';
require_once LOGS.'includes/functions.php';
require_once LOGS.'includes/IPInfo.php';

$locale = fusion_get_locale();
$settings = fusion_get_settings();

echo '<div class="m-b-20">';
    $online = get_settings('logs');
    echo strtr($locale['log_02'], [
        '[COUNT]' => $online['online_maxcount'],
        '[DATE]'  => showdate('shortdate', $online['online_maxtime'])
    ]);
echo '</div>';

$ipinfo = new IPInfo();

$data = [
    'log_user'      => '',
    'log_ip'        => '',
    'log_url'       => '',
    'log_code'      => '',
    'log_time'      => '',
    'log_useragent' => ''
];

if (isset($_POST['delete_log'])) {
    dbquery("DELETE FROM ".DB_LOGS);
    addnotice('success', $locale['log_13']);
    redirect(FUSION_REQUEST);
}

if (isset($_POST['delete_cache'])) {
    $files = glob(LOGS.'cache/*.cache');

    if ($files) {
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    addnotice('success', $locale['log_13']);
    redirect(FUSION_REQUEST);
}

$filter_values = [
    'filter_user' => !empty($_POST['filter_user']) ? form_sanitizer($_POST['filter_user'], 0, 'filter_user') : '',
];

echo openform('log_filter', 'post', FUSION_REQUEST);
echo '<div class="display-inline-block">';
    $user_opts = [];
    $result = dbquery("SELECT l.log_user, u.user_id, u.user_name, u.user_status FROM ".DB_LOGS." l LEFT JOIN ".DB_USERS." u ON l.log_user = u.user_id GROUP BY u.user_id ORDER BY user_name ASC");
    if (dbrows($result) > 0) {
        while ($data = dbarray($result)) {
            if ($data['log_user'] == 0) {
                $user_opts['guest'] = $locale['user_guest'];
            } else {
                $user_opts[$data['log_user']] = $data['user_name'];
            }
        }
    }
    echo form_select('filter_user', '', $filter_values['filter_user'], [
        'allowclear'  => TRUE,
        'placeholder' => '- '.$locale['log_03'].' -',
        'options'     => $user_opts
    ]);
echo '</div>';

echo '<div class="pull-right">';
    echo form_button('delete_cache', $locale['delete'].' Cache', 'delete_cache', ['class' => 'btn-danger m-r-10']);
    echo form_button('delete_log', $locale['delete'].' Log', 'delete_log', ['class' => 'btn-danger']);
echo '</div>';

add_to_jquery("$('#filter_user').bind('change', function(e) {
    $(this).closest('form').submit();
});");
echo closeform();

$sql_condition = '';
$sql_params = [];
$search_string = [];

if (!empty($_POST['filter_user'])) {
    $search_string['log_user'] = [
        'input' => $filter_values['filter_user'] == 'guest' ? 0 : $filter_values['filter_user'], 'operator' => '='
    ];
}

if (!empty($search_string)) {
    foreach ($search_string as $key => $values) {
        $sql_condition .= "AND $key ".$values['operator'].' :'.$key;
        $sql_params[':'.$key] = ($values['operator'] == 'LIKE' ? '%' : '').$values['input'].($values['operator'] == "LIKE" ? '%' : '');
    }
}

$limit = 20;
$total_rows = dbcount('(log_id)', DB_LOGS);
$rowstart = isset($_GET['rowstart']) && ($_GET['rowstart'] <= $total_rows) ? $_GET['rowstart'] : 0;

$result = dbquery("SELECT * FROM ".DB_LOGS." WHERE 1 ".$sql_condition." ORDER BY log_time DESC LIMIT $rowstart, $limit", $sql_params);
$rows = dbrows($result);

if ($rows > 0) {
    if ($total_rows > $rows) {
        echo makepagenav($rowstart, $limit, $total_rows, $limit, clean_request('', ['aid'], TRUE).'&');
    }

    echo '<div class="table-responsive"><table class="table table-striped">';
        echo '<thead><tr>';
            echo '<th>'.$locale['log_04'].'</th>';
            echo '<th>'.$locale['log_05'].'</th>';
            echo '<th>IP</th>';
            echo '<th>URL</th>';
            echo '<th>'.$locale['log_05a'].'</th>';
            echo '<th>OS</th>';
            echo '<th>'.$locale['log_06'].'</th>';
        echo '</tr></thead>';
        echo '<tbody>';

        while ($data = dbarray($result)) {
            $uap = get_data_from_ua($data['log_useragent']);
            $user = fusion_get_user($data['log_user']);
            $ip = $data['log_ip'];

            echo '<tr>';
                echo '<td>'.(!empty($user) ? profile_link($user['user_id'], $user['user_name'], $user['user_status']) : $locale['user_guest']).'</td>';
                echo '<td>'.timer($data['log_time']).'</td>';
                echo '<td><a data-toggle="collapse" href="#log'.$data['log_id'].'" aria-expanded="false" aria-controls="log'.$data['log_id'].'">'.$ip.'</a></td>';
                echo '<td title="'.htmlspecialchars_decode($data['log_url']).'">'.trimlink(htmlspecialchars_decode($data['log_url']), 70).'</td>';
                echo '<td>'.$data['log_code'].'</td>';
                echo '<td>'.$uap->os->toString().'</td>';
                echo '<td>'.$uap->ua->toString().'</td>';
            echo '</tr>';

            if ($ip !== '127.0.0.1') {
                $ip_data = $ipinfo->getIPInfo($ip);

                echo '<tr class="collapse" id="log'.$data['log_id'].'">';
                    echo '<td colspan="6">';
                        if (!empty($ip_data['asn'])) {
                            echo '<div><b>ASN</b>: '.$ip_data['asn']['org'].'</div>';
                            echo '<div><b>'.$locale['log_07'].'</b>: '.$ip_data['asn']['network'].'</div>';
                            echo '<div><b>'.$locale['log_08'].'</b>: '.$ip_data['asn']['hostname'].'</div>';
                        }

                        if (!empty($ip_data['country'])) {
                            echo '<div><b>'.$locale['log_09'].'</b>: '.$ip_data['country']['name'].' ('.$ip_data['country']['iso_code'].')</div>';
                        }

                        if (!empty($ip_data['city'])) {
                            echo '<div><b>'.$locale['log_10'].'</b>: '.$ip_data['city']['name'].'</div>';
                            echo '<div><b>'.$locale['log_11'].'</b>: '.$ip_data['city']['timezone'].'</div>';
                        }
                        echo '<div><b>UA</b>: '.$data['log_useragent'].'</div>';
                    echo '</td>';
                echo '</tr>';
            }
        }

        echo '</tbody>';
    echo '</table></div>';

    if ($total_rows > $rows) {
        echo makepagenav($rowstart, $limit, $total_rows, $limit, clean_request('', ['aid'], TRUE).'&');
    }
} else {
    echo '<div class="text-center">'.$locale['log_12'].'</div>';
}
