<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: acp_theme.php
| Author: RobiNN
| Version: 1.5.2
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

const MD = THEMES.'admin_themes/MDashboard/';
require_once INCLUDES.'theme_functions_include.php';
require_once MD.'acp_autoloader.php';

if (!defined('MD_LOCALE')) {
    if (file_exists(MD.'locale/'.LANGUAGE.'.php')) {
        define('MD_LOCALE', MD.'locale/'.LANGUAGE.'.php');
    } else {
        define('MD_LOCALE', MD.'locale/English.php');
    }
}

const BOOTSTRAP = TRUE;
const FONTAWESOME = TRUE;

define("IS_V9", (bool)version_compare(fusion_get_settings('version'), '8.0', (strpos(fusion_get_settings('version'), '9.') === 0 ? '>' : '<')));

if (isset($_COOKIE['sidebar-toggled']) && $_COOKIE['sidebar-toggled'] == 1) {
    define('THEME_BODY', '<body class="sidebar-toggled">');
}

function render_admin_panel() {
    new MDashboard\AdminPanel();
}

function render_admin_login() {
    new MDashboard\Login();
}

function render_admin_dashboard() {
    new MDashboard\Dashboard();
}

function openside($title = FALSE, $class = NULL) {
    $html = '<div class="sidepanel '.$class.'">';
    $html .= $title ? '<div class="sidepanel-header">'.$title.'</div>' : '';
    $html .= '<div class="sidepanel-body">';

    echo $html;
}

function closeside($title = FALSE) {
    $html = '</div>';
    $html .= $title ? '<div class="sidepanel-footer">'.$title.'</div>' : '';
    $html .= '</div>';

    echo $html;
}

function opentable($title, $class = NULL) {
    MDashboard\AdminPanel::openTable($title, $class);
}

function closetable() {
    MDashboard\AdminPanel::closeTable();
}

if (!IS_V9) {
    \PHPFusion\OutputHandler::addHandler(function ($output = '') {
        return strtr($output, [
            'class=\'textbox' => 'class=\'textbox form-control m-t-5 m-b-5',
            'class="textbox'  => 'class="textbox form-control m-t-5 m-b-5',
            'class=\'button'  => 'class=\'button btn btn-default',
            'class="button'   => 'class="button btn btn-default'
        ]);
    });
}
