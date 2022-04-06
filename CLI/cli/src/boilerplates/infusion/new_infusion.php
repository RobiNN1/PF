<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: new_infusion.php
| Author: YOUR_NAMELICENSE_TEXT
+--------------------------------------------------------*/
require_once __DIR__.'/../../maincore.php';

if (!defined('INF_EXIST_EXISTS')) {
    redirect(BASEDIR.'error.php?code=404');
}

require_once THEMES.'templates/header.php';
require_once INCLUDES.'infusions_include.php';

$locale = fusion_get_locale('', ADMIN_RIGHTS_LOCALE);
$inf_settings = get_settings('folder_name');

// Your code goes here

require_once THEMES.'templates/footer.php';
