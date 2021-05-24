<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: LatestBlogs.php
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

class LatestBlogs extends Core {
    public static function panel() {
        ob_start();

        echo '<h3 class="title">'.self::setLocale('lb_01').'</h3>';

        $blogs = function_exists('infusion_exists') ? infusion_exists('blog') : db_exists(DB_PREFIX.'blog');

        if ($blogs) {
            $result = dbquery("SELECT b.*, COUNT(c.comment_item_id) AS blog_comments
                FROM ".DB_BLOG." AS b
                LEFT JOIN ".DB_COMMENTS." c ON comment_item_id = b.blog_id AND c.comment_type = 'B'
                ".(multilang_table('BL') ? "WHERE b.blog_language = '".LANGUAGE."' AND" : "WHERE")." ".groupaccess('b.blog_visibility')." AND (b.blog_start = 0 || b.blog_start <= ".time().")
                AND (b.blog_end = 0 || b.blog_end >= ".time().") AND b.blog_draft = 0
                GROUP BY b.blog_id
                ORDER BY b.blog_datestamp DESC
                LIMIT 5
            ");

            if (dbrows($result) > 0) {
                echo '<ul>';
                while ($data = dbarray($result)) {
                    echo '<li>';
                        echo '<a href="'.INFUSIONS.'blog/blog.php?readmore='.$data['blog_id'].'">'.$data['blog_subject'].'</a>';
                        echo '<br/>';
                        echo '<small>';
                            echo '<span><i class="fa fa-calendar"></i> '.showdate('shortdate', $data['blog_datestamp']).'</span>';

                            if (!empty($data['blog_allow_comments'])) {
                                $icon = $data['blog_comments'] > 1 ? 's' : '';
                                echo ' &middot; <span><i class="fa fa-comment'.$icon.'"></i> '.$data['blog_comments'].'</span>';
                            }

                            echo ' &middot; <span><i class="fa fa-eye"></i> '.number_format($data['blog_reads']).'</span>';
                        echo '</small>';
                echo '</li>';
                }
                echo '</ul>';
            } else {
                echo self::setLocale('lb_02');
            }
        } else {
            echo self::setLocale('lb_03');
        }

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}
