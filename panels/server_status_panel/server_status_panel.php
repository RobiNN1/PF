<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
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
    require_once S_STATUS.'templates/server_status.tpl.php';

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
                'type'    => $data['server_type'],
                'host'    => $host,
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

            if ($data['server_type'] == 'minecraft') {
                $server['gq_hostname'] = remove_minecraft_formatting($server['gq_hostname']);
            }

            $servers[] = [
                'data'   => $data,
                'server' => $server
            ];
        }
    }

    render_server_status($servers);
}
