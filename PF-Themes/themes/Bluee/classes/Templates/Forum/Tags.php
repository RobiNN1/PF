<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Tags.php
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

class Tags {
    public static function displayForumTags($info) {
        \Bluee\Main::hideAll(0);

        if (get('tag_id')) {
            if (!empty($info['threads'])) {
                $info['threads']['items'] = '';

                if (!empty($info['threads']['sticky'])) {
                    foreach ($info['threads']['sticky'] as $cdata) {
                        $info['threads']['items'] .= Main::renderThreadItem($cdata);
                    }
                }

                if (!empty($info['threads']['item'])) {
                    foreach ($info['threads']['item'] as $cdata) {
                        $info['threads']['items'] .= Main::renderThreadItem($cdata);
                    }
                }
            }
        } else {
            if (!empty($info['tags'])) {
                unset($info['tags'][0]);

                foreach ($info['tags'] as $tag_id => $tag_data) {
                    if (!empty($tag_data['threads'])) {
                        $info['tags'][$tag_id]['date'] = timer($tag_data['threads']['thread_lastpost']);
                    }
                }
            }
        }

        $context = [
            'locale' => fusion_get_locale(),
            'info'   => $info,
            'get'    => ['tag_id' => get('tag_id')],
            'header' => Main::header(),
            'filter' => Main::forumFilter($info),
            'links'  => Main::links(),
            'tags'   => Main::tags()
        ];

        echo fusion_render(THEME.'twig/forum', 'tags.twig', $context);
    }
}
