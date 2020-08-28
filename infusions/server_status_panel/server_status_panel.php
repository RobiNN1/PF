<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: server_status_panel.php
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

if (defined('SERVER_STATUS_PANEL_EXISTS')) {
    $locale = fusion_get_locale('', SS_LOCALE);
    $inf_settings = get_settings('server_status_panel');

    require_once S_STATUS.'includes/functions.php';

    $result = dbquery("SELECT * FROM ".DB_SERVER_STATUS." ORDER BY server_order");

    $GameQ = new \GameQ\GameQ();
    $GameQ->setOption('timeout', 3);

    Phpfastcache\CacheManager::setDefaultConfig(new Phpfastcache\Config\ConfigurationOption([
        'path' => __DIR__.'/cache'
    ]));

    $InstanceCache = Phpfastcache\CacheManager::getInstance('files');

    $servers = [];

    if (dbrows($result)) {
        while ($data = dbarray($result)) {
            $host = $data['server_ip'].':'.$data['server_port'];

            $GameQ->addServer([
                'type' => $data['server_type'],
                'host' => $host,
                'options' => [
                    'query_port' => $data['server_qport']
                ]
            ]);

            $results = $GameQ->process();
            $cache_key = str_replace('.', '_', $data['server_ip'].'_'.$data['server_port']);

            $CachedString = $InstanceCache->getItem($cache_key);
            if (!$CachedString->isHit()) {
                $CachedString->set($results[$host])->expiresAfter(60);
                $InstanceCache->save($CachedString);
            }

            $server = $CachedString->get();

            $servers[] = [
                'data'   => $data,
                'server' => $server
            ];
        }
    }

    if (!function_exists('render_server_status')) {
        function render_server_status($servers) {
            $locale = fusion_get_locale();

            echo '<div class="table-responsive"><table class="table table-striped table-bordered">';
                echo '<thead><tr>';
                    echo '<td>'.$locale['ss_006'].'</td>';
                    echo '<td>IP:Port</td>';
                    echo '<td>'.$locale['ss_007'].'</td>';
                    echo '<td>'.$locale['ss_008'].'</td>';
                echo '</tr></thead>';
                echo '<tbody>';
                    if (!empty($servers)) {
                        foreach ($servers as $item) {
                            $data = $item['data'];
                            $server = $item['server'];
                            $host = $data['server_ip'].':'.$data['server_port'];

                            echo '<tr>';
                                echo '<td class="no-break text-overflow-hide">';
                                    if (file_exists(S_STATUS.'icons/'.$data['server_type'].'.png')) {
                                        echo '<img alt="'.$server['gq_name'].'" title="'.$server['gq_name'].'" class="img-responsive display-inline m-r-10" style="width: 32px;" src="'.S_STATUS.'icons/'.$data['server_type'].'.png">';
                                    }
                                    echo '<i class="fa fa-circle text-'.($server['gq_online'] ? 'success' : 'danger').'"></i> '.$server['gq_hostname'];
                                echo '</td>';
                                echo '<td>'.$host.(!empty($server['gq_joinlink']) ? ' <a href="'.$server['gq_joinlink'].'"><i class="fa fa-external-link-alt"></i></a>' : '').'</td>';
                                echo '<td>'.(!empty($server['gq_numplayers']) && !empty($server['gq_maxplayers']) ? $server['gq_numplayers'].'/'.$server['gq_maxplayers'] : 'N/A').'</td>';
                                echo '<td>'.(!empty($server['gq_mapname']) ? $server['gq_mapname'] : 'N/A').'</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="4" class="text-center">'.$locale['ss_notice_05'].'</td></tr>';
                    }
                echo '</tbody>';
            echo '</table></div>';
        }
    }

    render_server_status($servers);
}
