<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: acp_theme.php
| Author: RobiNN
| Version: 2.0.0
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

if (!defined('ATOMCP_LOCALE')) {
    if (file_exists(THEMES.'admin_themes/AtomCP/locale/'.LANGUAGE.'.php')) {
        define('ATOMCP_LOCALE', THEMES.'admin_themes/AtomCP/locale/'.LANGUAGE.'.php');
    } else {
        define('ATOMCP_LOCALE', THEMES.'admin_themes/AtomCP/locale/English.php');
    }
}

const DB_ACPCHAT = DB_PREFIX.'acpchat';
const ATOMCP = THEMES.'admin_themes/AtomCP/';
require_once ATOMCP.'acp_autoloader.php';

const BOOTSTRAP = TRUE;
const FONTAWESOME = TRUE;
const PWTOGGLE = TRUE;
define('DARK_MODE', isset($_COOKIE['darkmode']) && $_COOKIE['darkmode'] == 1);

//$fixed_layout = (isset($_COOKIE['fixed-layout']) && $_COOKIE['fixed-layout'] == 1) ? ' fixed-layout' : '';
$fixed_layout = 'fixed-layout';
$dark = DARK_MODE ? ' darkmode' : '';
$toggled = (isset($_COOKIE['sidebar-toggled']) && $_COOKIE['sidebar-toggled'] == 1) ? ' sidebar-toggled' : '';

if (!defined('THEME_BODY')) {
    define('THEME_BODY', '<body class="'.$fixed_layout.$dark.$toggled.'">');
}

function render_admin_panel() {
    new AtomCP\AdminPanel();
}

function render_admin_login() {
    new AtomCP\Login();
}

function render_admin_dashboard() {
    new AtomCP\Dashboard();
}

function openside($title = FALSE, $class = NULL, $options = []) {
    $default_options = [
        'id'         => 0,
        'collapse'   => FALSE,
        'body'       => TRUE,
        'side_class' => TRUE
    ];

    $options += $default_options;

    echo '<div class="'.($options['side_class'] ? 'openside ' : '').'panel panel-default '.$class.'"'.($options['collapse'] ? ' data-panel-id="panel-'.$options['id'].'"' : '').'>';

    if ($options['collapse'] === TRUE) {
        $collapsed = isset($_COOKIE['collapse-panel-'.$options['id']]) == 1;

        echo '<div class="panel-heading">';
        echo !empty($title) ? '<span>'.$title.'</span>' : '';
        echo '<span data-toggle="collapse" data-target="#collapse-panel-'.$options['id'].'" aria-expanded="false" aria-controls="collapse-panel-'.$options['id'].'" class="fa fa-'.($collapsed ? 'plus' : 'minus').' pull-right panel-collapsed-indicator"></span>';
        echo '</div>';

        echo '<div class="panel-collapse collapse'.($collapsed ? '' : ' in').'" id="collapse-panel-'.$options['id'].'">';
    } else {
        echo $title ? '<div class="panel-heading">'.$title.'</div>' : '';
    }

    echo $options['body'] ? '<div class="panel-body">' : '';
}

function closeside($footer = FALSE, $collapse = FALSE, $body = TRUE) {
    echo $body ? '</div>' : ''; // .panel-body"

    echo !empty($footer) ? '<div class="panel-footer">'.$footer.'</div>' : '';

    if ($collapse === TRUE) {
        echo '</div>'; // .panel-collapse
    }

    echo '</div>'; // .openside
}

function opentable($title, $class = NULL) {
    AtomCP\AdminPanel::openTable($title, $class);
}

function closetable() {
    AtomCP\AdminPanel::closeTable();
}

$pages_data = PHPFusion\Admins::getInstance()->getAdminPages();

foreach ($pages_data as $section) {
    foreach ($section as $page) {
        $img = '';
        $path = ATOMCP.'icons/'.$page['admin_rights'];

        foreach (['.svg', '.png', '.gif', '.jpg'] as $ext) {
            if (file_exists($path.$ext)) {
                $img = fusion_get_settings('site_path').'themes/admin_themes/AtomCP/icons/'.$page['admin_rights'].$ext;
                break;
            }
        }

        set_image('ac_'.$page['admin_rights'], $img);
    }
}

function atomcp_settings() {
    return get_theme_settings('AtomCP');
}
