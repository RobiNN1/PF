<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: ViewThread.php
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

class ViewThread extends Core {
    public static function renderThread($info) {
        $locale = fusion_get_locale();

        Main::header();

        $buttons = !empty($info['buttons']) ? $info['buttons'] : [];
        $data    = !empty($info['thread']) ? $info['thread'] : [];
        $pdata   = !empty($info['post_items']) ? $info['post_items'] : [];

        echo '<div class="row">';
            echo '<div class="col-xs-12 col-sm-10 col-md-10 col-lg-9 thread">';
                echo '<h2>';
                    if ($data['thread_sticky'] == TRUE) {
                        echo '<i title="'.$locale['forum_0103'].'" class="'.get_forumIcons('sticky').'"></i>';
                    }

                    if ($data['thread_locked'] == TRUE) {
                        echo '<i title="'.$locale['forum_0102'].'" class="'.get_forumIcons('lock').'"></i>';
                    }

                    echo $data['thread_subject'];
                echo '</h2>';

                echo '<span class="last-updated">'.$locale['forum_0363'].' '.timer($data['thread_lastpost']).'</span>';

                if (!empty($info['thread_tags_display'])) {
                    echo '<div class="clearfix"><i class="fa fa-tags text-lighter fa-fw"></i> '.$info['thread_tags_display'].'</div>';
                }

                echo !empty($info['poll_form']) ? '<div class="card m-t-20">'.$info['poll_form'].'</div>' : '';

                echo '<div class="clearfix m-t-20">';
                    echo '<div class="clearfix">';
                        echo '<div class="pull-left">';
                            echo '<div class="dropdown display-inline-block m-r-10">';
                                echo '<a id="ddfiltertime" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                                    echo '<strong>'.$locale['forum_0183'].'</strong> ';
                                    $selector['oldest'] = $locale['forum_0180'];
                                    $selector['latest'] = $locale['forum_0181'];
                                    echo isset($_GET['section']) && in_array($_GET['section'], array_flip($selector)) ? $selector[$_GET['section']] : $locale['forum_0180'];
                                    echo '<span class="caret"></span>';
                                echo '</a>';

                                if (!empty($info['post-filters'])) {
                                    echo '<ul class="dropdown-menu" aria-labelledby="ddfiltertime">';
                                        foreach ($info['post-filters'] as $filters) {
                                            echo '<li><a class="text-smaller" href="'.$filters['value'].'">'.$filters['locale'].'</a></li>';
                                        }
                                    echo '</ul>';
                                }
                            echo '</div>';

                            if (!empty($buttons['notify'])) {
                                echo '<a class="btn btn-default btn-sm m-r-10" href="'.$buttons['notify']['link'].'">'.$buttons['notify']['title'].'</a>';
                            }

                            echo '<a class="btn btn-default btn-sm" href="'.$buttons['print']['link'].'">'.$buttons['print']['title'].'</a>';
                        echo '</div>';

                        echo '<div class="pull-right">';
                            if ($info['permissions']['can_start_bounty']) {
                                $active = !empty($info['thread']['thread_bounty']) ? ' disabled' : '';
                                echo '<a class="btn btn-primary btn-sm m-r-10'.$active.'" href="'.$buttons['bounty']['link'].'">'.$buttons['bounty']['title'].'</a>';
                            }

                            if ($info['permissions']['can_create_poll'] && $info['permissions']['can_post']) {
                                $active = !empty($info['thread']['thread_poll']) ? ' disabled' : '';
                                echo '<a class="btn btn-success btn-sm m-r-10'.$active.'" href="'.$buttons['poll']['link'].'">'.$buttons['poll']['title'].'</a>';
                            }

                            if ($info['permissions']['can_post']) {
                                $active = empty($buttons['newthread']) ? ' disabled' : '';
                                echo '<a class="btn btn-primary btn-sm'.$active.'" href="'.$buttons['newthread']['link'].'">'.$buttons['newthread']['title'].'</a>';
                            }

                        echo '</div>';
                    echo '</div>';

                    echo !empty($info['page_nav']) ? '<div class="pull-right m-t-10 clearfix">'.$info['page_nav'].'</div>' : '';
                echo '</div>';

                if (!empty($pdata)) {
                    $i = 1;
                    foreach ($pdata as $post_id => $post_data) {
                        render_post_item($post_data, $i + (isset($_GET['rowstart']) ? $_GET['rowstart'] : ''));

                        if ($post_id == $info['post_firstpost']) {
                            if ($info['permissions']['can_post'] == 1) {
                                if (!empty($buttons['reply'])) {
                                    echo '<div class="text-right m-t-10 m-b-20">';
                                        $active = empty($buttons['reply']) ? ' disabled' : '';
                                        echo '<a class="btn btn-success btn-md m-l-20 vatop'.$active.'" href="'.$buttons['reply']['link'].'">';
                                            echo $buttons['reply']['title'];
                                        echo '</a>';
                                    echo '</div>';
                                }
                            }

                            if ($info['thread_bounty']) {
                                echo '<div class="block-bounty m-b-20">'.$info['thread_bounty'].'</div>';
                            }
                        }

                        $i++;
                    }
                }

                if (iMOD) {
                    echo $info['mod_form'];
                }

                echo '<div class="clearfix m-t-20">';
                    echo '<div class="pull-left">';
                        if ($info['permissions']['can_post']) {
                            $active = empty($buttons['newthread']) ? ' disabled' : '';
                            echo '<a class="btn btn-primary btn-sm m-r-10'.$active.'" href="'.$buttons['newthread']['link'].'">';
                                echo $buttons['newthread']['title'];
                            echo '</a>';
                        }

                        if ($info['permissions']['can_post']) {
                            if (!empty($buttons['reply'])) {
                                $active = empty($buttons['reply']) ? ' disabled' : '';
                                echo '<a class="btn btn-primary btn-sm'.$active.'" href="'.$buttons['reply']['link'].'">';
                                    echo $buttons['reply']['title'];
                                echo '</a>';
                            }
                        }
                    echo '</div>';

                    echo !empty($info['page_nav']) ? '<div class="pull-right clearfix">'.$info['page_nav'].'</div>' : '';
                echo '</div>';

                if (!empty($info['quick_reply_form'])) {
                    add_to_jquery('$("#post_quick_reply").removeClass("m-r-10");');
                    echo '<div class="m-t-10 p-t-5 p-b-0">'.$info['quick_reply_form'].'</div>';
                }

                echo '<div class="block m-t-20 m-b-20">';
                    $prm = $info['permissions'];
                    $can = '<strong class="text-success">'.$locale['can'].'</strong>';
                    $cannot = '<strong class="text-danger">'.$locale['cannot'].'</strong>';
                    $poll = $data['thread_poll'];

                    echo sprintf($locale['forum_perm_access'], $prm['can_access'] ? $can : $cannot).'<br/>';
                    echo sprintf($locale['forum_perm_post'], $prm['can_post'] ? $can : $cannot).'<br/>';
                    echo sprintf($locale['forum_perm_reply'], $prm['can_reply'] ? $can : $cannot).'<br/>';
                    echo !$poll ? sprintf($locale['forum_perm_create_poll'], $prm['can_create_poll'] ? $can : $cannot).'<br/>' : '';
                    echo $poll ? sprintf($locale['forum_perm_edit_poll'], $prm['can_edit_poll'] ? $can : $cannot).'<br/>' : '';
                    echo $poll ? sprintf($locale['forum_perm_vote_poll'], $prm['can_vote_poll'] ? $can : $cannot).'<br/>' : '';
                    echo sprintf($locale['forum_perm_upload'], $prm['can_upload_attach'] ? $can : $cannot).'<br/>';
                    echo sprintf($locale['forum_perm_download'], $prm['can_download_attach'] ? $can : $cannot).'<br/>';
                    echo $data['forum_type'] == 4 ? sprintf($locale['forum_perm_rate'], $prm['can_rate'] ? $can : $cannot).'<br/>' : '';
                    echo $data['forum_type'] == 4 ? sprintf($locale['forum_perm_bounty'], $prm['can_start_bounty'] ? $can : $cannot) : '';
                echo '</div>';

                if ($info['forum_moderators']) {
                    echo '<div class="block m-b-10">'.$locale['forum_0185'].' '.$info['forum_moderators'].'</div>';
                }
            echo '</div>';

            echo '<div class="col-xs-12 col-sm-2 col-md-2 col-lg-3">';
                echo '<a href="'.FORUM.'newthread.php" class="btn btn-success btn-block m-b-20">'.$locale['forum_0057'].'</a>';
                Main::tags();

                if (!empty($info['thread_users'])) {
                    echo '<div class="panel panel-default">';
                        echo '<div class="panel-body"><b>'.$locale['forum_0581'].'</b> ';
                            foreach ($info['thread_users'] as $user_id => $user) {
                                echo '<a href="'.BASEDIR.'profile.php?lookup='.$user_id.'">'.$user['user_name'].', </a>';
                            }
                        echo '</div>';
                    echo '</div>';
                }

            echo '</div>';
        echo '</div>';
    }
}
