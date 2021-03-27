<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Main.php
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
namespace MaterialTheme\Templates\Forum;

use MaterialTheme\Core;
use \PHPFusion\Forums\ForumServer;
use \PHPFusion\Panels;

class Main extends Core {
    public static function header() {
        add_to_head('<link rel="stylesheet" href="'.INFUSIONS.'forum/templates/css/forum.css">');
        self::setTplCss('forum');
        self::setParam('body_class', 'forum_main');
        self::setParam('container_class', 'p-b-20');
        self::setParam('right', FALSE);

        Panels::getInstance(TRUE)->hide_panel('RIGHT');
        Panels::getInstance(TRUE)->hide_panel('AU_CENTER');
        Panels::getInstance(TRUE)->hide_panel('U_CENTER');
        Panels::getInstance(TRUE)->hide_panel('L_CENTER');
        Panels::getInstance(TRUE)->hide_panel('BL_CENTER');

        \MaterialTheme\Main::headerContent([
            'id'          => 'forum',
            'title'       => fusion_get_locale('forum_0000'),
            'breadcrumbs' => FALSE
        ]);

        echo render_breadcrumbs();
    }

    public static function renderForum($info) {
        $locale = fusion_get_locale();

        self::header();

        echo '<div class="row">';
            echo '<div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">';
                if (isset($_GET['viewforum'])) {
                    self::viewForum($info);
                } else {
                    if (isset($_GET['section'])) {
                        self::renderSection($info);
                    } else {
                        self::renderForumMain($info);
                    }
                }
            echo '</div>';

            echo '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">';
                if (iMEMBER) {
                    echo '<a id="create_new_thread" href="'.FORUM.'newthread.php" class="btn btn-success btn-block m-b-20">'.$locale['forum_0057'].'</a>';
                    forum_newtopic();
                }

                self::tags();
                self::popularThreads();
            echo '</div>';
        echo '</div>';
    }

    private static function renderForumMain($info, $id = 0) {
        $locale = fusion_get_locale();

        if (!empty($info['forums'][$id])) {
            $forums = $info['forums'][$id];

            foreach ($forums as $forum_id => $data) {
                if ($data['forum_type'] == 1) {
                    echo '<div class="panel panel-primary forum-cat-panel">';
                        echo '<div class="panel-heading">';
                            echo '<h4 class="panel-title strong"><a href="'.$data['forum_link']['link'].'">'.$data['forum_link']['title'].'</a></h4>';

                            if ($data['forum_description']) {
                                echo '<span class="text-smaller">'.$data['forum_description'].'</span>';
                            }
                        echo '</div>';

                        if (isset($forums[$forum_id]['child'])) {
                            echo '<div class="list-group">';
                                $sub_forums = $forums[$forum_id]['child'];

                                foreach ($sub_forums as $sub_forum_id => $cdata) {
                                    echo '<div class="list-group-item clearfix">';
                                    render_forum_item($cdata);
                                    echo '</div>';
                            }
                            echo '</div>';
                        } else {
                            echo '<div class="panel-body text-center">';
                                echo $locale['forum_0327'];
                            echo '</div>';
                        }
                    echo '</div>';
                } else {
                    echo '<div class="list-group">';
                    if ($data['forum_type'] != 1) {
                        echo '<div class="list-group-item clearfix">';
                        render_forum_item($data);
                        echo '</div>';
                    }
                    echo '</div>';
                }
            }
        } else {
            echo '<div class="text-center card">'.$locale['forum_0328'].'</div>';
        }
    }

    private static function viewForum($info) {
        $locale = fusion_get_locale();

        if (!empty($info['forum_name'])) {
            echo '<div class="forum-title text-dark">';
                echo '<h4>'.$info['forum_name'].'</h4>';

                if (!empty($info['forum_description'])) {
                    echo '<div class="forum-description">'.$info['forum_description'].'</div>';
                }
            echo '</div>';
        }

        if (!empty($info['forum_page_link'])) {
            echo '<nav class="navbar navbar-default forum-navbar" style="min-height: inherit;">';
                echo '<div class="container-fluid">';
                    echo '<ul class="nav navbar-nav">';
                        $i = 0;
                        unset($info['forum_page_link']['subforums']); // hide subforums section
                        foreach ($info['forum_page_link'] as $view_keys => $page_link) {
                            $active = (!isset($_GET['view']) && !$i) || (isset($_GET['view']) && $_GET['view'] === $view_keys) ? ' class="active"' : '';

                            echo '<li'.$active.'><a class="p-t-10 p-b-10" href="'.$page_link['link'].'">'.$page_link['title'].'</a></li>';
                            $i++;
                        }
                    echo '</ul>';
                echo '</div>';
            echo '</nav>';
        }

        if (!empty($info['forum_rules'])) {
            echo '<div class="well m-t-20 text-white" style="background-color: #F44336;">';
                echo '<div class="strong"><i class="fa fa-exclamation"></i> '.$locale['forum_0350'].'</div>';
                echo $info['forum_rules'];
            echo '</div>';
        }

        if (isset($_GET['view'])) {
            switch ($_GET['view']) {
                default:
                case 'threads':
                    if ($info['forum_type'] > 1) {
                        echo '<div class="forum-title m-t-20">'.$locale['forum_0002'].'</div>';

                        echo '<div class="m-t-20 m-b-20">';
                        echo forum_filter($info);
                        echo '</div>';

                        echo '<div id="forumThreads" class="panel panel-default">';
                            self::renderForumThreads($info);
                        echo '</div>';
                    }
                    break;
                case 'subforums':
                    if (!empty($info['item'][$_GET['forum_id']]['child'])) {
                        echo '<div class="forum-title m-t-20">'.$locale['forum_0351'].'</div>';

                        echo '<div class="m-t-20 m-b-20">';
                        echo forum_filter($info);
                        echo '</div>';

                        echo '<div class="panel panel-default">';
                            echo '<div class="list-group">';
                                foreach ($info['item'][$_GET['forum_id']]['child'] as $subforum_id => $subforum_data) {
                                    echo '<div class="list-group-item clearfix">';
                                        render_forum_item($subforum_data);
                                    echo '</div>';
                                }
                            echo '</div>';
                        echo '</div>';
                    } else {
                        echo '<div class="card text-center">'.$locale['forum_0019'].'</div>';
                    }
                    break;
                case 'people':
                    if (!empty($info['item'])) {
                        echo '<div class="card table-responsive"><table class="table table-striped">';
                            echo '<thead><tr>';
                                echo '<th>'.$locale['forum_0018'].'</th>';
                                echo '<th>'.$locale['forum_0012'].'</th>';
                                echo '<th>'.$locale['forum_0016'].'</th>';
                            echo '</tr></thead>';
                            echo '<tbody>';
                                foreach ($info['item'] as $user) {
                                    echo '<tr>';
                                        echo '<td>'.display_avatar($user, '30px', '', FALSE, 'img-rounded m-r-10').profile_link($user['user_id'], $user['user_name'], $user['user_status']).'</td>';
                                        echo '<td><a href="'.$user['thread_link']['link'].'">'.$user['thread_link']['title'].'</a></td>';
                                        echo '<td>'.showdate('forumdate', $user['post_datestamp']).', '.timer($user['post_datestamp']).'</td>';
                                    echo '</tr>';
                                }
                            echo '</tbody>';
                        echo '</table></div>';
                    }
                    break;
                case 'activity':
                    if (!empty($info['item'])) {
                        if (!empty($info['pagenav'])) {
                            echo '<div class="pull-right">'.$info['pagenav'].'</div>';
                        }

                        if (!empty($info['max_post_count'])) {
                            echo '<div class="card"><strong>';
                                echo format_word($info['max_post_count'], $locale['fmt_post']);
                                echo ' | <a href="'.$info['last_activity']['link'].'">'.$locale['forum_0020'].'</a> ';
                                echo sprintf($locale['forum_0021'],
                                    showdate('forumdate', $info['last_activity']['time']),
                                    profile_link($info['last_activity']['user']['user_id'], $info['last_activity']['user']['user_name'], $info['last_activity']['user']['user_status'])
                                );
                            echo '</strong></div>';
                        }

                        $i = 0;
                        foreach ($info['item'] as $post_id => $postData) {
                            echo '<div class="panel panel-default">';
                                echo '<div class="panel-heading">';
                                    echo display_avatar($postData['post_author'], '30px', FALSE, '', 'm-r-10');
                                    echo '<small><b>';
                                        echo profile_link($postData['post_author']['user_id'], $postData['post_author']['user_name'], $postData['post_author']['user_status']).' ';
                                        echo showdate('forumdate', $postData['post_datestamp']).', ';
                                        echo timer($postData['post_datestamp']);
                                    echo '</b></small>';
                                echo '</div>';
                                echo '<div class="list-group">';
                                    echo '<div class="list-group-item">';
                                        echo '<div class="text-smaller text-lighter m-b-10"><b>'.$locale['forum_0023'].' '.$postData['thread_link']['title'].'</b></div>';
                                        echo parse_textarea($postData['post_message'], TRUE, TRUE, TRUE, IMAGES, TRUE);
                                    echo '</div>';
                                    echo '<div class="list-group-item">';
                                        echo '<div class="text-smaller strong">'.$locale['forum_0022'].' <a href="'.$postData['thread_link']['link'].'">'.$postData['thread_link']['title'].'</a> <i class="fa fa-external-link-alt"></i></div>';
                                    echo '</div>';
                                echo '</div>';
                            echo '</div>';
                            $i++;
                        }
                    } else {
                        echo '<div class="card text-center">'.$locale['forum_4121'].'</div>';
                    }
                    break;
            }
        } else {
            if (!empty($info['subforums'])) {

                if ($info['forum_type'] != 1) {
                    echo '<div class="forum-title m-t-20">'.$locale['forum_0351'].'</div>';
                }

                echo '<div class="panel panel-default">';
                    echo '<div class="list-group">';
                    foreach ($info['subforums'] as $subforum_id => $subforum_data) {
                        echo '<div class="list-group-item clearfix">';
                            render_forum_item($subforum_data);
                        echo '</div>';
                    }
                    echo '</div>';
                echo '</div>';
            } else {
                if ($info['forum_type'] == 1) {
                    echo '<div class=" text-center">'.$locale['forum_0327'].'</div>';
                }
            }

            if ($info['forum_type'] > 1 && !empty($info['filters']['type'])) {
                echo '<ul class="nav nav-tabs m-b-10">';
                    foreach ($info['filters']['type'] as $key => $tab) {
                        $active = $tab['active'] == 1 ? ' class="active"' : '';
                        echo '<li'.$active.'><a href="'.$tab['link'].'">'.$tab['icon'].''.$tab['title'].' <span class="badge">'.$tab['count'].'</span></a></li>';
                    }
                echo '</ul>';
            }

            if ($info['forum_type'] > 1) {
                echo '<div class="list-group">';
                    self::renderForumThreads($info);
                echo '</div>';
            }
        }

        echo '<div class="card">';
            $prm = $info['permissions'];
            $can = '<strong class="text-success">'.$locale['can'].'</strong>';
            $cannot = '<strong class="text-danger">'.$locale['cannot'].'</strong>';

            echo '<span>'.sprintf($locale['forum_perm_access'], $prm['can_access'] == TRUE ? $can : $cannot).'</span><br/>';
            echo '<span>'.sprintf($locale['forum_perm_post'], $prm['can_post'] == TRUE ? $can : $cannot).'</span><br/>';
            echo '<span>'.sprintf($locale['forum_perm_create_poll'], $prm['can_create_poll'] == TRUE ? $can : $cannot).'</span><br/>';
            echo '<span>'.sprintf($locale['forum_perm_upload'], $prm['can_upload_attach'] == TRUE ? $can : $cannot).'</span><br/>';
            echo '<span>'.sprintf($locale['forum_perm_download'], $prm['can_download_attach'] == TRUE ? $can : $cannot).'</span>';
        echo '</div>';

        if ($info['forum_moderators']) {
            echo '<div class="card"><span class="text-dark">'.$locale['forum_0185'].' '.$info['forum_moderators'].'</span></div>';
        }
    }

    private static function renderForumThreads($info) {
        $locale = fusion_get_locale();
        $data = $info['threads'];

        echo !empty($data['pagenav']) ? '<div class="text-right m-b-20">'.$data['pagenav'].'</div>' : '';

        if (!empty($data)) {
            echo '<div class="list-group">';
                if (!empty($data['sticky'])) {
                    $i = 0;
                    foreach ($data['sticky'] as $cdata) {
                        echo '<div class="list-group-item list-group-item-hover" style="background-color: #ffdca9;'.(++$i == count($data['sticky']) ? ' border-bottom: 2px solid #989898;' : '').'">';
                            render_thread_item($cdata);
                        echo '</div>';
                    }
                }

                if (!empty($data['item'])) {
                    foreach ($data['item'] as $cdata) {
                        echo '<div class="list-group-item list-group-item-hover">';
                            render_thread_item($cdata);
                        echo '</div>';
                    }
                }
            echo '</div>';
        } else {
            echo '<div class="card text-center">'.$locale['forum_0269'].'</div>';
        }

        echo !empty($data['pagenav']) ? '<div class="text-right hidden-xs m-t-15">'.$data['pagenav'].'</div>' : '';
        echo !empty($data['pagenav2']) ? '<div class="hidden-sm hidden-md hidden-lg m-t-15">'.$data['pagenav2'].'</div>' : '';
    }

    private static function renderSection($info) {
        $locale = fusion_get_locale();
        $data = $info['threads'];

        if (!empty($info['threads_time_filter'])) {
            echo '<div class="clearfix"><div class="pull-left">'.$info['threads_time_filter'].'</div></div>';
        }

        echo !empty($data['pagenav']) ? '<div class="text-right m-b-20">'.$data['pagenav'].'</div>' : '';

        if (!empty($data)) {
            echo '<div class="list-group">';
                if (!empty($data['sticky'])) {
                    foreach ($data['sticky'] as $cdata) {
                        echo '<div class="list-group-item list-group-item-hover">';
                            render_thread_item($cdata);
                        echo '</div>';
                    }
                }

                if (!empty($data['item'])) {
                    foreach ($data['item'] as $cdata) {
                        echo '<div class="list-group-item list-group-item-hover">';
                            render_thread_item($cdata);
                        echo '</div>';
                    }
                }
            echo '</div>';
        } else {
            echo '<div class="card text-center">'.$locale['forum_0269'].'</div>';
        }

        echo !empty($data['pagenav']) ? '<div class="text-right hidden-xs m-t-15">'.$data['pagenav'].'</div>' : '';
        echo !empty($data['pagenav2']) ? '<div class="hidden-sm hidden-md hidden-lg m-t-15">'.$data['pagenav2'].'</div>' : '';
    }

    public static function renderPostify($info) {
        self::header();

        opentable($info['title'], ($info['error'] ? 'card alert alert-danger' : 'card'));
            echo '<div class="text-center">';
                echo '<div>'.$info['title'].'</div>';
                echo !empty($info['message']) ? $info['message'].'<br/>' : '';
                foreach ($info['link'] as $link) {
                    echo '<p><a href="'.$link['url'].'">'.$link['title'].'</a></p>';
                }
            echo '</div>';
        closetable();
    }

    public static function tags() {
        $locale = fusion_get_locale();
        $thread_tags = ForumServer::tag(TRUE, FALSE)->get_TagInfo();

        if (!empty($thread_tags['tags'])) {
            echo '<div class="panel panel-default">';
                echo '<div class="panel-heading">'.$locale['forum_0272'].'</div>';
                echo '<div class="list-group">';
                    foreach ($thread_tags['tags'] as $tag_id => $tag_data) {
                        $active = isset($_GET['tag_id']) && $_GET['tag_id'] == $tag_id ? ' active' : '';
                        echo '<a href="'.$tag_data['tag_link'].'" class="list-group-item p-5 p-l-15'.$active.'">';
                            echo '<div class="pull-left m-r-10">';
                                $color = !empty($tag_data['tag_color']) ? $tag_data['tag_color'] : '#3498db';
                                echo '<i class="fa fa-square fa-lg" style="color: '.$color.';"></i>';
                            echo '</div>';
                            echo $tag_data['tag_title'];
                        echo '</a>';
                    }
                echo '</div>';
            echo '</div>';
        }
    }

    public static function popularThreads() {
        $locale = fusion_get_locale();

        $result = dbquery("SELECT t.thread_id, t.thread_subject, t.thread_author, t.thread_postcount
            FROM ".DB_FORUMS." tf
            INNER JOIN ".DB_FORUM_THREADS." t ON tf.forum_id=t.forum_id
            ".(multilang_column('FO') ? " WHERE forum_language='".LANGUAGE."' AND " : " WHERE ").groupaccess('forum_access')." AND (t.thread_lastpost >=:one_week AND t.thread_lastpost < :current) AND t.thread_locked=:not_locked AND t.thread_hidden=:not_hidden
            GROUP BY t.thread_id ORDER BY t.thread_postcount DESC LIMIT 10
        ", [
            ':one_week'   => TIME - (7 * 24 * 3600),
            ':current'    => TIME,
            ':not_locked' => 0,
            ':not_hidden' => 0
        ]);

        echo '<div class="panel panel-default">';
            echo '<div class="panel-heading">'.(!empty($locale['forum_0273']) ? $locale['forum_0273'] : $locale['forum_0002']).'</div>';
            echo '<div class="list-group">';

                if (dbrows($result)) {
                    while ($data = dbarray($result)) {
                        $user = fusion_get_user($data['thread_author']);

                        echo '<div class="list-group-item">';
                            echo '<a href="'.FORUM.'viewthread.php?thread_id='.$data['thread_id'].'">'.$data['thread_subject'].'</a>';
                            echo '<span class="m-l-5">'.$locale['by'].' '.profile_link($user['user_id'], $user['user_name'], $user['user_status']).'</span>';
                            echo '<span class="pull-right text-lighter"><i class="fa fa-comment"></i> '.format_word($data['thread_postcount'], $locale['fmt_post']).'</span>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="list-group-item text-center">'.(!empty($locale['forum_0275']) ? $locale['forum_0275'] : $locale['forum_0056']).'</div>';
                }

            echo '</div>';
        echo '</div>';
    }
}
