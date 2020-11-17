<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: acp_theme.php
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

if (!defined('ALTE_LOCALE')) {
    if (file_exists(THEMES.'admin_themes/AdminLTE3/locale/'.LANGUAGE.'.php')) {
        define('ALTE_LOCALE', THEMES.'admin_themes/AdminLTE3/locale/'.LANGUAGE.'.php');
    } else {
        define('ALTE_LOCALE', THEMES.'admin_themes/AdminLTE3/locale/English.php');
    }
}

define('ADMINLTE3', THEMES.'admin_themes/AdminLTE3/');
require_once ADMINLTE3.'acp_autoloader.php';

define('BOOTSTRAP4', TRUE);
define('FONTAWESOME', TRUE);

if (!check_admin_pass('')) {
    define('THEME_BODY', '<body class="hold-transition lockscreen">');
} else {
    define('THEME_BODY', '<body class="hold-transition sidebar-mini">');
}

function render_admin_panel() {
    new AdminLTE3\AdminPanel();
}

function render_admin_login() {
    new AdminLTE3\Login();
}

function render_admin_dashboard() {
    new AdminLTE3\Dashboard();
}

function openside($title = FALSE, $class = NULL) {
    $html = '<div class="card '.$class.'">';
    $html .= $title ? '<div class="card-header">'.$title.'</div>' : '';
    $html .= '<div class="card-body">';

    echo $html;
}

function closeside($footer = FALSE) {
    $html = '</div>';
    $html .= $footer ? '<div class="card-footer">'.$footer.'</div>' : '';
    $html .= '</div>';

    echo $html;
}

function opentable($title, $class = NULL, $bg = TRUE) {
    AdminLTE3\AdminPanel::openTable($title, $class, $bg);
}

function closetable($bg = TRUE) {
    AdminLTE3\AdminPanel::closeTable($bg);
}

\PHPFusion\OutputHandler::addHandler(function ($output = '') {
    return preg_replace_callback("/class=(['\"])[^('|\")]*/im", function ($m) {
        return strtr($m[0], [
            'btn-default'       => 'btn-secondary',
            'panel-group'       => 'panel-group',
            'panel'             => 'card',
            'panel-heading'     => 'card-header',
            'panel-title'       => 'card-title',
            'panel-body'        => 'card-body',
            'panel-footer'      => 'card-footer',
            'badge-defaul'      => 'badge-secondary',
            'input-group-addon' => 'input-group-append',
            'input-group-btn'   => 'input-group-append'
        ]);
    }, $output);
});
