<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Maintenance.php
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
namespace Bluee\Templates;

class Maintenance {
    public static function displayMaintenance($info) {
        define('THEME_BODY', '<body class="error-page">');

        $context = [
            'locale'     => fusion_get_locale(),
            'info'       => $info,
            'admin'      => iADMIN && (iUSER_RIGHTS != '' || iUSER_RIGHTS != 'C'),
            'admin_link' => iADMIN && (iUSER_RIGHTS != '' || iUSER_RIGHTS != 'C') ? ADMIN.'index.php'.fusion_get_aidlink() : ''
        ];

        echo fusion_render(THEME.'twig', 'maintenance.twig', $context);
    }
}
