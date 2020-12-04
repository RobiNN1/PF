<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: rss_viewer_panel.php
| Author: RobiNN
| Version: 1.0.1
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

libxml_set_streams_context(stream_context_create(['http' => ['user_agent' => 'php']]));
$dom = new \DOMDocument();

foreach ($sites as $site) {
    $rss = $dom->load($site['url']);
    $channel = $dom->getElementsByTagName('channel')->item(0);

    echo '<div class="col-xs-12 col-sm-6">';
    openside($site['name']);

    if (!empty($channel->getElementsByTagName('item'))) {
        foreach ($channel->getElementsByTagName('item') as $item) {
            $title = $item->getElementsByTagName('title')->item(0)->firstChild->data;
            $link = $item->getElementsByTagName('link')->item(0)->firstChild->data;
            echo '<a href="'.$link.'" target="_blank">'.$title.'</a>';
            echo '<hr class="m-0">';
        }
    }

    closeside();
    echo '</div>';
}

echo '</div>';
