<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Dashboard.php
| Author: YOUR_NAMELICENSE_TEXT
+--------------------------------------------------------*/
namespace ADDON_NAME;

class Dashboard {
    public function __construct() {
        $pagenum = (int)filter_input(INPUT_GET, 'pagenum');

        if ((isset($pagenum) && $pagenum) > 0) {
            global $admin_icons;

            $locale = fusion_get_locale();
            $aidlink = fusion_get_aidlink();

            $admin_title = str_replace('[SITENAME]', fusion_get_settings('sitename'), $locale['200']);
            $admin_title = !empty($locale['200a']) ? $locale['200a'] : $admin_title;

            $html = fusion_get_function('opentable', $admin_title);
            $html .= '<div class="row">';
                if (count($admin_icons['data']) > 0) {
                    foreach ($admin_icons['data'] as $data) {
                        $html .= '<div class="icon-wrapper col-xs-6 col-sm-2 col-md-2 col-lg-2">';
                            $html .= '<a class="text-center" href="'.$data['admin_link'].$aidlink.'">';
                                $html .= '<img class="display-block" src="'.get_image('ac_'.$data['admin_rights']).'" alt="'.$data['admin_title'].'"/>';
                                $html .= '<div>'.$data['admin_title'].'</div>';
                            $html .= '</a>';
                        $html .= '</div>';
                    }
                }
            $html .= '</div>';
            $html .= fusion_get_function('closetable', '');
        } else {
            global $members, $forum, $download, $news, $articles, $weblinks, $photos,
                   $global_comments, $global_ratings, $global_submissions, $global_infusions, $link_type, $submit_data, $comments_type, $infusions_count;

            $locale = fusion_get_locale();
            $aidlink = fusion_get_aidlink();
            $settings = fusion_get_settings();

            $html = '';
            $html .= fusion_get_function('opentable', $locale['250']);
                $grid = ['mobile' => 12, 'tablet' => 6, 'laptop' => 3, 'desktop' => 3];

                $panels = [
                    'registered'   => ['link' => '', 'title' => $locale['251']],
                    'cancelled'    => ['link' => 'status=5', 'title' => $locale['263']],
                    'unactivated'  => ['link' => 'status=2', 'title' => $locale['252']],
                    'security_ban' => ['link' => 'status=4', 'title' => $locale['253']]
                ];

                $html .= '<div class="members">';
                    $html .= '<div class="row">';
                        foreach ($panels as $panel => $block) {
                            $block['link'] = empty($block['link']) ? $block['link'] : '&'.$block['link'];
                            $html .= '<div class="col-xs-'.$grid['mobile'].' col-sm-'.$grid['tablet'].' col-md-'.$grid['laptop'].' col-lg-'.$grid['desktop'].' block">';
                            $html .= fusion_get_function('openside', '', $panel);
                                $html .= '<img class="pull-left m-r-10 dashboard-icon" src="'.get_image('ac_M').'" alt="'.$locale['M'].'"/>';
                                $html .= '<h4 class="text-right m-t-0 m-b-0">'.number_format($members[$panel]).'</h4>';
                                $html .= '<strong class="text-smaller pull-right" style="position: relative;z-index: 3;">'.$block['title'].'</strong>';

                                $content  = '<div class="text-right text-uppercase">';
                                $content .= '<a href="'.ADMIN.'members.php'.$aidlink.$block['link'].'">'.$locale['255'].' <i class="fa fa-angle-right"></i></a>';
                                $content .= '</div>';
                            $html .= fusion_get_function('closeside', checkrights('M') ? $content : '');
                            $html .= '</div>';
                        }
                    $html .= '</div>';
                $html .= '</div>';

                $grid = ['mobile' => 12, 'tablet' => 6, 'laptop' => 6, 'desktop' => 4];

                $html .= '<div class="row" id="overview">';
                    $modules = [];

                    if (defined('FORUM_EXISTS')) {
                        $modules['forum'] = [
                            'title' => $locale['265'],
                            'image' => get_image('ac_F'),
                            'stats' => [
                                ['title' => $locale['265'], 'count' => $forum['count']],
                                ['title' => $locale['256'], 'count' => $forum['thread']],
                                ['title' => $locale['259'], 'count' => $forum['post']],
                                ['title' => $locale['260'], 'count' => $forum['users']]
                            ]
                        ];
                    }

                    if (defined('DOWNLOADS_EXISTS')) {
                        $modules['downloads'] = [
                            'title' => $locale['268'],
                            'image' => get_image('ac_D'),
                            'stats' => [
                                ['title' => $locale['268'], 'count' => $download['download']],
                                ['title' => $locale['257'], 'count' => $download['comment']],
                                ['title' => $locale['254'], 'count' => $download['submit']]
                            ]
                        ];
                    }

                    if (defined('NEWS_EXISTS')) {
                        $modules['news'] = [
                            'title' => $locale['269'],
                            'image' => get_image('ac_N'),
                            'stats' => [
                                ['title' => $locale['269'], 'count' => $news['news']],
                                ['title' => $locale['257'], 'count' => $news['comment']],
                                ['title' => $locale['254'], 'count' => $news['submit']]
                            ]
                        ];
                    }

                    if (defined('ARTICLES_EXISTS')) {
                        $modules['articles'] = [
                            'title' => $locale['270'],
                            'image' => get_image('ac_A'),
                            'stats' => [
                                ['title' => $locale['270'], 'count' => $articles['article']],
                                ['title' => $locale['257'], 'count' => $articles['comment']],
                                ['title' => $locale['254'], 'count' => $articles['submit']]
                            ]
                        ];
                    }

                    if (defined('WEBLINKS_EXISTS')) {
                        $modules['weblinks'] = [
                            'title' => $locale['271'],
                            'image' => get_image('ac_W'),
                            'stats' => [
                                ['title' => $locale['271'], 'count' => $weblinks['weblink']],
                                ['title' => $locale['254'], 'count' => $weblinks['submit']]
                            ]
                        ];
                    }

                    if (defined('GALLERY_EXISTS')) {
                        $modules['gallery'] = [
                            'title' => $locale['272'],
                            'image' => get_image('ac_PH'),
                            'stats' => [
                                ['title' => $locale['261'], 'count' => $photos['photo']],
                                ['title' => $locale['257'], 'count' => $photos['comment']],
                                ['title' => $locale['254'], 'count' => $photos['submit']]
                            ]
                        ];
                    }

                    if (!empty($modules)) {
                        foreach ($modules as $module) {
                            $html .= '<div class="col-xs-'.$grid['mobile'].' col-sm-'.$grid['tablet'].' col-md-'.$grid['laptop'].' col-lg-'.$grid['desktop'].'">';
                                $html .= fusion_get_function('openside', '');
                                $html .= '<strong class="text-uppercase">'.$module['title'].' '.$locale['258'].'</strong>';
                                $html .= '<div class="clearfix m-t-10">';
                                    $html .= '<img class="img-responsive pull-right dashboard-icon" src="'.$module['image'].'" alt="'.$module['title'].'"/>';
                                    if (!empty($module['stats'])) {
                                        foreach ($module['stats'] as $stat) {
                                            $html .= '<div class="pull-left display-inline-block m-r-10">';
                                                $html .= '<span class="text-smaller">'.$stat['title'].'</span><br/>';
                                                $html .= '<h4 class="m-t-0">'.number_format($stat['count']).'</h4>';
                                            $html .= '</div>';
                                        }
                                    }
                                $html .= '</div>';
                                $html .= fusion_get_function('closeside', '');
                            $html .= '</div>';
                        }
                    }
                $html .= '</div>';

                $html .= '<div class="row">';
                    $html .= '<div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">';
                        if ($settings['comments_enabled'] == 1) {
                            $html .= '<div id="comments">';
                                $html .= fusion_get_function('openside', '<strong class="text-uppercase">'.$locale['277'].'</strong><span class="pull-right badge">'.number_format($global_comments['rows']).'</span>');
                                    if (count($global_comments['data']) > 0) {
                                        foreach ($global_comments['data'] as $i => $comment_data) {
                                            if (isset($comments_type[$comment_data['comment_type']]) && isset($link_type[$comment_data['comment_type']])) {
                                                $html .= '<div data-id="'.$i.'" class="clearfix p-b-10'.($i > 0 ? ' p-t-10' : '').'"'.($i > 0 ? ' style="border-top: 1px solid #ddd;"' : '').'>';
                                                    $html .= '<div id="comment_action-'.$i.'" class="btn-group btn-group-xs pull-right m-t-10">';
                                                        $html .= '<a class="btn btn-primary" title="'.$locale['274'].'" href="'.ADMIN.'comments.php'.$aidlink.'&ctype='.$comment_data['comment_type'].'&comment_item_id='.$comment_data['comment_item_id'].'"><i class="fa fa-eye"></i></a>';
                                                        $html .= '<a class="btn btn-warning" title="'.$locale['275'].'" href="'.ADMIN.'comments.php'.$aidlink.'&action=edit&comment_id='.$comment_data['comment_id'].'&ctype='.$comment_data['comment_type'].'&comment_item_id='.$comment_data['comment_item_id'].'"><i class="fa fa-pencil"></i></a>';
                                                        $html .= '<a class="btn btn-danger" title="'.$locale['276'].'" href="'.ADMIN.'comments.php'.$aidlink.'&action=delete&comment_id='.$comment_data['comment_id'].'&ctype='.$comment_data['comment_type'].'&comment_item_id='.$comment_data['comment_item_id'].'"><i class="fa fa-trash"></i></a>';
                                                    $html .= '</div>';
                                                    $html .= '<div class="pull-left display-inline-block m-t-5 m-b-0">'.display_avatar($comment_data, '25px', '', FALSE, 'img-rounded m-r-5').'</div>';
                                                    $html .= '<strong>'.(!empty($comment_data['user_id']) ? profile_link($comment_data['user_id'], $comment_data['user_name'], $comment_data['user_status']) : $comment_data['comment_name']).' </strong>';
                                                    $html .= $locale['273'].' <a href="'.sprintf($link_type[$comment_data['comment_type']], $comment_data['comment_item_id']).'"><strong>'.$comments_type[$comment_data['comment_type']].'</strong></a> ';
                                                    $html .= timer($comment_data['comment_datestamp']).'<br/>';
                                                    $html .= '<span class="text-smaller">'.trimlink(strip_tags(parse_text($comment_data['comment_message'], ['parse_smileys' => FALSE])), 130).'</span>';
                                                $html .= '</div>';
                                            }
                                        }

                                        if (isset($global_comments['comments_nav'])) {
                                            $html .= '<div class="clearfix"><span class="pull-right text-smaller">'.$global_comments['comments_nav'].'</span></div>';
                                        }
                                    } else {
                                        $html .= '<div class="text-center">'.$global_comments['nodata'].'</div>';
                                    }
                                $html .= fusion_get_function('closeside', '');
                            $html .= '</div>'; // #comments
                        }

                        if ($settings['ratings_enabled'] == 1) {
                            $html .= '<div id="ratings">';
                                $html .= fusion_get_function('openside', '<strong class="text-uppercase">'.$locale['278'].'</strong><span class="pull-right badge">'.number_format($global_ratings['rows']).'</span>');
                                    if (count($global_ratings['data']) > 0) {
                                        foreach ($global_ratings['data'] as $i => $ratings_data) {
                                            if (isset($link_type[$ratings_data['rating_type']]) && isset($comments_type[$ratings_data['rating_type']])) {
                                                $html .= '<div data-id="'.$i.'" class="clearfix p-b-10'.($i > 0 ? ' p-t-10' : '').'"'.($i > 0 ? ' style="border-top: 1px solid #ddd;"' : '').'>';
                                                    $html .= '<div class="pull-left display-inline-block m-t-5 m-b-0">'.display_avatar($ratings_data, '25px', '', FALSE, 'img-rounded m-r-5').'</div>';
                                                    $html .= '<strong>'.profile_link($ratings_data['user_id'], $ratings_data['user_name'], $ratings_data['user_status']).' </strong>';
                                                    $html .= $locale['273a'].' <a href="'.sprintf($link_type[$ratings_data['rating_type']], $ratings_data['rating_item_id']).'"><strong>'.$comments_type[$ratings_data['rating_type']].'</strong></a> ';
                                                    $html .= timer($ratings_data['rating_datestamp']);
                                                    $html .= '<span class="text-warning m-l-10">'.str_repeat('<i class="fa fa-star fa-fw"></i>', $ratings_data['rating_vote']).'</span>';
                                                $html .= '</div>';
                                            }
                                        }

                                        if (isset($global_ratings['ratings_nav'])) {
                                            $html .= '<div class="clearfix"><span class="pull-right text-smaller">'.$global_ratings['ratings_nav'].'</span></div>';
                                        }
                                    } else {
                                        $html .= '<div class="text-center">'.$global_ratings['nodata'].'</div>';
                                    }
                                $html .= fusion_get_function('closeside', '');
                            $html .= '</div>'; // #ratings
                        }

                        if (!empty(\PHPFusion\Admins::getInstance()->getSubmitData())) {
                            $html .= '<div id="submissions">';
                                $html .= fusion_get_function('openside', '<strong class="text-uppercase">'.$locale['279'].'</strong><span class="pull-right badge">'.number_format($global_submissions['rows']).'</span>');
                                    if (count($global_submissions['data']) > 0) {
                                        foreach ($global_submissions['data'] as $i => $submit_date) {
                                            if (isset($submit_data[$submit_date['submit_type']])) {
                                                $review_link = sprintf($submit_data[$submit_date['submit_type']]['admin_link'], $submit_date['submit_id']);

                                                $html .= '<div data-id="'.$i.'" class="clearfix p-b-10'.($i > 0 ? ' p-t-10' : '').'"'.($i > 0 ? ' style="border-top: 1px solid #ddd;"' : '').'>';
                                                    $html .= '<div class="pull-left display-inline-block m-t-5 m-b-0">'.display_avatar($submit_date, '25px', '', FALSE, 'img-rounded m-r-5').'</div>';
                                                    $html .= '<strong>'.profile_link($submit_date['user_id'], $submit_date['user_name'], $submit_date['user_status']).' </strong>';
                                                    $html .= $locale['273b'].' <strong>'.$submit_data[$submit_date['submit_type']]['submit_locale'].'</strong> ';
                                                    $html .= timer($submit_date['submit_datestamp']);
                                                    if (!empty($review_link)) {
                                                        $html .= '<a class="btn btn-sm btn-default m-l-10 pull-right" href="'.$review_link.'">'.$locale['286'].'</a>';
                                                    }
                                                $html .= '</div>';
                                            }
                                        }

                                        if (isset($global_submissions['submissions_nav'])) {
                                            $html .= '<div class="clearfix"><span class="pull-right text-smaller">'.$global_submissions['submissions_nav'].'</span></div>';
                                        }
                                    } else {
                                        $html .= '<div class="text-center">'.$global_submissions['nodata'].'</div>';
                                    }
                                $html .= fusion_get_function('closeside', '');
                            $html .= '</div>'; // #submissions
                        }

                    $html .= '</div>';

                    if (checkrights('I')) {
                        $html .= '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">';
                            $html .= '<div id="infusions">';
                                $html .= fusion_get_function('openside', '<strong class="text-uppercase">'.$locale['283'].'</strong><span class="pull-right badge">'.number_format($infusions_count).'</span>');
                                    $content = '';
                                    if ($infusions_count > 0) {
                                        if (!empty($global_infusions)) {
                                            foreach ($global_infusions as $inf_data) {
                                                $html .= '<span class="badge m-b-10 m-r-5">'.$inf_data['inf_title'].'</span>';
                                            }
                                        }
                                        $content = checkrights('I') ? '<div class="text-right text-uppercase"><a class="text-smaller" href="'.ADMIN.'infusions.php'.$aidlink.'">'.$locale['285'].' <i class="fa fa-angle-right"></i></a></div>' : '';
                                    } else {
                                        $html .= '<div class="text-center">'.$locale['284'].'</div>';
                                    }
                                $html .= fusion_get_function('closeside', $content);
                            $html .= '</div>'; // #infusins
                        $html .= '</div>';
                    }
                $html .= '</div>'; // .row
            $html .= fusion_get_function('closetable');
        }

        echo $html;
    }
}
