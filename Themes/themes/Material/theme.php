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
define('DARK_MODE', isset($_COOKIE['dark-mode']) && $_COOKIE['dark-mode'] == 1);

if (DARK_MODE) {
    define('THEME_BODY', '<body class="dark">');
}

if (!defined('DB_MT_NETWORKS')) {
    define('DB_MT_NETWORKS', DB_PREFIX.'mt_networks');
}

/*
 * Required Theme Components
 */
function render_page() {
    new MaterialTheme\Main();
}

function opentable($title = FALSE, $class = 'card') {
    echo '<div class="opentable">';
    echo $title ? '<div class="title"><h1>'.$title.'</h1></div>' : '';
    echo '<div class="'.$class.'">';
}

function closetable() {
    echo '</div>';
    echo '</div>';
}

function openside($title, $class = '', $panel = TRUE) {
    if (defined('LEFT') && $panel == TRUE) {
        echo '<div class="panel panel-primary openside '.$class.'">';
        echo $title ? '<div class="panel-heading"><h4 class="panel-title">'.$title.'</h4></div>' : '';
        echo '<div class="panel-body">';
    } else {
        echo '<div class="openside '.$class.'">';
        echo $title ? '<div class="title"><h4>'.$title.'</h4></div>' : '';
    }
}

function closeside($panel = TRUE) {
    if (defined('LEFT') && $panel == TRUE) {
        echo '</div>';
        echo '</div>';
    } else {
        echo '</div>';
    }
}

/*
 * Custom Templates
 */

// Articles
function display_main_articles($info) {
    MaterialTheme\Templates\Articles::displayMainArticles($info);
}

function render_article_item($info) {
    MaterialTheme\Templates\Articles::renderArticleItem($info);
}

// Blog
function render_main_blog($info) {
    MaterialTheme\Templates\Blog::renderMainBlog($info);
}

// Downloads
function render_downloads($info) {
    MaterialTheme\Templates\Downloads::renderDownloads($info);
}

// Forum
function render_forum($info) {
    MaterialTheme\Templates\Forum\Main::renderForum($info);
}

function render_postify($info) {
    MaterialTheme\Templates\Forum\Main::renderPostify($info);
}

function render_thread($info) {
    MaterialTheme\Templates\Forum\ViewThread::renderThread($info);
}

function display_forum_tags($info) {
    MaterialTheme\Templates\Forum\Tags::displayForumTags($info);
}

function display_forum_postform($info) {
    MaterialTheme\Templates\Forum\NewThread::displayForumPostForm($info);
}

// Home Page
function display_home($info) {
    MaterialTheme\Templates\HomePage::displayHome($info);
}

// News
function display_main_news($info) {
    MaterialTheme\Templates\News::displayMainNews($info);
}

function render_news_item($info) {
    MaterialTheme\Templates\News::renderNewsItem($info);
}

// Profile
function display_user_profile($info) {
    MaterialTheme\Templates\Profile::displayProfile($info);
}
