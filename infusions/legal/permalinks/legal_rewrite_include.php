<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: legal_rewrite_include.php
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

$regex = [
    '%legal_type%' => '(pp|cp)',
];

$pattern = [
    'legal/%legal_type%' => 'infusions/legal/legal.php?page=%legal_type%'
];

$pattern_tables['%legal_id%'] = [
    'table'       => DB_LEGAL,
    'primary_key' => 'legal_id',
    'id'          => ['%legal_id%' => 'legal_id'],
    'columns'     => [
        '%legal_type%' => 'legal_type'
    ]
];
