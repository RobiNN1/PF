<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: acp_autoloader.php
| Author: YOUR_NAMELICENSE_TEXT
+--------------------------------------------------------*/
defined('IN_FUSION') || exit;

spl_autoload_register(function ($class_name) {
    $path = THEMES.'admin_themes/ADDON_NAME/classes/'.str_replace(['\\', 'ADDON_NAME'], ['/', ''], $class_name).'.php';

    if (file_exists($path)) {
        require_once $path;
    }
});
