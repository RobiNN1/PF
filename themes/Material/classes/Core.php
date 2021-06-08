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
namespace MaterialTheme;

class Core {
    protected static $instance = NULL;
    private static $options = [
        'body_class'           => '',
        'container'            => TRUE,
        'container_class'      => '',
        'content_container'    => FALSE,
        'main_row_class'       => 'm-t-20 m-b-20',
        'right'                => TRUE,
        'right_card'           => FALSE,
        'right_card_class'     => 'p-b-5',
        'right_content'        => TRUE,
        'right_pre_content'    => '',
        'right_middle_content' => '',
        'right_post_content'   => '',
        'left_panel'           => TRUE,
        'left_pre_content'     => '',
        'left_post_content'    => '',
        'menu'                 => TRUE,
        'notices'              => TRUE,
        'header'               => FALSE,
        'header_id'            => 'default',
        'header_content'       => '',
        'header_styles'        => '',
        'small_header'         => FALSE,
        'small_header_id'      => 'default',
        'small_header_content' => '',
        'footer'               => TRUE
    ];

    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public static function getParam($name = NULL) {
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

        return !empty($locale[$key]) ? $locale[$key] : $locale;
    }

    public static function setTplCss($css) {
        $tpl_css = file_exists(THEME.'classes/Templates/css/'.$css.'.min.css') ? THEME.'classes/Templates/css/'.$css.'.min.css' : THEME.'classes/Templates/css/'.$css.'.css';
        add_to_head('<link rel="stylesheet" href="'.$tpl_css.'?v='.filemtime($tpl_css).'">');
    }

    public static function getFooterPanel($col = '') {
        $settings = get_theme_settings('Material');

        if (!empty($settings[$col])) {
            $panel = str_replace('.php', '', $settings[$col]);
            $col = new \ReflectionClass('MaterialTheme\\Footer\\'.$panel);
            return $col->newInstance()->panel();
        }

        return NULL;
    }

    public static function excludeFooterPanels() {
        $theme_settings = get_theme_settings('Material');
        $exclude_list = '';

        if (!empty($theme_settings['footer_exlude'])) {
            $exclude_list = explode("\r\n", $theme_settings['footer_exlude']);
        }

        if (is_array($exclude_list)) {
            if (fusion_get_settings('site_seo')) {
                $params = http_build_query(\PHPFusion\Rewrite\Router::getRouterInstance()->get_FileParams());
                $file_path = '/'.\PHPFusion\Rewrite\Router::getRouterInstance()->getFilePath().($params ? '?' : '').$params;
                $script_url = explode('/', $file_path);
            } else {
                $script_url = explode('/', $_SERVER['PHP_SELF']);
            }

            $url_count = count($script_url);
            $base_url_count = substr_count(BASEDIR, '../') + (fusion_get_settings('site_seo') ? ($url_count - 1) : 1);

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
        return '&copy; '.date('Y').' Theme created by <a href="https://github.com/RobiNN1" target="_blank">RobiNN</a>';
    }
}
