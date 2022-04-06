<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Articles.php
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
namespace MaterialTheme\Templates;

use MaterialTheme\Core;
use MaterialTheme\Main;
use PHPFusion\Articles\ArticlesServer;
use PHPFusion\Panels;

class Articles extends Core {
    private static $show_num_of_items = FALSE; // Set TRUE to show number of items in categories

    private static function header($info, $sub_title = '') {
        $locale = fusion_get_locale();

        Panels::getInstance(TRUE)->hide_panel('RIGHT');

        Main::headerContent([
            'id'         => 'articles',
            'title'      => $locale['article_0000'],
            'sub_title'  => $sub_title,
            'background' => TRUE
        ]);

        self::menu($info);
    }

    public static function displayMainArticles($info) {
        $articles_settings = ArticlesServer::get_article_settings();
        $locale = fusion_get_locale();

        self::header($info);

        if (!empty($info['article_items'])) {
            foreach ($info['article_items'] as $data) {
                echo '<div class="card article-item">';
                    echo '<div class="overflow-hide">';
                        echo '<span class="pull-right"><a class="label label-primary p-5" href="'.INFUSIONS.'articles/articles.php?cat_id='.$data['article_cat_id'].'">'.$data['article_cat_name'].'</a></span>';
                        echo '<h3 class="m-t-0"><a href="'.INFUSIONS.'articles/articles.php?article_id='.$data['article_id'].'">'.$data['article_subject'].'</a></h3>';
                    echo '</div>';

                    echo $data['article_snippet'];

                    echo '<br/><a class="display-inline" href="'.INFUSIONS.'articles/articles.php?article_id='.$data['article_id'].'">'.self::setLocale('readmore').'</a>';
                    echo '<br/>';
                    echo '<i class="fa fa-eye"></i> '.format_word($data['article_reads'], $locale['fmt_read']);

                    if ($data['article_allow_comments'] && fusion_get_settings('comments_enabled') == 1) {
                        $icon = ($data['article_comments'] < 1 ? 'ing' : $data['article_comments'] > 1) ? 's' : '';
                        echo '<i class="fa fa-comment'.$icon.' m-l-10"></i> ';
                        echo '<a href="'.INFUSIONS.'articles/articles.php?article_id='.$data['article_id'].'#comments">';
                            echo $data['article_comments'] < 1 ? self::setLocale('leave_comment') : format_word($data['article_comments'], $locale['fmt_comment']);
                        echo '</a>';
                    }

                    if ($data['article_allow_ratings'] && fusion_get_settings('ratings_enabled') == 1) {
                        echo '<i class="fa fa-star m-l-10"></i> ';
                        echo '<a href="'.INFUSIONS.'articles/articles.php?article_id='.$data['article_id'].'#comments">';
                            echo format_word($data['article_count_votes'], $locale['fmt_rating']);
                        echo '</a>';
                    }

                    echo '<i class="fa fa-print m-l-10"></i> ';
                    echo '<a href="'.$data['print_link'].'" title="'.$locale['print'].'" target="_blank">'.$locale['print'].'</a>';
                echo '</div>';
            }

            if ($info['article_total_rows'] > $articles_settings['article_pagination']) {
                $type_start = isset($_GET['type']) ? 'type='.$_GET['type'].'&' : '';
                $cat_start = isset($_GET['cat_id']) ? 'cat_id='.$_GET['cat_id'].'&' : '';

                echo '<div class="text-center m-t-10 m-b-10">';
                    echo makepagenav($_GET['rowstart'], $articles_settings['article_pagination'], $info['article_total_rows'], 3, INFUSIONS.'articles/articles.php?'.$cat_start.$type_start);
                echo '</div>';
            }
        } else {
            echo '<div class="card text-center">'.(isset($_GET['cat_id']) ? $locale['article_0062'] : $locale['article_0061']).'</div>';
        }
    }

    public static function renderArticleItem($info) {
        $locale = fusion_get_locale();
        $data = $info['article_item'];

        self::header($info, $data['article_subject']);

        echo '<article class="card">';
            echo '<i class="fa fa-user"></i> '.profile_link($data['user_id'], $data['user_name'], $data['user_status']);
            echo '<i class="fa fa-calendar m-l-10"></i> '.showdate("newsdate", $data['article_datestamp']);
            echo '<i class="fa fa-eye m-l-10"></i> '.format_word($data['article_reads'], $locale['fmt_read']);

            if ($data['article_allow_comments'] && fusion_get_settings('comments_enabled') == 1) {
                echo '<i class="fa fa-comment'.($data['article_comments'] > 1 ? 's' : '').' m-l-10"></i> <a href="#comments">'.format_word($data['article_comments'], $locale['fmt_comment']).'</a>';
            }

            if ($data['article_allow_ratings'] && fusion_get_settings('ratings_enabled') == 1) {
                echo '<i class="fa fa-chart-pie m-l-10"></i> '.format_word($data['article_count_votes'], $locale['fmt_rating']);
            }

            echo '<div class="pull-right" id="options">';
                $action = $data['admin_actions'];

                echo '<a href="'.$data['print_link'].'" class="btn btn-primary btn-circle btn-xs m-r-10" title="'.$locale['print'].'" target="_blank"><i class="fa fa-print"></i></a>';

                if (!empty($action)) {
                    echo '<a href="'.$action['edit']['link'].'" class="btn btn-warning btn-circle btn-xs m-r-10" title="'.$locale['edit'].'"><i class="fa fa-pen"></i></a>';
                    echo '<a href="'.$action['delete']['link'].'" class="btn btn-danger btn-circle btn-xs" title="'.$locale['delete'].'"><i class="fa fa-trash"></i></a>';
                }
            echo '</div>';

            echo '<hr/>';

            echo '<div class="content-text">'.$data['article_snippet'].'<br>'.$data['article_article'].'</div>';

            if ($data['article_pagenav']) {
                echo '<div class="clearfix m-b-20"><div class="pull-right">'.$data['article_pagenav'].'</div></div>';
            }

            echo '<hr/>';
            echo '<div class="clearfix m-b-10">';
                echo '<h4>'.$locale['about'].' '.profile_link($data['user_id'], $data['user_name'], $data['user_status']).'</h4>';
                echo '<div class="pull-left m-r-10">'.display_avatar($data, '80px', '', TRUE, 'img-rounded').'</div>';

                if (!empty(fusion_get_user($data['user_id'], 'user_sig'))) {
                    echo parse_textarea(fusion_get_user($data['user_id'], 'user_sig'));
                } else {
                    echo '<strong>'.getuserlevel($data['user_level']).'</strong><br/>';
                    echo '<strong>'.$locale['joined'].showdate('newsdate', $data['user_joined']).'</strong>';
                }
            echo '</div>';
        echo '</article>';

        echo $data['article_show_comments'] ? '<div class="card">'.$data['article_show_comments'].'</div>' : '';
        $ratings = $data['article_show_ratings'] ? '<div class="m-b-20 ratings-box">'.$data['article_show_ratings'].'</div>' : '';
        self::setParam('right_middle_content', $ratings);
    }

    private static function menu($info) {
        $locale = fusion_get_locale();

        self::setParam('right_card', TRUE);
        self::setParam('right_card_class', 'p-b-0');

        ob_start();

        $right_top = '';

        if (!empty($info['article_last_updated'])) {
            $right_top .= '<small class="strong">'.$locale['article_0004'].'</small>: ';
            $right_top .= '<small>'.($info['article_last_updated'] > 0 ? showdate('newsdate', $info['article_last_updated']) : $locale['na']).'</small>';
        }

        $right_top .= '<div class="text-bigger"><h4>'.$locale['show'].'</h4></div>';
        $right_top .= '<select onchange="location = this.value;" class="form-control m-b-20">';

        $i = 0;

        foreach ($info['article_filter'] as $link => $title) {
            $active = '';
            if ((!isset($_GET['type']) && $i == 0) || isset($_GET['type']) && stristr($link, $_GET['type'])) {
                $active = 'selected';
            }

            $right_top .= '<option value="'.$link.'" '.$active.'>'.$title.'</option>';
            $i++;
        }
        $right_top .= '</select>';

        self::setParam('right_pre_content', $right_top);

        openside($locale['article_0003'], '', FALSE);

            if (is_array($info['article_categories']) && !empty($info['article_categories'])) {
                if (self::$show_num_of_items == TRUE) {
                    $result = dbquery("SELECT ac.*, COUNT(a.article_id) AS article_count
                        FROM ".DB_ARTICLE_CATS." ac
                        LEFT JOIN ".DB_ARTICLES." AS a ON a.article_cat=ac.article_cat_id
                        WHERE ac.article_cat_status=1 AND ".groupaccess('article_cat_visibility')."
                        ".(multilang_table('AR') ? " AND ac.article_cat_language='".LANGUAGE."'" : '')."
                        GROUP BY ac.article_cat_id
                        ORDER BY ac.article_cat_id ASC
                    ");

                    if (dbrows($result) > 0) {
                        while ($data = dbarray($result)) {
                            $info['article_categories'][$data['article_cat_id']] = [
                                'link'  => INFUSIONS.'articles/articles.php?cat_id='.$data['article_cat_id'],
                                'name'  => $data['article_cat_name'],
                                'count' => $data['article_count']
                            ];
                        }
                    }
                }

                echo '<div class="list-group">';
                    foreach ($info['article_categories'] as $cat_id => $cat_data) {
                        $active = !empty($_GET['cat_id']) && $_GET['cat_id'] == $cat_id;
                        $active = ($active ? ' active' : '');
                        echo '<a class="list-group-item p-5 p-l-10'.$active.'" href="'.$cat_data['link'].'">';
                            echo $cat_data['name'];
                            if (self::$show_num_of_items == TRUE) {
                                echo '<span class="badge">'.$cat_data['count'].'</span>';
                            }
                        echo '</a>';
                    }
                echo '</div>';
            } else {
                echo '<p>'.$locale['article_0060'].'</p>';
            }

        closeside(FALSE);

        $right = ob_get_contents();
        ob_end_clean();

        self::setParam('right_post_content', $right);
    }
}
