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
namespace MaterialTheme\Templates;

use MaterialTheme\Core;
use MaterialTheme\Main;
use PHPFusion\News\NewsServer;
use PHPFusion\Panels;

class News extends Core {
    private static function header($info, $bg = NULL) {
        $locale = fusion_get_locale();
        self::setTplCss('news');
        Panels::getInstance(TRUE)->hide_panel('RIGHT');

        echo '<div class="card clearfix">';

            echo '<div class="pull-right">';
                if (!empty($info['news_last_updated'])) {
                    echo '<span class="m-r-10"><strong class="text-dark">'.$locale['news_0008'].':</strong> '.(is_array($info['news_last_updated']) ? showdate('newsdate', $info['news_last_updated'][1]) : $info['news_last_updated']).'</span>';
                }

                echo '<span class="m-r-10">';
                    echo "<strong class='text-dark'>".$locale['show'].":</strong> ";

                    $i = 0;
                    foreach ($info['news_filter'] as $link => $title) {
                        $filter_active = (!isset($_GET['type']) && $i == 0) || isset($_GET['type']) && stristr($link, $_GET['type']) ? ' text-dark' : '';
                        echo '<a href="'.$link.'" class="display-inline'.$filter_active.' m-r-10">'.$title.'</a>';
                        $i++;
                    }
                echo '</span>';
            echo '</div>';

            if (!empty($info['news_categories'])) {
                echo '<div class="dropdown pull-left">';
                    echo '<a id="categories" href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-submenu>';
                        echo '<span class="text-bigger">'.$locale['news_0009'].'</span> <span class="caret"></span>';
                    echo '</a>';

                    echo '<ul class="dropdown-menu m-t-15" style="width: 300px;" aria-labelledby="categories">';
                        $news_categories = dbquery_tree_full(DB_NEWS_CATS, 'news_cat_id', 'news_cat_parent', "WHERE news_cat_language='".LANGUAGE."'");

                        $current_parent = 0;

                        if (isset($_GET['cat_id'])) {
                            $news_index = dbquery_tree(DB_NEWS_CATS, 'news_cat_id', 'news_cat_parent', "WHERE news_cat_language='".LANGUAGE."'");
                            $current_parent = get_parent($news_index, $_GET['cat_id']);
                        }

                        $news_categories[0] = sort_tree($news_categories[0], 'news_cat_name');

                        foreach ($news_categories[0] as $cat_data) {
                            $active = isset($_GET['cat_id']) && isnum($_GET['cat_id']) && ($_GET['cat_id'] == $cat_data['news_cat_id'] || $current_parent == $cat_data['news_cat_id']);

                            if (isset($news_categories[$cat_data['news_cat_id']])) {
                                // Sub Cats
                                echo '<li class="dropdown-submenu submenu-cats '.($active == TRUE ? ' active' : '').'">';
                                    echo '<a id="ddncat'.$cat_data['news_cat_id'].'" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">'.$cat_data['news_cat_name'].'</a>';

                                    echo '<ul class="dropdown-menu" aria-labelledby="ddncat'.$cat_data['news_cat_id'].'" style="width: 250px;">';
                                        echo '<li'.($active == TRUE ? ' class="active"' : '').'><a href="'.INFUSIONS.'news/news.php?cat_id='.$cat_data['news_cat_id'].'">'.$cat_data['news_cat_name'].'</a></li>';

                                        foreach ($news_categories[$cat_data['news_cat_id']] as $sub_cat_data) {
                                            $sub_active = (isset($_GET['cat_id']) && isnum($_GET['cat_id']) && $_GET['cat_id'] == $sub_cat_data['news_cat_id']) ? ' class="active"' : '';
                                            echo '<li'.$sub_active.'><a href="'.INFUSIONS.'news/news.php?cat_id='.$sub_cat_data['news_cat_id'].'">'.$sub_cat_data['news_cat_name'].'</a></li>';
                                        }
                                    echo '</ul>';
                                echo '</li>';
                            } else {
                                // Category
                                echo '<li'.($active == TRUE ? ' class="active"' : '').'><a href="'.INFUSIONS.'news/news.php?cat_id='.$cat_data['news_cat_id'].'">'.$cat_data['news_cat_name'].'</a></li>';
                            }
                        }

                        echo '<li'.(isset($_GET['cat_id']) && isnum($_GET['cat_id']) && $_GET['cat_id'] == 0 ? ' class="active"' : '').'><a href="'.INFUSIONS.'news/news.php?cat_id=0">'.$locale['news_0006'].'</a></li>';
                    echo '</ul>';
                echo '</div>';
            }
        echo '</div>';

        Main::headerContent([
            'id'         => 'news',
            'title'      => $locale['news_0004'],
            'background' => $bg
        ]);
    }

    public static function displayMainNews($info) {
        $locale = fusion_get_locale();
        $news_settings = NewsServer::get_news_settings();

        self::header($info);

        if (!empty($info['news_items'])) {
            echo '<div class="card">';
                echo '<div class="row equal-height">';
                    foreach ($info['news_items'] as $data) {
                        $link = INFUSIONS.'news/news.php?readmore='.$data['news_id'];

                        echo '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 m-t-15 m-b-20">';
                            echo '<article class="item">';
                                echo '<div>';
                                echo '<figure class="thumb">';
                                    echo '<a href="'.$link.'">';
                                        $thumb = !empty($data['news_image_optimized']) ? $data['news_image_optimized'] : get_image('imagenotfound');
                                        echo '<img class="img-responsive" src="'.$thumb.'" alt="'.$data['news_subject'].'">';
                                    echo '</a>';
                                echo '</figure>';
                                echo '<div class="post clearfix">';
                                    echo '<h2 class="post-title"><a href="'.$link.'">'.$data['news_subject'].'</a></h2>';
                                    echo '<div class="meta">';
                                        echo '<span class="m-r-5"><i class="fa fa-user"></i> '.profile_link($data['user_id'], $data['user_name'], $data['user_status']).'</span>';
                                        echo '<span class="m-r-5"><i class="fa fa-clock"></i> '.showdate(fusion_get_settings('newsdate'), $data['news_date']).'</span>';
                                        echo '<span><i class="fa fa-folder"></i> <a href="'.INFUSIONS.'news/news.php?cat_id='.$data['news_cat_id'].'">'.$data['news_cat_name'].'</a></span>';
                                    echo '</div>';
                                echo '</div>';
                                echo '</div>';

                                echo '<a href="'.$link.'" class="readmore">'.self::setLocale('readmore').'</a>';
                            echo '</article>';
                        echo '</div>';
                    }
                echo '</div>';

                if ($info['news_total_rows'] > $news_settings['news_pagination']) {
                    $type_start = isset($_GET['type']) ? 'type='.$_GET['type'].'&' : '';
                    $cat_start = isset($_GET['cat_id']) ? 'cat_id='.$_GET['cat_id'].'&' : '';
                    echo '<div class="text-center m-t-10 m-b-10">';
                        echo makepagenav($_GET['rowstart'], $news_settings['news_pagination'], $info['news_total_rows'], 3, INFUSIONS.'news/news.php?'.$cat_start.$type_start);
                    echo '</div>';
                }
            echo '</div>';
        } else {
            echo '<div class="card text-center">'.$locale['news_0005'].'</div>';
        }
    }

    public static function renderNewsItem($info) {
        $locale = fusion_get_locale();
        $data = $info['news_item'];

        $bg = !empty($data['news_image_src']) ? $data['news_image_src'] : Main::getRandImg();
        self::header($info, $bg);

        echo '<div class="card">';
            echo '<div class="clearfix">';
                echo '<h1 class="display-inline-block m-t-5">'.$data['news_subject'].'</h1>';

                echo '<div class="pull-right" id="options">';
                    $action = $data['news_admin_actions'];
                    if (!empty($action)) {
                        echo '<a href="'.$data['print_link'].'" class="btn btn-primary btn-circle btn-xs m-r-10" title="'.$locale['print'].'" target="_blank"><i class="fa fa-print"></i></a>';
                        echo '<a href="'.$action['edit']['link'].'" class="btn btn-warning btn-circle btn-xs m-r-10" title="'.$locale['edit'].'"><i class="fa fa-pen"></i></a>';
                        echo '<a href="'.$action['delete']['link'].'" class="btn btn-danger btn-circle btn-xs" title="'.$locale['delete'].'"><i class="fa fa-trash"></i></a>';
                    } else {
                        echo '<a class="btn btn-primary btn-circle print" href="'.BASEDIR.'print.php?type=N&item_id='.$data['news_id'].'" title="'.$locale['print'].'" target="_blank"><i class="fa fa-print"></i></a>';
                    }
                echo '</div>';
            echo '</div>';

            echo '<div class="overflow-hide">';
                echo '<div>'.$data['news_news'].'</div>';
                echo '<br/>';
                echo $data['news_extended'];
            echo '</div>';

            if (!empty($data['news_gallery']) && count($data['news_gallery']) > 1) {
                echo '<hr />';
                echo '<h3>'.$locale['news_0019'].'</h3>';

                echo '<div class="overflow-hide m-b-20">';
                    foreach ($data['news_gallery'] as $id => $image) {
                        echo '<div class="pull-left overflow-hide" style="width: 250px; height: 120px;">';
                            echo colorbox(IMAGES_N.$image['news_image'], 'Image #'.$id, TRUE);
                        echo '</div>';
                    }

                echo '</div>';
            }

            echo $data['news_pagenav'] ? '<div class="clearfix m-b-20"><div class="pull-right">'.$data['news_pagenav'].'</div></div>' : '';

            echo '<div class="well text-center m-t-10 m-b-0">';
                echo '<span class="m-l-10"><i class="fa fa-user"></i> '.profile_link($data['user_id'], $data['user_name'], $data['user_status']).'</span>';
                echo '<span class="m-l-10"><i class="fa fa-calendar"></i> '.showdate('newsdate', $data['news_datestamp']).'</span>';
                echo '<span class="m-l-10"><i class="fa fa-eye"></i> '.number_format($data['news_reads']).'</span>';
                if ($data['news_allow_comments'] && fusion_get_settings('comments_enabled') == 1) {
                    $icon = $data['news_comments'] > 1 ? 's' : '';
                    echo '<span class="m-l-10"><i class="fa fa-comment'.$icon.'"></i> '.$data['news_display_comments'].'</span>';
                }

                if ($data['news_allow_ratings'] && fusion_get_settings('ratings_enabled') == 1) {
                    echo '<span class="m-l-10">'.$data['news_display_ratings'].'</span>';
                }
            echo '</div>';
        echo '</div>';

        echo $data['news_show_comments'] ? '<div class="card">'.$data['news_show_comments'].'</div>' : '';

        $ratings = $data['news_show_ratings'] ? '<div class="m-b-20 ratings-box">'.$data['news_show_ratings'].'</div>' : '';
        self::setParam('right_middle_content', $ratings);
        self::setParam('right_card', TRUE);

        self::relatedNewsPanel();
    }

    private static function relatedNewsPanel() {
        if ((FUSION_SELF == 'news.php') && isset($_GET['readmore']) && isnum($_GET['readmore'])) {
            $query = dbquery("SELECT news_subject from ".DB_NEWS." WHERE news_id=:news_id", [':news_id' => $_GET['readmore']]);

            list($news_subject) = dbarraynum($query);

            $result = dbquery("SELECT news_id, news_subject
                FROM ".DB_NEWS."
                WHERE MATCH (news_subject) AGAINST ('".$news_subject."' IN BOOLEAN MODE) AND news_id != :news_id
                ORDER BY news_datestamp DESC LIMIT 5
            ", [':news_id' => $_GET['readmore']]);

            if (dbrows($result) > 0) {
                ob_start();

                openside(self::setLocale('ns_01'), '', FALSE);

                echo '<ul class="list-style-none">';
                    while ($data = dbarray($result)) {
                        echo '<li><i class="fa fa-newspaper-o"></i> <a href="'.INFUSIONS.'news/news.php?readmore='.$data['news_id'].'">'.$data['news_subject'].'</a></li>';
                    }
                echo '</ul>';

                closeside(FALSE);

                $html = ob_get_contents();
                ob_end_clean();

                self::setParam('right_post_content', $html);
            }
        }
    }
}
