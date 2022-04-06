<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Core.php
| Author: PHP Fusion Inc
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
namespace AtomXTheme;

class Core {
    private static $options = [
        'right'                => TRUE,
        'right_content'        => '',
        'section_header'       => '',
        'section_header_class' => '',
        'greybody'             => FALSE,
        'container_fluid'      => FALSE
    ];
    private static $instance = NULL;
    public $locale = [];
    public $userdata = [];
    public $settings = [];
    public $aidlink = '';

    public function __construct() {
        $this->locale = self::setLocale();
        $this->locale += fusion_get_locale();
        $this->userdata = fusion_get_userdata();
        $this->settings = fusion_get_settings();
        $this->aidlink = fusion_get_aidlink();
    }

    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new static();
            self::$instance->setLocale();
        }

        return self::$instance;
    }

    protected static function getParam($name = NULL) {
        if (isset(self::$options[$name])) {
            return self::$options[$name];
        }

        return NULL;
    }

    public static function setParam($name, $value) {
        self::$options[$name] = $value;
    }

    public function setLocale() {
        if (empty($this->locale)) {
            $locale = [];

            if (file_exists(THEME.'locale/'.LANGUAGE.'.php')) {
                include THEME.'locale/'.LANGUAGE.'.php';
            } else {
                include THEME.'locale/English.php';
            }

            $this->locale = $locale;
        }

        return $this->locale;
    }

    /**
     * Theme Copyright
     * Do not delete or change this code!
     *
     * @return string
     */
    public static function themeCopyright() {
        return '&copy; '.date('Y').' Theme designed by <a href="https://phpfusion.com" target="_blank">PHP Fusion Inc</a>, Ported for v9 by <a href="https://github.com/RobiNN1" target="_blank">RobiNN</a>';
    }
}
