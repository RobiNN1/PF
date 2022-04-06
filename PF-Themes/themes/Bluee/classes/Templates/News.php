<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: News.php
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
namespace Bluee\Templates;

use Bluee\Main;

class News {
    public static function displayMainNews($info) {
        Main::hideAll(0);

        $news_settings = get_settings('news');

        if (!empty($info['news_items'])) {
            foreach ($info['news_items'] as $id => $data) {
                $info['news_items'][$id]['link'] = INFUSIONS.'news/news.php?readmore='.$data['news_id'];
                $thumb = !empty($data['news_image_optimized']) ? $data['news_image_optimized'] : get_image('imagenotfound');
                $info['news_items'][$id]['thumb'] = $thumb;
                $info['news_items'][$id]['news_news'] = strip_tags($info['news_items'][$id]['news_news']);
            }

            if ($info['news_total_rows'] > $news_settings['news_pagination']) {
                $type_start = isset($_GET['type']) ? 'type='.$_GET['type'].'&' : '';
                $cat_start = isset($_GET['cat_id']) ? 'cat_id='.$_GET['cat_id'].'&' : '';
                $info['pagenav'] = makepagenav($_GET['rowstart'], $news_settings['news_pagination'], $info['news_total_rows'], 3, INFUSIONS.'news/news.php?'.$cat_start.$type_start);
            }
        }

        $context = [
            'locale' => fusion_get_locale(),
            'info'   => $info
        ];

        echo fusion_render(THEME.'twig/news', 'index.twig', $context);
    }

    public static function renderNewsItem($info) {
        Main::hideAll(0);

        if (!empty($info['news_item']['news_image_src']) && file_exists($info['news_item']['news_image_src'])) {
            $info['news_item']['image'] = $info['news_item']['news_image_src'];
        }

        $info['news_item']['profile'] = profile_link($info['news_item']['user_id'], $info['news_item']['user_name'], $info['news_item']['user_status']);
        $info['news_item']['date'] = showdate('newsdate', $info['news_item']['news_datestamp']);
        $info['news_item']['reads'] = number_format($info['news_item']['news_reads']);

        if ($info['news_item']['news_allow_comments'] && fusion_get_settings('comments_enabled') == 1) {
            $info['news_item']['comments'] = $info['news_item']['news_display_comments'];
        }

        $context = [
            'locale' => fusion_get_locale(),
            'data'   => $info['news_item']
        ];

        echo fusion_render(THEME.'twig/news', 'item.twig', $context);
    }
}
