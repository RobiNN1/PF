<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: LatestArticles.php
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

class LatestArticles extends Core {
    public static function panel() {
        ob_start();

        echo '<h3 class="title">'.self::setLocale('la_01').'</h3>';

        $articles = function_exists('infusion_exists') ? infusion_exists('articles') : db_exists(DB_PREFIX.'articles');

        if ($articles) {
            $result = dbquery("SELECT a.*, COUNT(c.comment_item_id) AS article_comments
                FROM ".DB_ARTICLES." AS a
                INNER JOIN ".DB_ARTICLE_CATS." AS ac ON a.article_cat=ac.article_cat_id
                LEFT JOIN ".DB_COMMENTS." c ON comment_item_id = a.article_id AND c.comment_type = 'A'
                WHERE a.article_draft = 0 AND ac.article_cat_status = 1 AND ".groupaccess("a.article_visibility")." AND ".groupaccess("ac.article_cat_visibility")."
                ".(multilang_table('AR') ? "AND a.article_language = '".LANGUAGE."' AND ac.article_cat_language = '".LANGUAGE."'" : '')."
                GROUP BY a.article_id
                ORDER BY a.article_datestamp DESC
                LIMIT 5
            ");

            if (dbrows($result) > 0) {
                echo '<ul>';
                while ($data = dbarray($result)) {
                    echo '<li>';
                        echo '<a href="'.INFUSIONS.'articles/articles.php?article_id='.$data['article_id'].'">'.$data['article_subject'].'</a>';
                        echo '<br/>';
                        echo '<small>';
                            echo '<span><i class="fa fa-calendar"></i> '.showdate('shortdate', $data['article_datestamp']).'</span>';

                            if (!empty($data['article_allow_comments'])) {
                                $icon = $data['article_comments'] > 1 ? 's' : '';
                                echo ' &middot; <span><i class="fa fa-comment'.$icon.'"></i> '.$data['article_comments'].'</span>';
                            }

                            echo ' &middot; <span><i class="fa fa-eye"></i> '.number_format($data['article_reads']).'</span>';
                        echo '</small>';
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo self::setLocale('la_02');
            }
        } else {
            echo self::setLocale('la_03');
        }

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}
