<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: add_settimgs_v9.php
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
require_once __DIR__.'/maincore.php';
require_once THEMES.'templates/header.php';

$_GET['localeset'] = 'English';
require_once CLASSES.'PHPFusion/Installer/Lib/Core.settings.php';

$added = '';
$settings = fusion_get_settings();
$table_rows = get_table_rows('settings');

foreach ($table_rows['insert'] as $count => $inserts) {
    $key = $inserts['settings_name'];
    $value = $inserts['settings_value'];
    if (!isset($settings[$key])) {
        $result = dbquery("INSERT INTO ".DB_PREFIX."settings (settings_name, settings_value) VALUES ('$key', '$value')");
        $added .= 'Added \''.$key.'\' => \''.$value.'\', <br>';
    }
}
echo $added;

require_once THEMES.'templates/footer.php';
