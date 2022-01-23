<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Dashboard.php
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
namespace AtomCP;

use PHPFusion\Admins;

class Dashboard {
    public function __construct() {
        global $global_comments, $global_ratings, $global_submissions, $link_type, $submit_data, $comments_type;

        $locale = fusion_get_locale('', ATOMCP_LOCALE);
        $aidlink = fusion_get_aidlink();
        $settings = fusion_get_settings();
        $theme_settings = atomcp_settings();
        $if_modules = !empty($theme_settings['charts']) || column_exists('users', 'user_notes') || !empty($theme_settings['infstats']) || !empty($theme_settings['usersonline']);

        $file_list = makefilelist(ATOMCP.'classes/Panels/', '.|..|index.php');
        $panels = [];
        foreach ($file_list as $name) {
            $name = str_replace('.php', '', $name);
            $panel = new \ReflectionClass('AtomCP\\Panels\\'.$name);
            $panel = $panel->newInstance();

            $panels[] = $panel;
        }

        echo '<section class="content-header">';
            echo '<h1>'.$locale['cp_100'].'</h1>';

            echo render_breadcrumbs();
        echo '</section>';

        echo '<div class="row m-t-20">';
            $col = 12;
            if ($if_modules) {
                $col = 9;
            }
            echo '<div class="col-xs-12 col-sm-12 col-md-'.$col.' col-lg-'.$col.'">';
                if (!empty($panels)) {
                    foreach ($panels as $panel) {
                        if (method_exists($panel, 'mainPanel')) {
                            $panel->mainPanel();
                        }
                    }
                }

                $sections = Admins::getInstance()->getAdminSections();
                $admin_pages = Admins::getInstance()->getAdminPages();

                if (!empty($sections)) {
                    echo '<div class="sections">';
                    foreach ($sections as $i => $section_name) {
                        if ($i != 0) {
                            $active = isset($_GET['pagenum']) && $_GET['pagenum'] == $i ? ' active' : '';
                            openside($section_name, $active, ['id' => $i, 'collapse' => TRUE, 'side_class' => FALSE]);
                                if (!empty($admin_pages[$i]) && is_array($admin_pages[$i])) {
                                    echo '<div class="row">';
                                    foreach ($admin_pages[$i] as $data) {
                                        echo '<div class="col-xs-6 col-sm-4 col-md-3 col-lg-3">';
                                            echo '<a class="icon-wrapper" title="'.$data['admin_title'].'" href="'.$data['admin_link'].$aidlink.'">';
                                                echo '<img src="'.get_image('ac_'.$data['admin_rights']).'" alt="'.$data['admin_title'].'">';
                                                echo '<span>'.$data['admin_title'].'</span>';
                                            echo '</a>';
                                        echo '</div>';
                                    }
                                    echo '</div>';
                                } else {
                                    echo '<div class="text-center">'.$locale['cp_103'].'</div>';
                                }
                            closeside('', TRUE);
                        }
                    }
                    echo '</div>';
                }

            echo '</div>';


            if ($if_modules) {
                echo '<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">';

                if (!empty($panels)) {
                    foreach ($panels as $panel) {
                        if (method_exists($panel, 'sidePanel')) {
                            $panel->sidePanel();
                        }
                    }
                }

                echo '</div>';
            }
        echo '</div>';

        echo '<div class="row">';
            if ($settings['comments_enabled'] == 1) {
                echo '<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">';
                    echo '<div id="comments">';
                        openside('<i class="fa fa-comments-o"></i> <strong class="text-uppercase">'.$locale['277'].'</strong><span class="pull-right badge bg-blue">'.number_format($global_comments['rows']).'</span>');
                            if (count($global_comments['data']) > 0) {
                                foreach ($global_comments['data'] as $i => $comment_data) {
                                    if (isset($comments_type[$comment_data['comment_type']]) && isset($link_type[$comment_data['comment_type']])) {
                                        echo '<div data-id="'.$i.'" class="clearfix p-b-10'.($i > 0 ? ' p-t-10 topborder' : '').'">';
                                            echo '<div id="comment_action-'.$i.'" class="btn-group btn-group-xs pull-right">';
                                                echo '<a class="btn btn-primary" title="'.$locale['274'].'" href="'.ADMIN.'comments.php'.$aidlink.'&ctype='.$comment_data['comment_type'].'&comment_item_id='.$comment_data['comment_item_id'].'"><i class="fa fa-eye"></i></a>';
                                                echo '<a class="btn btn-warning" title="'.$locale['275'].'" href="'.ADMIN.'comments.php'.$aidlink.'&action=edit&comment_id='.$comment_data['comment_id'].'&ctype='.$comment_data['comment_type'].'&comment_item_id='.$comment_data['comment_item_id'].'"><i class="fa fa-pencil"></i></a>';
                                                echo '<a class="btn btn-danger" title="'.$locale['276'].'" href="'.ADMIN.'comments.php'.$aidlink.'&action=delete&comment_id='.$comment_data['comment_id'].'&ctype='.$comment_data['comment_type'].'&comment_item_id='.$comment_data['comment_item_id'].'"><i class="fa fa-trash"></i></a>';
                                            echo '</div>';
                                            echo '<div class="pull-left display-inline-block m-t-5 m-b-0">'.display_avatar($comment_data, '25px', '', FALSE, 'img-circle m-r-5').'</div>';
                                            echo '<strong>'.(!empty($comment_data['user_id']) ? profile_link($comment_data['user_id'], $comment_data['user_name'], $comment_data['user_status']) : $comment_data['comment_name']).' </strong>';
                                            echo $locale['273'].' <a href="'.sprintf($link_type[$comment_data['comment_type']], $comment_data['comment_item_id']).'"><strong>'.$comments_type[$comment_data['comment_type']].'</strong></a> ';
                                            echo '<br/>';
                                            echo timer($comment_data['comment_datestamp']).'<br/>';
                                            echo '<span class="text-smaller">'.trimlink(strip_tags(parse_text($comment_data['comment_message'], ['parse_smileys' => FALSE])), 130).'</span>';
                                        echo '</div>';
                                    }
                                }

                                if (isset($global_comments['comments_nav'])) {
                                    echo '<div class="clearfix">';
                                        echo '<span class="pull-right text-smaller">'.$global_comments['comments_nav'].'</span>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<div class="text-center">'.$global_comments['nodata'].'</div>';
                            }
                        closeside();
                    echo '</div>'; // #comments
                echo '</div>';
            }

            if ($settings['ratings_enabled'] == 1) {
                echo '<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">';
                    echo '<div id="ratings">';
                        openside('<i class="fa fa-star-o"></i> <strong class="text-uppercase">'.$locale['278'].'</strong><span class="pull-right badge bg-blue">'.number_format($global_ratings['rows']).'</span>');
                            if (count($global_ratings['data']) > 0) {
                                foreach ($global_ratings['data'] as $i => $ratings_data) {
                                    if (isset($link_type[$ratings_data['rating_type']]) && isset($comments_type[$ratings_data['rating_type']])) {
                                        echo '<div data-id="'.$i.'" class="clearfix p-b-10'.($i > 0 ? ' p-t-10 topborder' : '').'">';
                                            echo '<div class="pull-left display-inline-block m-t-5 m-b-0">'.display_avatar($ratings_data, '25px', '', FALSE, 'img-circle m-r-5').'</div>';
                                            echo '<strong>'.profile_link($ratings_data['user_id'], $ratings_data['user_name'], $ratings_data['user_status']).' </strong>';
                                            echo $locale['273a'].' <a href="'.sprintf($link_type[$ratings_data['rating_type']], $ratings_data['rating_item_id']).'"><strong>'.$comments_type[$ratings_data['rating_type']].'</strong></a> ';
                                            echo timer($ratings_data['rating_datestamp']);
                                            echo '<span class="text-warning m-l-10">'.str_repeat('<i class="fa fa-star fa-fw"></i>', $ratings_data['rating_vote']).'</span>';
                                        echo '</div>';
                                    }
                                }

                                if (isset($global_ratings['ratings_nav'])) {
                                    echo '<div class="clearfix">';
                                        echo '<span class="pull-right text-smaller">'.$global_ratings['ratings_nav'].'</span>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<div class="text-center">'.$global_ratings['nodata'].'</div>';
                            }
                        closeside();
                    echo '</div>'; // #ratings
                echo '</div>';
            }

            if (!empty(\PHPFusion\Admins::getInstance()->getSubmitData())) {
                echo '<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">';
                    echo '<div id="submissions">';
                        openside('<i class="fa fa-cloud-upload"></i> <strong class="text-uppercase">'.$locale['279'].'</strong><span class="pull-right badge bg-blue">'.number_format($global_submissions['rows']).'</span>');
                            if (count($global_submissions['data']) > 0) {
                                if (!empty($submit_data)) {
                                    foreach ($global_submissions['data'] as $i => $submit_date) {
                                        $review_link = sprintf($submit_data[$submit_date['submit_type']]['admin_link'], $submit_date['submit_id']);

                                        echo '<div data-id="'.$i.'" class="clearfix p-b-10'.($i > 0 ? ' p-t-10 topborder' : '').'">';
                                            if (!empty($review_link)) {
                                                echo '<a class="btn btn-sm btn-default m-l-10 pull-right" href="'.$review_link.'" title="'.$locale['286'].'"><i class="fa fa-eye"></i></a>';
                                            }
                                            echo '<div class="pull-left display-inline-block m-t-5 m-b-0">'.display_avatar($submit_date, '25px', '', FALSE, 'img-circle m-r-5').'</div>';
                                            echo '<strong>'.profile_link($submit_date['user_id'], $submit_date['user_name'], $submit_date['user_status']).' </strong>';
                                            echo $locale['273b'].' <strong>'.$submit_data[$submit_date['submit_type']]['submit_locale'].'</strong> ';
                                            echo '<br/>';
                                            echo timer($submit_date['submit_datestamp']);
                                        echo '</div>';
                                    }
                                }

                                if (isset($global_submissions['submissions_nav'])) {
                                    echo '<div class="clearfix">';
                                        echo '<span class="pull-right text-smaller">'.$global_submissions['submissions_nav'].'</span>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<div class="text-center">'.$global_submissions['nodata'].'</div>';
                            }
                        closeside();
                    echo '</div>'; // #submissions
                echo '</div>';
            }

        echo '</div>'; // .row
    }
}
