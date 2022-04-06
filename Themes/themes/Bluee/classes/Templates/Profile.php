<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Profile.php
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
namespace Bluee\Templates;

use Bluee\Main;

class Profile {
    public static function displayProfile($info) {
        global $userFields;

        Main::hideAll(0);

        $locale = fusion_get_locale('', BLUEE_LOCALE);
        $userdata = fusion_get_userdata();
        $user_data = $userFields->getUserData();

        if ($userdata['user_id'] == $_GET['lookup']) {
            self::changeAvatar();
        }

        if (!empty($info['user_field'])) {
            foreach ($info['user_field'] as $category_data) {
                if (!empty($category_data['fields'])) {
                    foreach ($category_data['fields'] as $field_data) {
                        if (!empty($field_data['type']) && $field_data['type'] == 'social') {
                            $info['profile_social_networks'][] = $field_data;
                        }
                    }
                }
            }
        }

        if (!empty($info['section'])) {
            $tab_title = [];

            foreach ($info['section'] as $page_section) {
                $tab_title['title'][$page_section['id']] = $page_section['name'];
                $tab_title['id'][$page_section['id']] = $page_section['id'];
                $tab_title['icon'][$page_section['id']] = $page_section['icon'];
            }

            $tab_active = tab_active($tab_title, $_GET['section']);

            $info['sections']['opentab'] = opentab($tab_title, $_GET['section'], 'profile_tab', TRUE, '', 'section', ['section']);
            $info['sections']['closetab'] = closetab();
            $info['sections']['opentabbody'] = opentabbody($tab_title['title'][$_GET['section']], $tab_title['id'][$_GET['section']], $tab_active, TRUE);
            $info['sections']['closetabbody'] = closetabbody();
            $info['sections']['first_section'] = $tab_title['id'][$_GET['section']] == $tab_title['id'][1];

            $info['profile_user_avatar'] = display_avatar($user_data, '115px', '', FALSE, 'img-circle');
            $info['profile_edit_avatar'] = $userdata['user_id'] == $_GET['lookup'];
            $info['profile_user_name'] = $info['core_field']['profile_user_name']['value'];
            $info['profile_user_level'] = $info['core_field']['profile_user_level']['value'];
            $info['profile_user_lastvisit'] = $user_data['user_lastvisit'] >= time() - 300;

            if ($tab_title['id'][$_GET['section']] == $tab_title['id'][1]) {
                if (!empty($info['core_field'])) {
                    foreach ($info['core_field'] as $field_id => $field_data) {
                        $user_groups = '';
                        $section_data = [];

                        switch ($field_id) {
                            case 'profile_user_avatar':
                            case 'profile_user_name':
                            case 'profile_user_level':
                            case 'profile_user_ip':
                                break;
                            case 'profile_user_group':
                                if (!empty($field_data['value']) && is_array($field_data['value'])) {
                                    $i = 0;
                                    foreach ($field_data['value'] as $group) {
                                        $user_groups .= $i > 0 ? ', ' : '';
                                        $user_groups .= '<a href="'.$group['group_url'].'">'.$group['group_name'].'</a>';
                                        $i++;
                                    }
                                }
                                break;
                            default:
                                if (!empty($field_data['value'])) {
                                    $section_data = $field_data;
                                }
                        }

                        $info['user_groups'] = $user_groups;
                        $info['core_field'][$field_id] = $section_data;
                    }
                }
            }
        }

        $context = [
            'locale'   => $locale,
            'info'     => $info,
            'timeline' => self::timeline($user_data)
        ];

        echo fusion_render(THEME.'twig/profile', 'profile.twig', $context);
    }

    private static function timeline($user_data) {
        $locale = fusion_get_locale('', BLUEE_LOCALE);

        $max_items = 40;
        $limit = $max_items / 2;
        $timeline = [];
        $_timeline = [];

        $comments = dbquery("SELECT c.*, u.user_id, u.user_name, u.user_status, u.user_avatar
            FROM ".DB_COMMENTS." c
            LEFT JOIN ".DB_USERS." u ON u.user_id=".$user_data['user_id']."
            WHERE c.comment_hidden=0 AND (c.comment_type = 'N' OR c.comment_type = 'D')
            ORDER BY c.comment_datestamp DESC
            LIMIT ".(int)$limit."
        ");

        while ($data = dbarray($comments)) {
            $comments_per_page = fusion_get_settings('comments_per_page');
            $c_data = [];

            switch ($data['comment_type']) {
                case 'N':
                    $result_n = dbquery("SELECT n.news_subject FROM ".DB_NEWS." AS n LEFT JOIN ".DB_NEWS_CATS." AS nc ON nc.news_cat_id=n.news_cat
                        WHERE n.news_id=:id AND (n.news_start=0 OR n.news_start<='".time()."') AND (n.news_end=0 OR n.news_end>='".time()."') AND n.news_draft=0 AND ".groupaccess('n.news_visibility')." ".(multilang_table('NS') ? "AND n.news_language='".LANGUAGE."'" : '')."
                        ORDER BY n.news_datestamp DESC
                    ", [':id' => $data['comment_item_id']]);

                    if (dbrows($result_n)) {
                        $news_data = dbarray($result_n);
                        $comment_start = dbcount('(comment_id)', DB_COMMENTS, "comment_item_id='".$data['comment_item_id']."' AND comment_type='N' AND comment_id<=".$data['comment_id']);
                        $comment_start = $comment_start > $comments_per_page ? '&c_start_news_comments='.((floor($comment_start / $comments_per_page) * $comments_per_page) - $comments_per_page) : '';

                        $c_data += [
                            'link'  => INFUSIONS.'news/news.php?readmore='.$data['comment_item_id'].$comment_start.'#c'.$data['comment_id'],
                            'title' => $news_data['news_subject']
                        ];
                    }
                    break;
                case 'D':
                    $result_d = dbquery("SELECT d.download_title FROM ".DB_DOWNLOADS." AS d INNER JOIN ".DB_DOWNLOAD_CATS." AS dc ON dc.download_cat_id=d.download_cat
                        WHERE d.download_id=:id AND ".groupaccess('d.download_visibility')." ".(multilang_table('DL') ? " AND dc.download_cat_language='".LANGUAGE."'" : '')."
                        ORDER BY d.download_datestamp DESC
                    ", [':id' => $data['comment_item_id']]);

                    if (dbrows($result_d)) {
                        $download_data = dbarray($result_d);
                        $comment_start = dbcount('(comment_id)', DB_COMMENTS, "comment_item_id='".$data['comment_item_id']."' AND comment_type='D' AND comment_id<=".$data['comment_id']);
                        $comment_start = $comment_start > $comments_per_page ? '&c_start_news_comments='.((floor($comment_start / $comments_per_page) * $comments_per_page) - $comments_per_page) : '';

                        $c_data += [
                            'link'  => INFUSIONS.'downloads/downloads.php?download_id='.$data['comment_item_id'].$comment_start.'#c'.$data['comment_id'],
                            'title' => $download_data['download_title']
                        ];
                    }
                    break;
            }

            $timeline[$data['comment_datestamp']] = [
                'type'  => $locale['bluee_001'],
                'link'  => !empty($c_data['link']) ? $c_data['link'] : '',
                'title' => !empty($c_data['title']) ? $c_data['title'] : '',
                'time'  => $data['comment_datestamp']
            ];
        }

        if (db_exists(DB_PREFIX.'forums')) {
            $forum_activities = dbquery("SELECT p.*, t.thread_subject, t.thread_author
                FROM ".DB_FORUM_POSTS." p
                LEFT JOIN ".DB_FORUM_THREADS." t ON t.thread_id = p.thread_id
                LEFT JOIN ".DB_FORUMS." f ON f.forum_id = p.forum_id
                WHERE p.post_author='".$user_data['user_id']."' AND ".groupaccess('forum_access')."
                ORDER BY p.post_id DESC, p.post_datestamp DESC
                LIMIT ".(int)$limit."
            ");

            while ($data = dbarray($forum_activities)) {
                $first_post = dbarraynum(dbquery("SELECT MIN(post_id) FROM ".DB_FORUM_POSTS." WHERE thread_id='".intval($data['thread_id'])."' AND post_hidden='0' GROUP BY thread_id"));
                $first_post = $data['thread_author'] == $user_data['user_id'] && $first_post[0] == $data['post_id'];
                $timeline[$data['post_datestamp']] = [
                    'type'  => ($first_post ? $locale['bluee_002'] : $locale['bluee_003']),
                    'link'  => FORUM.'viewthread.php?thread_id='.$data['thread_id'].'&pid='.$data['post_id'].'#post_'.$data['post_id'],
                    'title' => $data['thread_subject'].' #'.$data['post_id'],
                    'time'  => $data['post_datestamp']
                ];
            }
        }

        krsort($timeline);

        foreach ($timeline as $timestamp => $item) {
            $format = self::timelineFormat($timestamp);
            $_timeline[$format][] = $item;
        }

        $context = [
            'locale'    => $locale,
            'timeline'  => $_timeline,
            'user_data' => $user_data
        ];

        return fusion_render(THEME.'twig/profile', 'timeline.twig', $context);
    }

    private static function changeAvatar() {
        $locale = fusion_get_locale('', BLUEE_LOCALE);
        $userdata = fusion_get_userdata();
        $settings = fusion_get_settings();

        $img_path = IMAGES.'avatars/';
        $modal = openmodal('change-photo', $locale['bluee_004'], ['button_id' => 'change-photo']);
        $modal .= openform('change-photo', 'post', FUSION_REQUEST, ['enctype' => TRUE]);

        if (!empty($userdata['user_avatar']) && $userdata['user_avatar'] != '') {
            if (isset($_POST['delete_avatar'])) {
                if ($userdata['user_avatar'] != ''
                    && file_exists($img_path.$userdata['user_avatar'])
                    && is_file($img_path.$userdata['user_avatar'])) {
                    unlink($img_path.$userdata['user_avatar']);
                }

                if (\defender::safe()) {
                    $db = ['user_id' => $userdata['user_id'], 'user_avatar' => ''];
                    dbquery_insert(DB_USERS, $db, 'update');
                    addnotice('success', $locale['bluee_005']);
                    redirect(FUSION_REQUEST);
                }
            }

            $modal .= display_avatar($userdata, '100px', '', FALSE, 'img-thumbnail');
            $modal .= form_checkbox('delete_avatar', $locale['delete'], '', ['reverse_label' => TRUE]);
            $modal .= form_button('save', $locale['save'], $locale['save'], ['class' => 'btn-success', 'icon' => 'fas fa-hdd']);
        } else {
            if (isset($_POST['update'])) {
                $photo = '';

                if (isset($_FILES['user_avatar']) && $_FILES['user_avatar']['name']) {
                    if (!empty($_FILES['user_avatar']) && is_uploaded_file($_FILES['user_avatar']['tmp_name'])) {
                        $upload = (array)form_sanitizer($_FILES['user_avatar'], '', 'user_avatar');
                        if ($upload['error'] === 0)
                            $photo = $upload['image_name'];
                    }
                }

                if (\defender::safe()) {
                    $db = ['user_id' => $userdata['user_id'], 'user_avatar' => $photo];
                    dbquery_insert(DB_USERS, $db, 'update');
                    addnotice('success', $locale['bluee_006']);
                    redirect(FUSION_REQUEST);
                }
            }

            $modal .= form_fileinput('user_avatar', '', '', [
                'upload_path'     => $img_path,
                'type'            => 'image',
                'max_byte'        => $settings['avatar_filesize'],
                'max_height'      => $settings['avatar_width'],
                'max_width'       => $settings['avatar_height'],
                'thumbnail'       => 0,
                'width'           => '100%',
                'delete_original' => FALSE,
                'class'           => 'm-t-10 m-b-0',
                'error_text'      => $locale['u180'],
                'ext_tip'         => sprintf($locale['u184'], parsebytesize($settings['avatar_filesize']), $settings['avatar_width'], $settings['avatar_height'])
            ]);

            $modal .= form_button('update', $locale['save'], $locale['save'], ['class' => 'btn-success m-t-20', 'icon' => 'fas fa-hdd']);
        }

        $modal .= closeform();
        $modal .= closemodal();

        add_to_footer($modal);
    }

    private static function timelineFormat($time) {
        $locale = fusion_get_locale('', BLUEE_LOCALE);

        if ($time >= strtotime('today 00:00')) {
            return date('G:i', $time);
        } else if ($time >= strtotime('yesterday 00:00')) {
            return $locale['bluee_007'];
        } else if ($time >= strtotime('-6 day 00:00')) {
            return $locale['bluee_008'];
        } else if ($time >= strtotime('-13 day 00:00')) {
            return $locale['bluee_009'];
        } else if ($time >= strtotime('-20 day 00:00')) {
            return $locale['bluee_010'];
        } else if ($time >= strtotime('-29 day 00:00')) {
            return $locale['bluee_011'];
        } else {
            return date('M j, Y', $time);
        }
    }
}
