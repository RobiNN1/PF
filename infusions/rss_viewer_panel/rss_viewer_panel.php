<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: rss_viewer_panel.php
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

$sites = [
    [
        'name' => 'Blog',
        'url'  => 'http://pf.host/infusions/rss_feeds_panel/feeds/rss_blog.php'
    ],
    [
        'name' => 'News',
        'url'  => 'http://pf.host/infusions/rss_feeds_panel/feeds/rss_news.php'
    ],
];

echo '<div class="row">';

require_once __DIR__.'/Feed.php';
Feed::$cacheDir = __DIR__.'/cache';

foreach ($sites as $site) {
    $rss = Feed::loadRss($site['url']);
    echo '<div class="col-xs-12 col-sm-6">';
    openside($site['name']);

    foreach ($rss->item as $item) {
        echo '<a href="'.htmlspecialchars($item->url).'" target="_blank">'.htmlspecialchars($item->title).'</a>';
        echo '<hr class="m-0">';
    }

    closeside();
    echo '</div>';
}

echo '</div>';
