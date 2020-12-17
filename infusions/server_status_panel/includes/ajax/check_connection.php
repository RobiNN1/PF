<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: check_connection.php
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

if (authorize_aid()) {
    $locale = fusion_get_locale('', SS_LOCALE);

    $ip = !empty($_GET['ip']) ? $_GET['ip'] : 0;
    $port = !empty($_GET['port']) ? $_GET['port'] : 0;
    $qport = !empty($_GET['qport']) ? $_GET['qport'] : 0;
    $type = !empty($_GET['type']) ? $_GET['type'] : 0;

    if (!empty($ip) && !empty($port) && !empty($qport) && !empty($type)) {
        require_once S_STATUS.'includes/functions.php';

        $GameQ = new \GameQ\GameQ();
        $GameQ->setOption('timeout', 1);

        $host = $ip.':'.$port;

        $GameQ->addServer([
            'type'    => $type,
            'host'    => $host,
            'options' => [
                'query_port' => $qport
            ]
        ]);

        $results = $GameQ->process();
        $result = $results[$host]['gq_online'] == 1 ? $locale['ss_001'] : $locale['ss_002'];
    } else {
        $result = $locale['ss_003'];
    }

    header('Content-Type: application/json');
    echo json_encode(['result' => $result]);
}

function authorize_aid() {
    $aid = (string)filter_input(INPUT_GET, 'aid');

    if (defined('iAUTH') && isset($aid) && iAUTH == $aid) {
        return TRUE;
    }

    return FALSE;
}
