<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: acp_theme.php
| Author: YOUR_NAME
| Version: 1.0.0LICENSE_TEXT
+--------------------------------------------------------*/
defined('IN_FUSION') || exit;

require_once INCLUDES.'theme_functions_include.php';
require_once 'acp_autoloader.php';

const BOOTSTRAP = TRUE;
const FONTAWESOME = TRUE;

function render_admin_panel() {
    new ADDON_NAME\AdminPanel();
}

function render_admin_login() {
    new ADDON_NAME\Login();
}

function render_admin_dashboard() {
    new ADDON_NAME\Dashboard();
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
