<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: newsletter_rewrite_include.php
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
    '%token%' => '([0-9a-zA-Z._\W]+)'
];

$pattern = [
    'newsletter'                     => 'infusions/newsletter_panel/newsletter.php',
    'newsletter/subscribe/%token%'   => 'infusions/newsletter_panel/newsletter.php?subscribe=%token%',
    'newsletter/unsubscribe/%token%' => 'infusions/newsletter_panel/newsletter.php?unsubscribe=%token%'
];
