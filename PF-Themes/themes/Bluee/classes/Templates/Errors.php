<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Errors.php
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

use Bluee\Main;

class Errors {
    public static function displayErrorPage($info) {
        define('THEME_BODY', '<body class="error-page">');

        Main::hideAll();

        $context = [
            'locale' => fusion_get_locale(),
            'info'   => $info
        ];

        echo fusion_render(THEME.'twig', 'errors.twig', $context);
    }
}
