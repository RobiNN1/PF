<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: LatestComments.php
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

class LatestComments extends Core {
    public static function panel() {
        $locale = fusion_get_locale();

        ob_start();

        echo '<h3 class="title">'.$locale['global_025'].'</h3>';

        $display_comments = 6;
        $comments_per_page = fusion_get_settings('comments_per_page');

        $result = dbquery("SELECT c.*, u.user_id, u.user_name, u.user_status, u.user_avatar
            FROM ".DB_COMMENTS." c
            LEFT JOIN ".DB_USERS." u ON u.user_id=c.comment_name
            ORDER BY c.comment_datestamp DESC
            LIMIT ".$display_comments."
        ");

        if (dbrows($result)) {
            $info = [];

            while ($data = dbarray($result)) {
                switch ($data['comment_type']) {
                    case 'A':
                        $result_a = dbquery("SELECT a.article_subject
                            FROM ".DB_ARTICLES." AS a
                            INNER JOIN ".DB_ARTICLE_CATS." AS ac ON ac.article_cat_id=a.article_cat
                            WHERE a.article_id=:id AND a.article_draft=0
                            AND ".groupaccess('a.article_visibility')."
                            ".(multilang_table('AR') ? " AND a.article_language='".LANGUAGE."'" : '')."
                            ORDER BY a.article_datestamp DESC
                        ", [':id' => $data['comment_item_id']]);

                        if (dbrows($result_a)) {
                            $article_data = dbarray($result_a);
                            $comment_start = dbcount('(comment_id)', DB_COMMENTS, "comment_item_id='".$data['comment_item_id']."' AND comment_type='A' AND comment_id<=".$data['comment_id']);
                            $comment_start = $comment_start > $comments_per_page ? '&c_start_news_comments='.((floor($comment_start / $comments_per_page) * $comments_per_page) - $comments_per_page) : '';

                            $info[] = [
                                'data'  => $data,
                                'url'   => INFUSIONS.'articles/articles.php?article_id='.$data['comment_item_id'],
                                'title' => $article_data['article_subject'],
                                'c_url' => INFUSIONS.'articles/articles.php?article_id='.$data['comment_item_id'].$comment_start.'#c'.$data['comment_id']
                            ];
                        }
                        continue 2;
                    case 'B':
                        $result_b = dbquery("SELECT b.blog_subject
                            FROM ".DB_BLOG." AS b
                            INNER JOIN ".DB_BLOG_CATS." AS bc ON bc.blog_cat_id=b.blog_cat
                            WHERE b.blog_id=:id AND ".groupaccess('b.blog_visibility')."
                            ".(multilang_table('BL') ? " AND b.blog_language='".LANGUAGE."'" : '')."
                            ORDER BY b.blog_datestamp DESC
                        ", [':id' => $data['comment_item_id']]);

                        if (dbrows($result_b)) {
                            $blog_data = dbarray($result_b);
                            $comment_start = dbcount('(comment_id)', DB_COMMENTS, "comment_item_id='".$data['comment_item_id']."' AND comment_type='B' AND comment_id<=".$data['comment_id']);
                            $comment_start = $comment_start > $comments_per_page ? '&c_start_news_comments='.((floor($comment_start / $comments_per_page) * $comments_per_page) - $comments_per_page) : '';

                            $info[] = [
                                'data'  => $data,
                                'url'   => INFUSIONS.'blog/blog.php?readmore='.$data['comment_item_id'],
                                'title' => $blog_data['blog_subject'],
                                'c_url' => INFUSIONS.'blog/blog.php?readmore='.$data['comment_item_id'].$comment_start.'#c'.$data['comment_id']
                            ];
                        }
                        continue 2;
                    case 'N':
                        $result_n = dbquery("SELECT n.news_subject
                            FROM ".DB_NEWS." AS n
                            LEFT JOIN ".DB_NEWS_CATS." AS nc ON nc.news_cat_id=n.news_cat
                            WHERE n.news_id=:id AND (n.news_start=0 OR n.news_start<='".TIME."')
                            AND (n.news_end=0 OR n.news_end>='".TIME."') AND n.news_draft=0
                            AND ".groupaccess('n.news_visibility')."
                            ".(multilang_table('NS') ? "AND n.news_language='".LANGUAGE."'" : '')."
                            ORDER BY n.news_datestamp DESC
                        ", [':id' => $data['comment_item_id']]);

                        if (dbrows($result_n)) {
                            $news_data = dbarray($result_n);
                            $comment_start = dbcount('(comment_id)', DB_COMMENTS, "comment_item_id='".$data['comment_item_id']."' AND comment_type='N' AND comment_id<=".$data['comment_id']);
                            $comment_start = $comment_start > $comments_per_page ? '&c_start_news_comments='.((floor($comment_start / $comments_per_page) * $comments_per_page) - $comments_per_page) : '';

                            $info[] = [
                                'data'  => $data,
                                'url'   => INFUSIONS.'news/news.php?readmore='.$data['comment_item_id'],
                                'title' => $news_data['news_subject'],
                                'c_url' => INFUSIONS.'news/news.php?readmore='.$data['comment_item_id'].$comment_start.'#c'.$data['comment_id']
                            ];
                        }
                        continue 2;
                    case 'P':
                        $result_p = dbquery("SELECT p.photo_title
                            FROM ".DB_PHOTOS." AS p
                            INNER JOIN ".DB_PHOTO_ALBUMS." AS a ON p.album_id=a.album_id
                            WHERE p.photo_id=:id AND ".groupaccess('a.album_access')."
                            ".(multilang_table('PG') ? " AND a.album_language='".LANGUAGE."'" : '')."
                            ORDER BY p.photo_datestamp DESC
                        ", [':id' => $data['comment_item_id']]);

                        if (dbrows($result_p)) {
                            $photo_data = dbarray($result_p);
                            $comment_start = dbcount('(comment_id)', DB_COMMENTS, "comment_item_id='".$data['comment_item_id']."' AND comment_type='P' AND comment_id<=".$data['comment_id']);
                            $comment_start = $comment_start > $comments_per_page ? '&c_start_news_comments='.((floor($comment_start / $comments_per_page) * $comments_per_page) - $comments_per_page) : '';

                            $info[] = [
                                'data'  => $data,
                                'url'   => INFUSIONS.'gallery/gallery.php?photo_id='.$data['comment_item_id'],
                                'title' => $photo_data['photo_title'],
                                'c_url' => INFUSIONS.'gallery/gallery.php?photo_id='.$data['comment_item_id'].$comment_start.'#c'.$data['comment_id']
                            ];
                        }
                        continue 2;
                    case 'D':
                        $result_d = dbquery("SELECT d.download_title
                            FROM ".DB_DOWNLOADS." AS d
                            INNER JOIN ".DB_DOWNLOAD_CATS." AS dc ON dc.download_cat_id=d.download_cat
                            WHERE d.download_id=:id AND ".groupaccess('d.download_visibility')."
                            ".(multilang_table('DL') ? " AND dc.download_cat_language='".LANGUAGE."'" : '')."
                            ORDER BY d.download_datestamp DESC
                        ", [':id' => $data['comment_item_id']]);

                        if (dbrows($result_d)) {
                            $download_data = dbarray($result_d);
                            $comment_start = dbcount('(comment_id)', DB_COMMENTS, "comment_item_id='".$data['comment_item_id']."' AND comment_type='D' AND comment_id<=".$data['comment_id']);
                            $comment_start = $comment_start > $comments_per_page ? '&c_start_news_comments='.((floor($comment_start / $comments_per_page) * $comments_per_page) - $comments_per_page) : '';

                            $info[] = [
                                'data'  => $data,
                                'url'   => INFUSIONS.'downloads/downloads.php?download_id='.$data['comment_item_id'],
                                'title' => $download_data['download_title'],
                                'c_url' => INFUSIONS.'downloads/downloads.php?download_id='.$data['comment_item_id'].$comment_start.'#c'.$data['comment_id']
                            ];
                        }
                        break;
                }
            }

            echo '<ul class="list-style-none break-words">';
            foreach ($info as $id => $data) {
                $link = !empty($data['data']['user_id']);

                echo '<li id="comment-'.$id.'" class="m-t-5">';
                    echo '<div class="pull-left">'.display_avatar($data['data'], '35px', '', $link, 'img-rounded m-r-10 m-t-5').'</div>';
                    echo '<div class="overflow-hide">';
                        echo '<strong><a href="'.$data['url'].'">'.trim_text($data['title'], 35).'</a></strong>';
                        echo '<div class="clearfix"><a href="'.$data['c_url'].'">'.trim_text(strip_tags(parse_textarea($data['data']['comment_message'], FALSE, TRUE)), 27).'</a></div>';
                    echo '</div>';
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo $locale['global_026'];
        }

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}
