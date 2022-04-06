<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Core.php
| Author: Frederick MC Chan
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
namespace Atom9Theme;

class Core {
    protected static $instance = NULL;
    private static $options = [
        'notices'       => TRUE,
        'left'          => TRUE,
        'left_content'  => '',
        'right'         => TRUE,
        'right_content' => '',
        'panels'        => TRUE,
        'atom_banner'   => TRUE
    ];

    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new static();
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

    public function getIgnitionPacks($ignition_pack) {
        $css = file_exists(THEME.'IgnitionPacks/'.$ignition_pack.'/styles.min.css') ? THEME.'IgnitionPacks/'.$ignition_pack.'/styles.min.css' : THEME.'IgnitionPacks/'.$ignition_pack.'/styles.css';
        add_to_head('<link rel="stylesheet" href="'.$css.'">');

        require_once THEME.'IgnitionPacks/'.$ignition_pack.'/theme.php';
    }

    public static function getFooterPanel($col) {
        $settings = get_theme_settings('Atom9');

        if (!empty($settings[$col])) {
            $panel = str_replace('.php', '', $settings[$col]);
            $col = new \ReflectionClass('Atom9Theme\\Footer\\'.$panel);
            return $col->newInstance()->panel();
        }

        return NULL;
    }

    public static function footerPanels() {
        $settings = fusion_get_settings();
        $theme_settings = get_theme_settings('Atom9');
        $exclude_list = '';

        if (!empty($theme_settings['panel_exlude'])) {
            $exclude_list = explode("\r\n", $theme_settings['panel_exlude']);
        }

        if (is_array($exclude_list)) {
            if ($settings['site_seo']) {
                $params = http_build_query(\PHPFusion\Rewrite\Router::getRouterInstance()->get_FileParams());
                $file_path = '/'.\PHPFusion\Rewrite\Router::getRouterInstance()->getFilePath().($params ? '?' : '').$params;
                $script_url = explode('/', $file_path);
            } else {
                $script_url = explode('/', $_SERVER['PHP_SELF']);
            }

            $url_count = count($script_url);
            $base_url_count = substr_count(BASEDIR, '../') + ($settings['site_seo'] ? ($url_count - 1) : 1);

            $match_url = '';
            while ($base_url_count != 0) {
                $current = $url_count - $base_url_count;
                $match_url .= '/'.$script_url[$current];
                $base_url_count--;
            }

            return !in_array($match_url, $exclude_list);
        } else {
            return TRUE;
        }
    }

    /**
     * Theme Copyright
     * Do not delete or change this code!
     *
     * @return string
     */
    public static function themeCopyright() {
        return '&copy; '.date('Y').' Theme created by <a href="https://phpfusion.com" target="_blank">Frederick MC Chan</a> & <a href="https://github.com/RobiNN1" target="_blank">RobiNN</a>';
    }
}
