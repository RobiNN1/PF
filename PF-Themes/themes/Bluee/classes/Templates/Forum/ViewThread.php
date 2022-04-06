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
namespace Bluee\Templates\Forum;

use PHPFusion\Forums\ForumServer;

class ViewThread {
    public static function renderThread($info) {
        \Bluee\Main::hideAll(0);

        $locale = fusion_get_locale('', BLUEE_LOCALE);

        $posts = NULL;
        if (!empty($info['post_items'])) {
            $i = get('sort_post') == 'latest' ? count($info['post_items']) : 1;
            foreach ($info['post_items'] as $post_data) {
                $post_data['thread_author'] = $info['thread']['thread_author'];
                $posts .= self::renderPostItem($post_data, $i + (!empty($_GET['rowstart']) ? $_GET['rowstart'] : 0));

                if (get('sort_post') == 'latest') {
                    $i--;
                } else {
                    $i++;
                }
            }
        }

        $info['post_filters'] = $info['post-filters'];

        $selector = [
            'oldest' => $locale['forum_0180'],
            'latest' => $locale['forum_0181'],
            'high'   => $locale['forum_0182']
        ];

        $info['selector'] = isset($_GET['sort_post']) && in_array($_GET['sort_post'], array_flip($selector)) ? $selector[$_GET['sort_post']] : $locale['forum_0180'];

        $info['postcount'] = format_word($info['thread']['thread_postcount'], $locale['fmt_post']);
        $info['threadviews'] = format_word($info['thread']['thread_views'], $locale['fmt_views']);
        $info['threadlastpost'] = timer($info['thread']['thread_lastpost']);

        if (!empty($info['thread_tags_display'])) {
            $tags = Main::cacheTags();
            $tags_ = explode('.', $info['thread_tags']);
            $info['threadtags'] = '';

            foreach ($tags_ as $tag_id) {
                if (isset($tags[$tag_id])) {
                    $tag_data = $tags[$tag_id];
                    $icon = !empty($tag_data['tag_icon']) ? '<i class="'.$tag_data['tag_icon'].'"></i> ' : '';
                    $info['threadtags'] .= '<a href="'.$tag_data['tag_link'].'" class="text-white badge tag" style="background-color: '.$tag_data['tag_color'].';">'.$icon.$tag_data['tag_title'].'</a>';
                }
            }
        }

        $context = [
            'locale'       => $locale,
            'info'         => $info,
            'header'       => Main::header(FALSE),
            'breadcrumbs'  => render_breadcrumbs(),
            'links'        => Main::links(),
            'tags'         => Main::tags(),
            'participated' => self::participated($info),
            'posts'        => $posts
        ];

        echo fusion_render(THEME.'twig/forum/thread', 'render_thread.twig', $context);
    }

    private static function renderPostItem($data, $i) {
        $locale = fusion_get_locale();

        $data['useravatar'] = display_avatar($data, '50px', FALSE, FALSE, 'img-circle avatar');
        $data['userlevel'] = getuserlevel($data['user_level']);

        if ($data['post_votebox']) {
            $data['votebox'] = strtr($data['post_votebox'].$data['post_answer_check'], [
                'fa fa-caret-up'   => 'icon fas fa-thumbs-up',
                'fa fa-caret-down' => 'icon fas fa-thumbs-down',
                'fa fa-check'      => 'icon fas fa-check'
            ]);
        }

        if ($data['user_id'] != 1) {
            if (iSUPERADMIN || (iADMIN && checkrights('M'))) {
                $data['admin_link'] = [
                    'edit'   => ADMIN.'members.php'.fusion_get_aidlink().'&ref=edit&lookup='.$data['user_id'],
                    'delete' => ADMIN.'members.php'.fusion_get_aidlink().'&ref=delete&lookup='.$data['user_id']
                ];
            }
        }

        if (column_exists('users', 'user_facebook')) {
            $data['user_facebook']['link'] = fusion_get_user($data['user_id'], 'user_facebook');
        }

        if (column_exists('users', 'user_github')) {
            $data['user_github']['link'] = fusion_get_user($data['user_id'], 'user_github');
        }

        if (!empty($data['post_moods'])) {
            foreach ($data['post_moods'] as $key => $mdata) {
                if (!empty($mdata['users'])) {
                    $data['post_moods'][$key]['users_list'] = implode(', ', array_map(function ($user) {
                        return $user['profile_link'];
                    }, $mdata['users']));
                }
            }

            $data['post_moods']['count'] = format_word($data['post_moods']['users_count'], $locale['fmt_user']);
        }

        $context = [
            'locale'         => $locale,
            'forum_settings' => ForumServer::getForumSettings(),
            'data'           => $data,
            'post_i'         => $i
        ];

        return fusion_render(THEME.'twig/forum/thread', 'render_post_item.twig', $context);
    }

    private static function participated($info) {
        if (!empty($info['thread_users'])) {
            foreach ($info['thread_users'] as $user_id => $user) {
                $info['thread_users'][$user_id]['user_id'] = $user_id;
                $info['thread_users'][$user_id]['avatar'] = display_avatar($user, '20px', '', FALSE, 'pull-left m-r-5');
            }

            $context = [
                'locale' => fusion_get_locale('', BLUEE_LOCALE),
                'users'  => $info['thread_users']
            ];

            return fusion_render(THEME.'twig/forum/panels', 'participated.twig', $context);
        }

        return NULL;
    }
}
