<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: user_newsletter_include_var.php
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

$user_field_api_version = "1.0.0";
$user_field_name = $locale['uf_newsletter'];
$user_field_desc = $locale['uf_newsletter_desc'];
$user_field_dbname = 'user_newsletter';
$user_field_group = 2;
$user_field_dbinfo = "TINYINT(1) NOT NULL DEFAULT 0";
