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

function display_loginform($info) {
    Atom9Theme\IgnitionPacks\StarCity\Templates\Auth::loginForm($info);
}

function display_register_form($info) {
    Atom9Theme\IgnitionPacks\StarCity\Templates\Auth::registerForm($info);
}

function render_downloads($info) {
    Atom9Theme\IgnitionPacks\StarCity\Templates\Downloads::renderDownloads($info);
}

function display_home($info) {
    Atom9Theme\IgnitionPacks\StarCity\Templates\Home::homePanel($info);
}

function display_main_news($info) {
    Atom9Theme\IgnitionPacks\StarCity\Templates\News::displayMainNews($info);
}

function render_news_item($info) {
    Atom9Theme\IgnitionPacks\StarCity\Templates\News::renderNewsItem($info);
}

function display_inbox($info) {
    Atom9Theme\IgnitionPacks\StarCity\Templates\PrivateMessages::displayInbox($info);
}
