<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Blog.php
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

class Blog extends Core {
    private static function header($info, $bg = NULL) {
        $locale = fusion_get_locale();

        Main::headerContent([
            'id'         => 'blog',
            'title'      => $locale['blog_1000'],
            'background' => $bg
        ]);

        add_to_head('<style type="text/css">.blog-cat-img {width: 25px; height: 25px;}</style>');

        Panels::getInstance(TRUE)->hide_panel('RIGHT');
        self::setParam('right_post_content', self::menu($info));
    }

    public static function renderMainBlog($info) {
        add_to_head('<link rel="stylesheet" type="text/css" href="'.INFUSIONS.'blog/templates/css/blog.css"/>');
        if (isset($_GET['readmore']) && !empty($info['blog_item'])) {
            self::displayBlogItem($info);
        } else {
            self::displayBlogIndex($info);
        }
    }

    private static function displayBlogIndex($info) {
        $locale = fusion_get_locale();

        self::header($info);

        echo '<div class="card">';

        if (!empty($info['blog_item'])) {
            foreach ($info['blog_item'] as $blog_id => $data) {
                echo '<div class="row blog-item">';
                    echo '<div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">';
                        echo '<div class="pull-left">';
                            echo '<div class="pull-left m-r-5">'.$data['blog_user_avatar'].'</div>';
                            echo $data['blog_user_link'];
                        echo '</div>';
                        echo '<br/><br/>';

                        if ($data['blog_allow_comments'] && fusion_get_settings('comments_enabled') == 1) {
                            echo '<span class="m-r-10 text-lighter">';
                                echo '<a href="'.INFUSIONS.'blog/blog.php?readmore='.$blog_id.'&cat_id='.$data['blog_cat'].'#comments">';
                                    $icon = $data['blog_comments'] > 1 ? 's' : '';
                                    echo '<i class="fa fa-comment'.$icon.' fa-fw"></i> '.$data['blog_comments'];
                                echo '</a>';
                            echo '</span><br/>';
                        }

                        if ($data['blog_allow_ratings'] && fusion_get_settings('ratings_enabled') == 1) {
                            echo '<span class="m-r-10 text-lighter">';
                                echo '<i class="fa fa-star fa-fw"></i> '.$data['blog_count_votes'];
                            echo '</span><br/>';
                        }
                        echo '<span class="m-r-10 text-lighter"><i class="fa fa-eye fa-fw"></i> '.$data['blog_reads'].'</span>';
                    echo '</div>';

                    echo '<div class="col-xs-12 col-sm-9 col-md-10 col-lg-10">';
                        echo '<h3 class="strong m-b-20 m-t-0"><a href="'.$data['blog_link'].'&cat_id='.$data['blog_cat'].'">'.$data['blog_subject'].'</a></h3>';

                        if (!empty($data['blog_category_link'])) {
                            echo '<div class="display-block"><i class="fa fa-folder"></i> '.$data['blog_category_link'].'</div>';
                        }

                        echo '<div class="display-block">';
                            echo '<i class="fa fa-clock m-r-5"></i> ';
                            echo $locale['global_049'].' '.timer($data['blog_datestamp']);
                        echo '</div>';

                        if ($data['blog_image']) {
                            echo '<div class="blog-image m-10 '.$data['blog_ialign'].'">'.$data['blog_image'].'</div>';
                        }

                        echo $data['blog_blog'].'<br/>';

                        $link = INFUSIONS.'blog/blog.php?readmore='.$data['blog_id'].'&cat_id='.$data['blog_cat'];
                        echo '<a href="'.$link.'">'.$locale['blog_1006'].'</a>';
                    echo '</div>';
                echo '</div>';

                echo ($data === end($info['blog_item'])) ? '' : '<hr/>';
            }

            echo !empty($info['blog_nav']) ? '<div class="text-center">'.$info['blog_nav'].'</div>' : '';
        } else {
            echo '<div class="text-center">'.$locale['blog_3000'].'</div>';
        }

        echo '</div>';
    }

    private static function displayBlogItem($info) {
        $blog_settings = get_settings('blog');
        $locale = fusion_get_locale();
        $data = $info['blog_item'];

        $bg = !empty($data['blog_image_link']) ? $data['blog_image_link'] : Main::getRandImg();
        self::header($info, $bg);

        echo '<div class="card">';
            echo '<div class="pull-right" id="options">';
                $action = $data['admin_link'];
                if (!empty($action)) {
                    echo '<a href="'.$data['print_link'].'" class="btn btn-primary btn-circle btn-xs m-r-10" title="'.$locale['print'].'" target="_blank"><i class="fa fa-print"></i></a>';
                    echo '<a href="'.$action['edit'].'" class="btn btn-warning btn-circle btn-xs m-r-10" title="'.$locale['edit'].'"><i class="fa fa-pen"></i></a>';
                    echo '<a href="'.$action['delete'].'" class="btn btn-danger btn-circle btn-xs" title="'.$locale['delete'].'"><i class="fa fa-trash"></i></a>';
                } else {
                    echo '<a class="btn btn-primary btn-circle btn-sm print" href="'.$data['print_link'].'" title="'.$locale['print'].'" target="_blank"><i class="fa fa-print"></i></a>';
                }
            echo '</div>';

            echo '<div class="overflow-hide">';
                echo '<h2 class="strong m-t-0 m-b-0">'.$data['blog_subject'].'</h2>';
                echo '<div class="blog-category">'.$data['blog_category_link'].'</div>';
                echo '<div class="m-t-20 m-b-20">'.$data['blog_post_author'].' '.$data['blog_post_time'].'</div>';
            echo '</div>';

            if ($data['blog_image']) {
                echo '<a class="m-10 '.$data['blog_ialign'].' blog-image-overlay" href="'.$data['blog_image_link'].'">';
                    $style = 'style="padding: 5px; max-height: '.$blog_settings['blog_photo_h'].'px; overflow: hidden;"';
                    $link = $data['blog_image_link'];
                    echo '<img class="img-responsive" src="'.$link.'" alt="'.$data['blog_subject'].'" '.$style.'/>';
                echo '</a>';
            }

            echo '<div class="content-text">'.$data['blog_blog'].'<br>'.$data['blog_extended'].'</div>';

            echo $data['blog_nav'] ? '<div class="clearfix m-b-20"><div class="pull-right">'.$data['blog_nav'].'</div></div>' : '';

            echo '<div class="well">';
                $user_contact = '';
                if (isset($item['user_skype']) && $item['user_skype']) {
                    $user_contact .= '<strong>Skype:</strong> '.$item['user_skype'];
                }
                if (isset($item['user_icq']) && $item['user_icq']) {
                    $user_contact .= '<strong>ICQ:</strong> '.$item['user_icq'];
                }
                echo  '<h4>'.$locale['about'].' '.profile_link($data['user_id'], $data['user_name'], $data['user_status']).'</h4>';
                echo (isset($data['user_joined']) && $data['user_joined'] !== '') ? sprintf($locale['testimonial_join'], showdate('shortdate', $data['user_joined'])).'. ' : '';
                echo (isset($data['user_location']) && $data['user_location'] !== '') ? sprintf($locale['testimonial_location'], $data['user_location']) : '. ';
                echo (isset($data['user_web']) && $data['user_web']) ? sprintf($locale['testimonial_web'], $data['user_web']).". " : '';
                echo (isset($data['user_contact']) && $data['user_contact'] !== '') ? sprintf($locale['testimonial_contact'], $user_contact).'. ' : '';
            echo '</div>';
        echo '</div>'; // .card

        echo $data['blog_allow_comments'] && fusion_get_settings('comments_enabled') == 1 ? '<div class="card">'.$data['blog_show_comments'].'</div>' : '';
        $ratings = $data['blog_allow_ratings'] && fusion_get_settings('ratings_enabled') == 1 ? '<div class="m-b-20 ratings-box">'.$data['blog_show_ratings'].'</div>' : '';
        self::setParam('right_middle_content', $ratings);
    }

    private static function menu($info) {
        $locale = fusion_get_locale();

        self::setParam('right_card', TRUE);

        ob_start();

        $right_top = '<div class="text-bigger"><h4>'.$locale['show'].'</h4></div>';
        $right_top .= '<ul class="list-style-none m-b-20">';
            foreach ($info['blog_filter'] as $filter_key => $filter) {
                $active = isset($_GET['type']) && $_GET['type'] === $filter_key ? ' class="active strong"' : '';
                $right_top .= '<li'.$active.'><a href="'.$filter['link'].'">'.$filter['title'].'</a></li>';
            }
        $right_top .= '</ul>';

        self::setParam('right_pre_content', $right_top);

        openside($locale['blog_1003'], '', FALSE);

            echo '<div class="list-groups">';
                if (!empty($info['blog_categories'])) {
                    foreach ($info['blog_categories'][0] as $id => $data) {
                        $active = isset($_GET['cat_id']) && $_GET['cat_id'] == $id ? ' active' : '';
                        echo '<a class="list-group-item p-5 p-l-15'.$active.'" href="'.INFUSIONS.'blog/blog.php?cat_id='.$id.'">'.$data['blog_cat_name'].'</a>';

                        if ($id != 0 && $info['blog_categories'] != 0) {
                            foreach ($info['blog_categories'] as $sub_cats) {
                                foreach ($sub_cats as $sub_cat_id => $sub_cat_data) {
                                    if (!empty($sub_cat_data['blog_cat_parent']) && $sub_cat_data['blog_cat_parent'] == $id) {
                                        $active = isset($_GET['cat_id']) && $_GET['cat_id'] == $sub_cat_id ? ' active' : '';

                                        echo '<a class="list-group-item p-t-5 p-b-5 p-r-5'.$active.'" style="padding-left: 30px;" href="'.INFUSIONS.'blog/blog.php?cat_id='.$sub_cat_id.'">'.$sub_cat_data['blog_cat_name'].'</a>';
                                    }
                                }
                            }
                        }
                    }
                } else {
                    echo '<p>'.$locale['blog_3001'].'</p>';
                }
            echo '</div>';

        closeside(FALSE);

        echo '<h4>'.$locale['blog_1004'].'</h4>';
        echo '<ul class="m-b-20" id="blogarchive">';
            if (!empty($info['blog_archive'])) {
                foreach ($info['blog_archive'] as $year => $archive_data) {
                    $active = $year == date('Y') ? ' text-dark' : '';
                    echo '<li>';
                        $collaped_ = isset($_GET['archive']) && $_GET['archive'] == $year ? ' strong' : '';
                        echo '<a class="'.$active.$collaped_.'" data-toggle="collapse" data-parent="#blogarchive" href="#blog-'.$year.'" aria-expanded="false" aria-controls="blog-'.$year.'">'.$year.'</a>';

                        $collaped = isset($_GET['archive']) && $_GET['archive'] == $year ? ' in' : '';
                        echo '<ul id="blog-'.$year.'" class="collapse m-l-15 '.$collaped.'">';
                            foreach ($archive_data as $a_data) {
                                echo '<li'.($a_data['active'] ? ' class="active strong"' : '').'><a href="'.$a_data['link'].'">'.$a_data['title'].' <span class="badge m-l-10">'.$a_data['count'].'</span></a></li>';
                            }
                        echo '</ul>';
                    echo '</li>';
                }
            } else {
                echo '<li>'.$locale['blog_3002'].'</li>';
            }
        echo '</ul>';

        echo '<h4>'.$locale['blog_1005'].'</h4>';
        echo '<ul class="list-style-none m-b-20">';
            if (!empty($info['blog_author'])) {
                foreach ($info['blog_author'] as $author_info) {
                    echo '<li'.($author_info['active'] ? ' class="active strong"' : '').'>';
                        echo '<a href="'.$author_info['link'].'">'.$author_info['title'].'</a> ';
                        echo '<span class="badge m-l-10">'.$author_info['count'].'</span>';
                    echo '</li>';
                }
            } else {
                echo '<li>'.$locale['blog_3003'].'</li>';
            }
        echo '</ul>';

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}
