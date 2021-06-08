<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: theme.php
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
defined('IN_FUSION') || exit;

require_once INCLUDES.'theme_functions_include.php';
require_once 'theme_autoloader.php';

const THEME_BULLET = '&middot;';
const BOOTSTRAP = TRUE;
const FONTAWESOME = TRUE;

if (!defined('ATOM9_LOCALE')) {
    if (file_exists(THEME.'locale/'.LANGUAGE.'.php')) {
        define('ATOM9_LOCALE', THEME.'locale/'.LANGUAGE.'.php');
    } else {
        define('ATOM9_LOCALE', THEME.'locale/English.php');
    }
}

function render_page() {
    Atom9Theme\Main::getInstance()->renderPage();
}

function opentable($title = '', $class = '') {
    echo '<div class="mainpanel '.$class.'">';
    echo $title ? '<div class="heading">'.$title.'</div>' : '';
    echo '<div class="body">';
}

function closetable() {
    echo '</div>';
    echo '</div>';
}

function openside($title = '') {
    echo '<div class="sidepanel">';
    echo $title ? '<div class="heading">'.$title.'</div>' : '';
    echo '<div class="body">';
}

function closeside() {
    echo '</div>';
    echo '</div>';
}

$theme_settings = get_theme_settings('Atom9');
$ignition_pack = !empty($theme_settings['ignition_pack']) ? $theme_settings['ignition_pack'] : 'StarCity';
define('IGNITION_PACK', THEME.'IgnitionPacks/'.$ignition_pack.'/');

Atom9Theme\Core::getInstance()->getIgnitionPacks($ignition_pack);
