<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: LatestNews.php
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
namespace MaterialTheme\Footer;

use MaterialTheme\Core;

class LatestNews extends Core {
    public static function panel() {
        ob_start();

        echo '<h3 class="title">'.self::setLocale('ln_01').'</h3>';

        $news = function_exists('infusion_exists') ? infusion_exists('news') : db_exists(DB_PREFIX.'news');

        if ($news) {
            $result = dbquery("SELECT n.*, COUNT(c.comment_item_id) AS news_comments
                FROM ".DB_NEWS." n
                LEFT JOIN ".DB_COMMENTS." c ON comment_item_id = n.news_id AND c.comment_type = 'N'
                ".(multilang_table('NS') ? "WHERE n.news_language='".LANGUAGE."' AND" : "WHERE")." ".groupaccess('n.news_visibility')."
                AND (n.news_start = 0 || n.news_start <= ".time().") AND (n.news_end = 0 || n.news_end >= ".time().") AND n.news_draft = 0
                GROUP BY n.news_id
                ORDER BY n.news_datestamp DESC
                LIMIT 5
            ");

            if (dbrows($result) > 0) {
                echo '<ul class="break-words">';
                while ($data = dbarray($result)) {
                    echo '<li>';
                        echo '<a href="'.INFUSIONS.'news/news.php?readmore='.$data['news_id'].'"><strong>'.trim_text($data['news_subject'], 40).'</strong></a>';
                        echo '<br/>';
                        echo '<small>';
                            echo '<span><i class="fa fa-calendar"></i> '.showdate('shortdate', $data['news_datestamp']).'</span>';

                            if (!empty($data['news_allow_comments'])) {
                                $icon = $data['news_comments'] > 1 ? 's' : '';
                                echo ' &middot; <span><i class="fa fa-comment'.$icon.'"></i> '.$data['news_comments'].'</span>';
                            }

                            echo ' &middot; <span><i class="fa fa-eye"></i> '.number_format($data['news_reads']).'</span>';
                        echo '</small>';
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo self::setLocale('ln_02');
            }
        } else {
            echo self::setLocale('ln_03');
        }

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}
