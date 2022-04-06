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
namespace CzechiaTheme;

class Core {
    private static $options = [
        'right'         => TRUE,
        'header'        => TRUE,
        'footer'        => TRUE,
        'footer_panels' => TRUE, // set FALSE to disable panels in footer
        'notices'       => TRUE,
        'container'     => TRUE,
        'main_row'      => TRUE
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

    public static function setLocale($key = NULL) {
        $locale = [];

        if (file_exists(THEME.'locale/'.LANGUAGE.'.php')) {
            include THEME.'locale/'.LANGUAGE.'.php';
        } else {
            include THEME.'locale/English.php';
        }

        return $locale[$key];
    }

    /**
     * Theme Copyright
     * Do not delete or change this code!
     *
     * @return string
     */
    public static function themeCopyright() {
        return '&copy; '.date('Y').' Theme created by <a href="https://github.com/RobiNN1" target="_blank">RobiNN</a>';
    }
}
