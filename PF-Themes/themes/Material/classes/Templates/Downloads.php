<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Downloads.php
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
use PHPFusion\Panels;

class Downloads extends Core {
    public static function renderDownloads($info) {
        $locale = fusion_get_locale();
        self::setTplCss('downloads');
        self::setParam('body_class', 'downloads');

        Main::headerContent([
            'id'           => 'downloads',
            'title'        => $locale['download_1000'],
            'small_header' => TRUE
        ]);

        add_to_jquery('$(".downloads #downloads_search").removeClass("text-white");');

        Panels::getInstance(TRUE)->hide_panel('RIGHT');

        self::setParam('right_post_content', self::menu($info));

        echo self::horizontalMenu($info);

        if ($info['get']['download_id'] && !empty($info['download_item'])) {
            self::displayDownloadItem($info);
        } else {
            self::displayDownloadIndex($info);
        }
    }

    private static function displayDownloadIndex($info) {
        $locale = fusion_get_locale();
        $dl_settings = get_settings('downloads');

        if (!empty($info['download_item'])) {
            echo '<div class="row equal-height">';
                foreach ($info['download_item'] as $data) {
                    echo '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">';
                        echo '<div class="card download-item">';
                            $link = DOWNLOADS.'downloads.php?cat_id='.$data['download_cat_id'].'&download_id='.$data['download_id'];

                            if ($dl_settings['download_screenshot'] == 1) {
                                echo '<div class="preview text-center"><a href="'.$link.'">';
                                    if (!empty($data['download_thumb']) && file_exists($data['download_thumb'])) {
                                        echo '<img class="img-responsive" src="'.$data['download_thumb'].'" alt="'.$data['download_title'].'">';
                                    } else {
                                        echo get_image('imagenotfound', $data['download_title']);
                                    }
                                echo '</a></div>';
                            }

                            echo '<div class="card-body">';
                                echo '<h4 class="text-center"><a href="'.$link.'">'.trimlink($data['download_title'], 20).'</a></h4>';

                                echo '<div class="card-meta text-center">';
                                    echo '<a href="'.DOWNLOADS.'downloads.php?cat_id='.$data['download_cat_id'].'">'.$data['download_cat_name'].'</a>';
                                    echo '<br/>';
                                    echo '<time>'.$data['download_post_time'].'</time>';
                                echo '</div>';

                                echo '<div class="publisher">';
                                    echo display_avatar($data, '40px', '', FALSE, 'img-circle m-r-10');
                                    echo self::setLocale('dl_05').' ';
                                    echo !empty($data['user_id']) ? profile_link($data['user_id'], $data['user_name'], $data['user_status']) : $locale['user_na'];
                                echo '</div>';
                            echo '</div>';

                            echo '<div class="card-footer">';
                                echo '<a href="'.$link.'" class="btn btn-primary btn-sm btn-block">';
                                    echo '<i class="fa fa-download"></i> '.$locale['download_1007'];
                                echo '</a>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';
                }
            echo '</div>';

            echo !empty($info['download_nav']) ? '<div class="text-center m-b-20">'.$info['download_nav'].'</div>' : '';
        } else {
            echo '<div class="card text-center">'.$locale['download_3000'].'</div>';
        }
    }

    private static function displayDownloadItem($info) {
        $dl_settings = get_settings('downloads');
        $locale = fusion_get_locale();
        self::setParam('content_container', FALSE);
        $data = $info['download_item'];

        echo '<div class="card">';
            if ($data['admin_link']) {
                $admin_actions = $data['admin_link'];
                echo '<div class="btn-group pull-right">';
                    echo '<a class="btn btn-default btn-sm" href="'.$admin_actions['edit'].'"><i class="fa fa-pen"></i> '.$locale['edit'].'</a>';
                    echo '<a class="btn btn-danger btn-sm" href="'.$admin_actions['delete'].'"><i class="fa fa-trash"></i> '.$locale['delete'].'</a>';
                echo '</div>';
            }

            echo '<h3 class="m-t-0 m-b-0">'.$data['download_title'].'</h3>';
            echo $data['download_description_short'];
            echo '<hr/>';

            echo '<div class="row m-b-20">';
                if ($dl_settings['download_screenshot'] == 1) {
                    echo '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">';
                        if ($data['download_image'] && file_exists(DOWNLOADS.'images/'.$data['download_image'])) {
                            echo thumbnail(DOWNLOADS.'images/'.$data['download_image'], '200px');
                        } else {
                            echo get_image('imagenotfound', $data['download_title']);
                        }
                    echo '</div>';

                    $grid = 9;
                } else {
                    $grid = 12;
                }

                echo '<div class="col-xs-12 col-sm-'.$grid.' col-md-'.$grid.' col-lg-'.$grid.'">';
                    $profile = profile_link($data['user_id'], $data['user_name'], $data['user_status']);
                    echo '<strong>'.$locale['global_050'].'</strong>: '.$profile.'<br/>';
                    echo '<strong>'.$locale['download_1017'].'</strong>: '.$data['download_homepage'].'<br/>';
                    echo '<strong>'.self::setLocale('dl_01').': </strong> ';
                    $link = DOWNLOADS.'downloads.php?cat_id='.$data['download_cat_id'];
                    echo '<a href="'.$link.'">'.$data['download_cat_name'].'</a>';
                    echo '<br/>';
                    echo '<a href="'.$data['download_file_link'].'" class="btn btn-primary m-b-20">';
                        echo '<i class="fa fa-download"></i> '.self::setLocale('dl_02').($data['download_filesize'] ? ' ('.$data['download_filesize'].')' : '');
                    echo '</a>';
                echo '</div>';
            echo '</div>';

            if ($data['download_description']) {
                echo '<div class="p-10 m-b-20 item-border">';
                    echo '<strong>'.self::setLocale('dl_03').'</strong><br/>';
                    echo $data['download_description'];
                echo '</div>';
            }

            if ($dl_settings['download_screenshot'] == 1 && $data['download_image'] && file_exists(DOWNLOADS.'images/'.$data['download_image'])) {
                echo '<div class="p-10 m-b-20 item-border">';
                    echo '<strong>'.self::setLocale('dl_04').'</strong><br/>';
                    $link = DOWNLOADS.'images/'.$data['download_image'];
                    echo '<img src="'.$link.'" alt="'.$data['download_title'].'" class="img-responsive">';
                echo '</div>';
            }

            echo '<div class="p-10 item-border">';
                echo '<strong>'.$locale['download_1010'].'</strong>';
                echo '<div class="row">';
                    echo '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">';
                        echo '<span class="strong text-smaller text-lighter">'.$locale['download_1011'].': </span>'.$data['download_version'];
                    echo '</div>';

                    echo '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">';
                        echo '<span class="strong text-smaller text-lighter">'.$locale['download_1012'].': </span>'.$data['download_count'];
                    echo '</div>';

                    echo '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">';
                        echo '<span class="strong text-smaller text-lighter">'.$locale['download_1021'].': </span>'.$data['download_post_time'];
                    echo '</div>';
                echo '</div>';

                echo '<hr/>';

                echo '<div class="row">';
                    echo '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">';
                        echo '<span class="strong text-smaller text-lighter">'.$locale['download_1013'].': </span>'.$data['download_license'];
                    echo '</div>';

                    echo '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">';
                        echo '<span class="strong text-smaller text-lighter">'.$locale['download_1014'].': </span>'.$data['download_os'];
                    echo '</div>';

                    echo '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">';
                        echo '<span class="strong text-smaller text-lighter">'.$locale['download_1015'].': </span>'.$data['download_copyright'];
                    echo '</div>';
                echo '</div>';
            echo '</div>';

        echo '</div>';

        echo $data['download_allow_comments'] && fusion_get_settings('comments_enabled') == 1 ? '<div class="card">'.$data['download_show_comments'].'</div>' : '';
        $ratings = $data['download_allow_ratings'] && fusion_get_settings('ratings_enabled') == 1 ? '<div class="m-b-20 ratings-box">'.$data['download_show_ratings'].'</div>' : '';
        self::setParam('right_middle_content', $ratings);
    }

    private static function horizontalMenu($info) {
        ob_start();

        echo '<div class="horizontal-menu clearfix">';
            echo '<ul class="list-style-none menu pull-right">';
                foreach ($info['download_filter'] as $filter_key => $filter) {
                    $active = isset($_GET['type']) && $_GET['type'] === $filter_key ? ' class="active strong"' : '';
                    echo '<li'.$active.'><a href="'.$filter['link'].'">'.$filter['title'].'</a></li>';
                }
            echo '</ul>';
        echo '</div>';

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    private static function menu($info) {
        $locale = fusion_get_locale();

        self::setParam('right_card', TRUE);

        ob_start();

        openside($locale['download_1003'], '', FALSE);
            if (is_array($info['download_categories']) && !empty($info['download_categories'])) {
                $result = dbquery("SELECT dc.*, COUNT(d.download_id) AS download_count
                    FROM ".DB_DOWNLOAD_CATS." dc
                    LEFT JOIN ".DB_DOWNLOADS." AS d ON d.download_cat=dc.download_cat_id
                    WHERE ".(multilang_table('DL') ? "dc.download_cat_language='".LANGUAGE."'" : '')."
                    GROUP BY dc.download_cat_id
                    ORDER BY dc.download_cat_id ASC
                ");

                $dl_cats = [];

                if (dbrows($result) > 0) {
                    while ($data = dbarray($result)) {
                        $dl_cats[$data['download_cat_id']] = [
                            'link'  => INFUSIONS.'downloads/downloads.php?cat_id='.$data['download_cat_id'],
                            'name'  => $data['download_cat_name'],
                            'count' => $data['download_count'],
                            'desc'  => !empty($data['download_cat_description']) ? 'title="'.$data['download_cat_description'].'" data-toggle="tooltip"' : ''
                        ];
                    }
                }

                echo '<div class="list-group">';
                    foreach ($dl_cats as $cat_id => $cat_data) {
                        $active = !empty($_GET['cat_id']) && $_GET['cat_id'] == $cat_id ? ' active' : '';

                        echo '<a class="list-group-item p-5 p-l-10'.$active.'" '.$cat_data['desc'].' href="'.$cat_data['link'].'">';
                            echo $cat_data['name'];
                            echo '<span class="badge m-t-2">'.$cat_data['count'].'</span>';
                        echo '</a>';
                    }
                echo '</div>';
            } else {
                echo '<p>'.$locale['download_3001'].'</p>';
            }

        closeside(FALSE);

        echo '<h4>'.$locale['download_1004'].'</h4>';
        echo '<ul class="list-style-none m-b-20">';
            if (!empty($info['download_author'])) {
                foreach ($info['download_author'] as $author_info) {
                    echo '<li'.($author_info['active'] ? ' class="active strong"' : '').'>';
                        echo '<a href="'.$author_info['link'].'">'.$author_info['title'].'</a> ';
                        echo '<span class="badge m-l-10">'.$author_info['count'].'</span>';
                    echo '</li>';
                }
            } else {
                echo '<li>'.$locale['download_3002'].'</li>';
            }
        echo '</ul>';

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}
