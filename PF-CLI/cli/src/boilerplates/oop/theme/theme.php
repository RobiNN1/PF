<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: theme.php
| Author: YOUR_NAMELICENSE_TEXT
+--------------------------------------------------------*/
defined('IN_FUSION') || exit;

require_once 'theme_autoloader.php';

const BOOTSTRAP = TRUE;
const FONTAWESOME = TRUE;

// Required Theme Components
function render_page() {
    new ADDON_NAME\Main();
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

// News
function display_main_news($info) {
    ADDON_NAME\Templates\News::displayMainNews($info);
}

function render_news_item($info) {
    ADDON_NAME\Templates\News::renderNewsItem($info);
}
