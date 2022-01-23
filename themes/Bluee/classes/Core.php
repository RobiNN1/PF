<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Core.php
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
namespace Bluee;

class Core {
    private static $options = [
        'header'     => TRUE,
        'fixed_menu' => TRUE,
        'section'    => TRUE,
        'row'        => TRUE,
        'footer'     => TRUE,
        'notices'    => TRUE
    ];

    protected static function getParam($name = NULL) {
        if (isset(self::$options[$name])) {
            return self::$options[$name];
        }

        return NULL;
    }

    public static function setParam($name, $value) {
        self::$options[$name] = $value;
    }

    public static function footerLinks() {
        return [
            ['link' => BASEDIR.'contact.php', 'title' => fusion_get_locale('CT_400', LOCALE.LOCALESET.'contact.php'), 'icon' => 'fas fa-address-book'],
        ];
    }
}
