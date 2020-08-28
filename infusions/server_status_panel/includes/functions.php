<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.phpfusion.com/
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

require_once S_STATUS.'includes/vendor/autoload.php';

function protocols_list() {
    $protocols_path = S_STATUS.'includes/vendor/austinb/gameq/src/GameQ/Protocols/';
    $dir = dir($protocols_path);
    $protocols = [];

    while (FALSE !== ($entry = $dir->read())) {
        if (!is_file($protocols_path.$entry)) {
            continue;
        }

        $reflection = new ReflectionClass('\\GameQ\\Protocols\\'.pathinfo($entry, PATHINFO_FILENAME));

        if (!$reflection->IsInstantiable()) {
            continue;
        }

        $class = $reflection->newInstance();

        $protocols[$class->name()] = [
            'name'  => $class->nameLong(),
            'state' => $class->state(),
        ];

        unset($class);
    }

    unset($dir);

    ksort($protocols);
    $games = [];
    foreach ($protocols as $gameq => $info) {
        $games[$gameq] = htmlentities($info['name']);
    }

    return $games;
}
