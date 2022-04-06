<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: LatestNews.php
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
namespace Atom9Theme\Footer;

class LatestNews {
    public static function panel() {
        $locale = fusion_get_locale('', ATOM9_LOCALE);

        ob_start();

        echo '<h3>'.$locale['a9_006'].'</h3>';

        $news = function_exists('infusion_exists') ? infusion_exists('news') : db_exists(DB_PREFIX.'news');
        if ($news) {
            $result = dbquery("SELECT news_id, news_subject
                FROM ".DB_NEWS."
                ".(multilang_table("NS") ? "WHERE ".in_group('news_language', LANGUAGE)." AND" : "WHERE")." ".groupaccess('news_visibility')."
                AND (news_start='0'||news_start<=".time().") AND (news_end='0' || news_end>=".time().") AND news_draft='0'
                ORDER BY news_datestamp DESC
                LIMIT 5
            ");

            if (dbrows($result) > 0) {
                echo '<ul>';
                while ($data = dbarray($result)) {
                    echo '<li><a href="'.INFUSIONS.'news/news.php?readmore='.$data['news_id'].'">'.$data['news_subject'].'</a></li>';
                }
                echo '</ul>';
            } else {
                echo $locale['a9_007'];
            }
        } else {
            echo $locale['a9_008'];
        }

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}
