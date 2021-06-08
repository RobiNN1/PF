<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: theme.php
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

/**
 * Required Theme Components
 */
function render_page() {
    new CzechiaTheme\Main();
}

function opentable($title = FALSE, $class = '') {
    echo '<div class="opentable">';
    echo $title ? '<div class="title">'.$title.'</div>' : '';
    echo '<div class="'.$class.'">';
}

function closetable() {
    echo '</div>';
    echo '</div>';
}

function openside($title = FALSE, $class = '') {
    echo '<div class="openside '.$class.'">';
    echo $title ? '<div class="title">'.$title.'</div>' : '';
}

function closeside() {
    echo '</div>';
}

/**
 * Downloads
 * @param $info
 */
function render_downloads($info) {
    CzechiaTheme\Templates\Downloads::renderDownloads($info);
}

/**
 * Error Page
 * @param $info
 */
function display_error_page($info) {
    CzechiaTheme\Templates\Error::displayErrorPage($info);
}

/**
 * Login
 * @param $info
 */
function display_loginform($info) {
    CzechiaTheme\Templates\Auth::loginForm($info);
}

function display_register_form($info) {
    CzechiaTheme\Templates\Auth::registerForm($info);
}

/**
 * News
 * @param $info
 */
function display_main_news($info) {
    CzechiaTheme\Templates\News::displayMainNews($info);
}

function render_news_item($info) {
    CzechiaTheme\Templates\News::renderNewsItem($info);
}

set_image('imagenotfound', fusion_get_settings('siteurl').'themes/Czechia/images/noimage.svg');

add_handler(function ($output = '') {
    return preg_replace("/<meta name='theme-color' content='#ffffff'>/i", '<meta name="theme-color" content="#196496"/>', $output);
});
