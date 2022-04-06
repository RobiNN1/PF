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
namespace Bluee\Templates\Forum;

use PHPFusion\Forums\ForumServer;

class Main {
    public static function header($breadcrumb = TRUE) {
        define('THEME_BODY', '<body class="forummain">');

        $html = '<h1 class="main-title">'.fusion_get_locale('forum_0000').'</h1>';

        if ($breadcrumb == TRUE) {
            $html .= render_breadcrumbs();
        }

        return $html;
    }

    public static function renderForum($info) {
        \Bluee\Main::hideAll(0);

        if (check_get('viewforum')) {
            echo self::viewForum($info);
        } else {
            if (check_get('section')) {
                $content = self::renderSection($info);
            } else {
                $content = self::renderForumMain($info);
            }

            $context = [
                'locale'  => fusion_get_locale(),
                'header'  => self::header(),
                'links'   => self::links(),
                'tags'    => self::tags(),
                'stats'   => self::stats(),
                'content' => $content
            ];

            echo fusion_render(THEME.'twig/forum/main', 'render_forum.twig', $context);
        }
    }

    private static function renderForumMain($info) {
        $locale = fusion_get_locale();
        $forums = !empty($info['forums'][0]) ? $info['forums'][0] : NULL;

        if (!empty($forums)) {
            foreach ($forums as $forum_id => $data) {
                if ($data['forum_type'] == 1) {
                    if (isset($data['child'])) {
                        foreach ($data['child'] as $sub_forum_id => $cdata) {
                            $forums[$forum_id]['child'][$sub_forum_id]['item'] = self::renderForumItem($cdata);
                        }
                    }
                } else {
                    $forums[$forum_id]['item'] = self::renderForumItem($data);
                }
            }
        }

        $context = [
            'locale' => $locale,
            'forums' => $forums
        ];

        return fusion_render(THEME.'twig/forum/main', 'render_forum_main.twig', $context);
    }

    private static function renderForumItem($data) {
        $locale = fusion_get_locale();

        if ($data['forum_image'] && file_exists(INFUSIONS.'forum/images/'.$data['forum_image'])) {
            $data['forum_image'] = '<img src="'.FORUM.'images/'.$data['forum_image'].'" alt="Icon">';
        } else if (!empty($data['forum_icon'])) {
            $data['forum_image'] = '<i class="fa-fw '.$data['forum_icon'].'"></i>';
        } else {
            $data['forum_image'] = '<i class="'.$data['forum_icon_alt'].'"></i>';
        }

        $data['threadcount'] = format_word($data['forum_threadcount'], $locale['fmt_thread']);

        if (!empty($data['forum_lastpost'])) {
            if (!empty($data['last_post']['avatar'])) {
                $data['last_post_avatar'] = display_avatar($data, '50px', '', '', 'img-circle');
            }
        }

        $context = [
            'locale' => $locale,
            'data'   => $data
        ];

        return fusion_render(THEME.'twig/forum/main', 'render_forum_item.twig', $context);
    }

    private static function viewForum($info) {
        $locale = fusion_get_locale('', BLUEE_LOCALE);

        $i = 0;

        unset($info['forum_page_link']['subforums']);
        foreach ($info['forum_page_link'] as $view_keys => $page_link) {
            $info['forum_page_link'][$view_keys]['active'] = (!isset($_GET['view']) && !$i) || (isset($_GET['view']) && $_GET['view'] === $view_keys);
            $i++;
        }

        $content = NULL;

        if (isset($_GET['view'])) {
            switch ($_GET['view']) {
                default:
                case 'threads':
                    if ($info['forum_type'] > 1) {
                        $context = [
                            'locale'  => $locale,
                            'filter'  => self::forumFilter($info),
                            'threads' => self::renderForumThreads($info)
                        ];

                        $content = fusion_render(THEME.'twig/forum/main/view', 'threads.twig', $context);
                    }
                    break;
                case 'subforums':
                    $subforums = NULL;
                    $items = NULL;

                    if (!empty($info['item'][$_GET['forum_id']]['child'])) {
                        $subforums = $info['item'][$_GET['forum_id']]['child'];

                        foreach ($subforums as $subforum_data) {
                            $items .= self::renderForumItem($subforum_data);
                        }
                    }

                    $context = [
                        'locale'    => $locale,
                        'items'     => $items,
                        'subforums' => $subforums
                    ];

                    $content = fusion_render(THEME.'twig/forum/main/view', 'subforums.twig', $context);
                    break;
                case 'people':
                    if (!empty($info['item'])) {
                        foreach ($info['item'] as $id => $data) {
                            $info['item'][$id]['avatar'] = display_avatar($data, '30px', '', FALSE, 'rounded m-r-10');
                            $info['item'][$id]['profile_link'] = profile_link($data['user_id'], $data['user_name'], $data['user_status']);
                            $info['item'][$id]['date'] = timer($data['post_datestamp']);
                        }
                    }

                    $context = [
                        'locale' => $locale,
                        'info'   => $info
                    ];

                    $content = fusion_render(THEME.'twig/forum/main/view', 'people.twig', $context);
                    break;
                case 'activity':
                    if (!empty($info['item'])) {
                        if (!empty($info['max_post_count'])) {
                            $info['count'] = format_word($info['max_post_count'], $locale['fmt_post']);
                            $info['date'] = sprintf($locale['forum_0021'],
                                showdate('forumdate', $info['last_activity']['time']),
                                profile_link($info['last_activity']['user']['user_id'], $info['last_activity']['user']['user_name'], $info['last_activity']['user']['user_status'])
                            );
                        }

                        foreach ($info['item'] as $id => $data) {
                            $info['item'][$id]['avatar'] = display_avatar($data['post_author'], '30px', FALSE, FALSE, 'm-r-10');
                            $info['item'][$id]['profile_link'] = profile_link($data['post_author']['user_id'], $data['post_author']['user_name'], $data['post_author']['user_status']);
                            $info['item'][$id]['date'] = showdate('forumdate', $data['post_datestamp']);
                            $info['item'][$id]['date2'] = timer($data['post_datestamp']);
                            $info['item'][$id]['message'] = nl2br(parseubb($data['post_message']));
                        }
                    }

                    $context = [
                        'locale' => $locale,
                        'info'   => $info
                    ];

                    $content = fusion_render(THEME.'twig/forum/main/view', 'activity.twig', $context);
                    break;
            }
        }

        $subforums = NULL;
        if (!empty($info['subforums'])) {
            foreach ($info['subforums'] as $subforum_data) {
                $subforums .= self::renderForumItem($subforum_data);
            }
        }

        if ($info['forum_image'] && file_exists(INFUSIONS.'forum/images/'.$info['forum_image'])) {
            $info['forum_image'] = '<img src="'.FORUM.'images/'.$info['forum_image'].'" alt="Icon">';
        } else if (!empty($info['forum_icon'])) {
            $info['forum_image'] = '<i class="'.$info['forum_icon'].'"></i>';
        } else if ($info['forum_type'] == 1) {
            $info['forum_image'] = '<i class="far fa-folder-open"></i>';
        }

        $context = [
            'locale'      => $locale,
            'get'         => ['view' => get('view')],
            'info'        => $info,
            'new_link'    => iMEMBER && $info['permissions']['can_post'] && !empty($info['new_thread_link']),
            'filter'      => self::forumFilter($info),
            'threads'     => self::renderForumThreads($info),
            'content'     => $content,
            'subforums'   => $subforums,
            'header'      => self::header(FALSE),
            'breadcrumbs' => render_breadcrumbs(),
            'links'       => self::links(),
            'tags'        => self::tags(),
            'stats'       => self::stats(),
        ];

        return fusion_render(THEME.'twig/forum/main', 'view_forum.twig', $context);
    }

    private static function renderForumThreads($info) {
        $data = $info['threads'];
        $content = NULL;

        if (!empty($data)) {
            if (!empty($data['sticky'])) {
                foreach ($data['sticky'] as $cdata) {
                    $content .= self::renderThreadItem($cdata);
                }
            }

            if (!empty($data['item'])) {
                foreach ($data['item'] as $cdata) {
                    $content .= self::renderThreadItem($cdata);
                }
            }
        }

        $context = [
            'locale'  => fusion_get_locale(),
            'info'    => $info,
            'data'    => $data,
            'content' => $content
        ];

        return fusion_render(THEME.'twig/forum/main', 'render_forum_threads.twig', $context);
    }

    public static function renderThreadItem($info) {
        $locale = fusion_get_locale();

        $icons = [
            'lock'   => '<span class="ticon lock" title="'.$locale['forum_0263'].'"><i class="fa-fw fas fa-lock-alt"></i></span>',
            'sticky' => '<span class="ticon sticky" title="'.$locale['forum_0103'].'"><i class="fa-fw fas fa-thumbtack"></i></span>',
            'poll'   => '<span class="ticon poll" title="'.$locale['forum_0314'].'"><i class="fa-fw fas fa-chart-pie"></i></span>',
            'hot'    => '<span class="ticon hot" title="'.$locale['forum_0311'].'"><i class="fa-fw fas fa-fire-alt"></i></span>',
            'reads'  => '<span class="ticon reads" title="'.$locale['forum_0311'].'"><i class="fa-fw fas fa-ticket-alt"></i></span>',
            'attach' => '<span class="ticon attach" title="'.$locale['forum_0312'].'"><i class="fa-fw fas fa-image"></i></span>',
            'icon'   => '<span class="ticon icon" title="'.$locale['forum_0260'].'"><i class="fa-fw fas fa-comment-alt"></i></span>'
        ];

        $thead_icons = '';
        foreach ($info['thread_icons'] as $key => $data) {
            if (!empty($data)) {
                foreach ($icons as $i_key => $i_data) {
                    if ($key === $i_key) {
                        $thead_icons .= $i_data;
                    }
                }
            }
        }

        $threadtags = NULL;

        if (!empty($info['thread_tags'])) {
            $tags = self::cacheTags();
            $tags_ = explode('.', $info['thread_tags']);

            foreach ($tags_ as $tag_id) {
                if (isset($tags[$tag_id])) {
                    $tag_data = $tags[$tag_id];
                    $color = !empty($tag_data['tag_color']) ? $tag_data['tag_color'] : '#3498db';
                    $icon = !empty($tag_data['tag_icon']) ? '<i class="'.$tag_data['tag_icon'].'"></i> ' : '';
                    $threadtags .= '<a href="'.$tag_data['tag_link'].'" class="text-white badge tag" style="background-color: '.$color.';">'.$icon.$tag_data['tag_title'].'</a>';
                }
            }
        }

        $context = [
            'locale'       => $locale,
            'info'         => $info,
            'threadtags'   => $threadtags,
            'avatar'       => display_avatar($info['thread_last']['user'], '50px', '', TRUE, 'img-circle'),
            'latest_time'  => timer($info['thread_last']['time']),
            'thead_icons'  => $thead_icons,
            'starter_text' => (!empty($info['thread_starter_text']) ? $info['thread_starter_text'] : $info['thread_starter']),
            'views'        => format_word($info['thread_views'], $locale['fmt_views']),
            'posts'        => format_word($info['thread_postcount'], $locale['fmt_post']),
            'votes'        => ($info['forum_type'] == '4') ? format_word($info['vote_count'], $locale['fmt_vote']) : ''
        ];

        return fusion_render(THEME.'twig/forum/main', 'render_thread_item.twig', $context);
    }

    private static function renderSection($info) {
        $data = $info['threads'];
        $content = NULL;

        if (!empty($data)) {
            if (!empty($data['sticky'])) {
                foreach ($data['sticky'] as $cdata) {
                    $content .= self::renderThreadItem($cdata);
                }
            }

            if (!empty($data['item'])) {
                foreach ($data['item'] as $cdata) {
                    $content .= self::renderThreadItem($cdata);
                }
            }
        }

        $context = [
            'locale'  => fusion_get_locale(),
            'info'    => $info,
            'data'    => $data,
            'content' => $content
        ];

        return fusion_render(THEME.'twig/forum/main', 'render_section.twig', $context);
    }

    public static function forumFilter($info) {
        $locale = fusion_get_locale();

        if (isset($_GET['tag_id']) && isnum($_GET['tag_id']) || isset($_GET['forum_id']) && isnum($_GET['forum_id'])) {
            $selector = [
                'today'  => $locale['forum_0212'],
                '2days'  => $locale['forum_p002'],
                '1week'  => $locale['forum_p007'],
                '2week'  => $locale['forum_p014'],
                '1month' => $locale['forum_p030'],
                '2month' => $locale['forum_p060'],
                '3month' => $locale['forum_p090'],
                '6month' => $locale['forum_p180'],
                '1year'  => $locale['forum_3015']
            ];

            $selector2 = [
                'all'         => $locale['forum_0374'],
                'discussions' => $locale['forum_0222'],
                'attachments' => $locale['forum_0223'],
                'poll'        => $locale['forum_0314'],
                'solved'      => $locale['forum_0378'],
                'unsolved'    => $locale['forum_0379']
            ];

            $selector3 = [
                'author'  => $locale['forum_0052'],
                'time'    => $locale['forum_0381'],
                'subject' => $locale['forum_0051'],
                'reply'   => $locale['forum_0054'],
                'view'    => $locale['forum_0053']
            ];

            $selector4 = [
                'descending' => $locale['forum_0230'],
                'ascending'  => $locale['forum_0231']
            ];

            $context = [
                'locale'    => $locale,
                'filter'    => $info['filter'],
                'selector'  => (isset($_GET['time']) && in_array($_GET['time'], array_flip($selector)) ? $selector[$_GET['time']] : $locale['forum_0211']),
                'selector2' => (isset($_GET['type']) && in_array($_GET['type'], array_flip($selector2)) ? $selector2[$_GET['type']] : $locale['forum_0390']),
                'selector3' => (isset($_GET['sort']) && in_array($_GET['sort'], array_flip($selector3)) ? $selector3[$_GET['sort']] : $locale['forum_0381']),
                'selector4' => (isset($_GET['order']) && in_array($_GET['order'], array_flip($selector4)) ? $selector4[$_GET['order']] : $locale['forum_0230'])
            ];

            return fusion_render(THEME.'twig/forum/main', 'filter.twig', $context);
        }

        return NULL;
    }

    public static function renderPostify($info) {
        \Bluee\Main::hideAll(0);

        $context = [
            'locale' => fusion_get_locale('', BLUEE_LOCALE),
            'info'   => $info
        ];

        echo fusion_render(THEME.'twig/forum/main', 'postify.twig', $context);
    }

    public static function links() {
        $context = [
            'locale'   => fusion_get_locale('', BLUEE_LOCALE),
            'get'      => ['section' => get('section')],
            'forum_id' => (isset($_GET['forum_id']) ? '?forum_id='.$_GET['forum_id'] : '')
        ];

        return fusion_render(THEME.'twig/forum/panels', 'links.twig', $context);
    }

    public static function tags() {
        $tags = self::cacheTags();

        if (!empty($tags)) {
            $context = [
                'locale' => fusion_get_locale('', BLUEE_LOCALE),
                'tags'   => $tags
            ];

            return fusion_render(THEME.'twig/forum/panels', 'tags.twig', $context);
        }

        return NULL;
    }

    public static function stats() {
        $context = [
            'locale'  => fusion_get_locale('', BLUEE_LOCALE),
            'threads' => dbcount('(thread_id)', DB_FORUM_THREADS),
            'posts'   => dbcount('(post_id)', DB_FORUM_POSTS)
        ];

        return fusion_render(THEME.'twig/forum/panels', 'stats.twig', $context);
    }

    public static function cacheTags() {
        $tags = ForumServer::tag(FALSE)->cacheTags('tag_id ASC');
        if (!empty($tags)) {
            $tags = $tags->tag_info['tags'];

            krsort($tags);

            $tags_add = [
                0 => ['tag_color' => '#263238']
            ];

            foreach ($tags as $id => $data) {
                foreach ($tags_add as $i_id => $i_data) {
                    if ($id === $i_id) {
                        if (!empty($i_data['tag_color'])) {
                            $tags[$id]['tag_color'] = $i_data['tag_color'];
                        }

                        $tags[$id] += $i_data;
                    }
                }
            }
        }

        return $tags;
    }
}
