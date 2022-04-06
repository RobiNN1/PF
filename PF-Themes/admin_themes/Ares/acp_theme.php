<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: acp_theme.php
| Author: RobiNN
| Version: 1.1.2
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

if (!defined('ARS_LOCALE')) {
    if (file_exists(THEMES.'admin_themes/Ares/locale/'.LANGUAGE.'.php')) {
        define('ARES_LOCALE', THEMES.'admin_themes/Ares/locale/'.LANGUAGE.'.php');
    } else {
        define('ARES_LOCALE', THEMES.'admin_themes/Ares/locale/English.php');
    }
}

const ARES = THEMES.'admin_themes/Ares/';
require_once INCLUDES.'theme_functions_include.php';
require_once ARES.'acp_autoloader.php';

const BOOTSTRAP = TRUE;
const ENTYPO = TRUE;
const FONTAWESOME = TRUE;

if (!check_admin_pass('')) {
    define('THEME_BODY', '<body class="login-page">');
}

function render_admin_panel() {
    new Ares\AdminPanel();
}

function render_admin_login() {
    new Ares\Login();
}

function render_admin_dashboard() {
    new Ares\Dashboard();
}

function openside($title = FALSE, $class = NULL) {
    $html = '<div class="panel panel-default openside '.$class.'">';
    $html .= $title ? '<div class="panel-heading">'.$title.'</div>' : '';
    $html .= '<div class="panel-body">';

    echo $html;
}

function closeside($footer = FALSE) {
    $html = '</div>';
    $html .= $footer ? '<div class="panel-footer">'.$footer.'</div>' : '';
    $html .= '</div>';

    echo $html;
}

function opentable($title, $class = NULL) {
    $html = '<div class="panel panel-default opentable '.$class.'">';
    $html .= $title ? '<div class="panel-heading"><h3>'.$title.'</h3></div>' : '';
    $html .= '<div class="panel-body">';

    echo $html;
}

function closetable() {
    $html = '</div>';
    $html .= '</div>';

    echo $html;
}
