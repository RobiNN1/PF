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
namespace CzechiaTheme\Templates;

use CzechiaTheme\Core;
use PHPFusion\News\NewsServer;
use PHPFusion\Panels;

class News extends Core {
    private static function header($info) {
        $locale = fusion_get_locale();

        add_to_css('
            figure {margin: 0;padding: 0;border: 0;font: inherit;vertical-align: baseline;display: block;}
            .item {padding: 0;height: inherit;}
            .item .thumb {float: left;height: 120px;overflow: hidden;margin-right: 10px;}
            .item .thumb img {width: 100%;max-width: 200px;-webkit-transform: scale(1.1);-ms-transform: scale(1.1);-o-transform: scale(1.1);transform: scale(1.1);margin-top: -10px;}
            .item .post .post-title {margin-bottom: .625rem;display: block;font-size: 18px;font-weight: 700;}
            .item .post .post-title a {color: #1a1a1a;}
            .item .post .post-title a:hover {color: #0b5fd2;}
            @media (min-width: 768px) {
                .item .thumb {float: inherit;height: 100px;margin-right: inherit;}
                .item .thumb img {max-width: 100%;max-height: 120px;-webkit-transform: scale(1.1);-ms-transform: scale(1.1);-o-transform: scale(1.1);transform: scale(1.1);margin-top: -10px;}
                .item .post .post-title {margin-bottom: 10px;padding-bottom: 9px;border-bottom: 1px solid rgb(241, 241, 241);}
                .item .post .meta {margin: 0;padding: 3px 0 10px;font-size: 12px;}
                .item .post .readmore {border: 1px solid transparent; text-transform: uppercase;font-weight: bold;color: #046ca0;padding: 5px 10px;font-size: 14px;-webkit-border-radius: 2px;border-radius: 2px;display: inline-block;width: 100%;text-align: center;}
                .item .post .readmore:hover {border-color: #046ca0;}
            }
        ');

        echo '<h1 class="text-center">'.$locale['news_0004'].'</h1>';

        echo render_breadcrumbs();

        echo '<div class="card clearfix">';
            if (!empty($info['news_last_updated'])) {
                echo '<span class="m-r-10"><strong class="text-dark">'.$locale['news_0008'].':</strong> '.(is_array($info['news_last_updated']) ? showdate('newsdate', $info['news_last_updated'][1]) : $info['news_last_updated']).'</span>';
            }

            echo '<span class="m-r-10">';
                echo '<strong class="text-dark">'.$locale['show'].':</strong> ';
                $i = 0;
                foreach ($info['news_filter'] as $link => $title) {
                    $filter_active = (!isset($_GET['type']) && $i == 0) || isset($_GET['type']) && stristr($link, $_GET['type']) ? ' text-dark' : '';
                    echo '<a href="'.$link.'" class="display-inline'.$filter_active.' m-r-10">'.$title.'</a>';
                    $i++;
                }
            echo '</span>';
        echo '</div>';

        /**
         * Check if array is multidimensional
         *
         * @param array $array
         *
         * @return bool
         */
        function is_multidimensiona_array($array) {
            if (!is_array($array)) {
                return FALSE;
            }
            foreach ($array as $elm) {
                if (!is_array($elm)) {
                    return FALSE;
                }
            }
            return TRUE;
        }

        ob_start();
        openside('<i class="fa fa-list"></i> '.$locale['news_0009']);
        echo '<ul class="list-style-none">';
            $categories = is_multidimensiona_array($info['news_categories'][0]) ? $info['news_categories'][0] : $info['news_categories'];
            foreach ($categories as $cat) {
                echo '<li><a'.(!empty($cat['active']) && $cat['active'] ? ' class="text-dark"' : '').' href="'.$cat['link'].'">'.$cat['name'].'</a></li>';

                if (!empty($cat['sub'])) {
                    foreach ($cat['sub'] as $sub_cat) {
                        echo '<li><a class="'.($sub_cat['active'] ? 'text-dark ' : '').'p-l-15" href="'.$sub_cat['link'].'">'.$sub_cat['name'].'</a></li>';
                    }
                }
            }
        echo '</ul>';
        closeside();
        $cats_menu = ob_get_contents();
        ob_end_clean();

        Panels::addPanel('menu_panel', $cats_menu, Panels::PANEL_RIGHT, iGUEST, 1);
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

                        echo '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 m-t-15">';
                            echo '<article class="item">';
                                echo '<figure class="thumb">';
                                    echo '<a href="'.$link.'">';
                                        $thumb = !empty($data['news_image_optimized']) ? $data['news_image_optimized'] : get_image('imagenotfound');
                                        echo '<img class="img-responsive" src="'.$thumb.'" alt="'.$data['news_subject'].'"/>';
                                    echo '</a>';
                                echo '</figure>';
                                echo '<div class="post clearfix">';
                                    echo '<h2 class="post-title"><a href="'.$link.'">'.$data['news_subject'].'</a></h2>';
                                    echo '<div class="meta">';
                                        echo '<span class="m-r-5"><i class="fa fa-user"></i> '.profile_link($data['user_id'], $data['user_name'], $data['user_status']).'</span>';
                                        echo '<span class="m-r-5"><i class="fa fa-clock-o"></i> '.showdate(fusion_get_settings('newsdate'), $data['news_date']).'</span>';
                                        echo '<span><i class="fa fa-folder-o"></i> <a href="'.INFUSIONS.'news/news.php?cat_id='.$data['news_cat_id'].'">'.$data['news_cat_name'].'</a></span>';
                                    echo '</div>';
                                    echo '<a href="'.$link.'" class="readmore m-t-5">'.self::setLocale('05').'</a>';
                                echo '</div>';
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

        self::header($info);

        echo '<div class="card">';
            echo '<h1 class="display-inline-block m-t-5">'.$data['news_subject'].'</h1>';

            echo '<div class="pull-right" id="options">';
                $action = $data['news_admin_actions'];
                if (!empty($action)) {
                    echo '<div class="btn-group">';
                        echo '<a href="'.$data['print_link'].'" class="btn btn-primary btn-sm" title="'.$locale['print'].'" target="_blank">';
                            echo '<i class="fa fa-print"></i>';
                        echo '</a>';

                        echo '<a href="'.$action['edit']['link'].'" class="btn btn-warning btn-sm" title="'.$locale['edit'].'">';
                            echo '<i class="fa fa-pencil"></i>';
                        echo '</a>';

                        echo '<a href="'.$action['delete']['link'].'" class="btn btn-danger btn-sm" title="'.$locale['delete'].'">';
                            echo '<i class="fa fa-trash"></i>';
                        echo '</a>';

                    echo '</div>';
                } else {
                    echo '<a href="'.$data['print_link'].'" class="btn btn-primary btn-sm" title="'.$locale['print'].'" target="_blank">';
                        echo '<i class="fa fa-print"></i>';
                    echo '</a>';
                }
            echo '</div>';

            echo '<div class="overflow-hide">';
                if ($data['news_image_src']) {
                    echo '<a href="'.$data['news_image_src'].'" class="news-image-overlay">';
                        $position = $data['news_image_align'] == 'news-img-center' ? 'center-x m-b-10' : $data['news_image_align'];
                        $width = $data['news_image_align'] == 'news-img-center' ? '100%' : '200px';
                        echo '<img class="img-responsive '.$position.' m-r-10" style="width: '.$width.';" src="'.$data['news_image_src'].'" alt="'.$data['news_subject'].'"/>';
                    echo '</a>';
                }

                echo '<div><b>'.$data['news_news'].'</b></div>';
                echo '<br/>';
                echo $data['news_extended'];
                echo !empty($data['news_pagenav']) ? '<div class="text-center m-10">'.$data['news_pagenav'].'</div>' : '';
            echo '</div>';

            if (!empty($data['news_gallery']) && count($data['news_gallery']) > 1) {
                echo '<hr/>';
                echo '<h3>'.$locale['news_0019'].'</h3>';

                echo '<div class="overflow-hide m-b-20">';
                    foreach ($data['news_gallery'] as $id => $image) {
                        echo '<div class="pull-left overflow-hide" style="width: 250px; height: 120px;">';
                            echo colorbox(IMAGES_N.$image['news_image'], 'Image #'.$id, TRUE);
                        echo '</div>';
                    }

                echo '</div>';
            }

            echo '<div class="well text-center m-t-10 m-b-0">';
                echo '<span class="m-l-10"><i class="fa fa-user"></i> '.profile_link($data['user_id'], $data['user_name'], $data['user_status']).'</span>';
                echo '<span class="m-l-10"><i class="fa fa-calendar"></i> '.showdate('newsdate', $data['news_datestamp']).'</span>';
                echo '<span class="m-l-10"><i class="fa fa-eye"></i> '.number_format($data['news_reads']).'</span>';

                if ($data['news_allow_comments'] && fusion_get_settings('comments_enabled') == 1) {
                    echo '<span class="m-l-10"><i class="fa fa-comments-o"></i> '.$data['news_display_comments'].'</span>';
                }

                if ($data['news_allow_ratings'] && fusion_get_settings('ratings_enabled') == 1) {
                    echo '<span class="m-l-10">'.$data['news_display_ratings'].'</span>';
                }
            echo '</div>';
        echo '</div>';

        echo $data['news_allow_comments'] && fusion_get_settings('comments_enabled') == 1 ? '<div class="card">'.$data['news_show_comments'].'</div>' : '';
        echo $data['news_allow_ratings'] && fusion_get_settings('ratings_enabled') == 1 ? '<div class="card">'.$data['news_show_ratings'].'</div>' : '';
    }
}
