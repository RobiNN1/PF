<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: 2.0.upgrade.inc
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
$inf_version = '2.0';

$check_team = [
    'name'       => " ADD name VARCHAR(50) NOT NULL DEFAULT '' AFTER userid",
    'info'       => " ADD info TEXT NOT NULL AFTER profession",
    'image'      => " ADD image VARCHAR(120) NOT NULL AFTER info",
    'item_order' => " ADD item_order SMALLINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER image"
];

foreach ($check_team as $key => $value) {
    if (!column_exists(DB_TEAM, $key, FALSE)) {
        $inf_altertable[] = DB_TEAM.$value;
    }
}
