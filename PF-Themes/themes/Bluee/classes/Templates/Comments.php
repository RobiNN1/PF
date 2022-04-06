<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Comments.php
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

class Comments {
    public static function displayCommentsUi($info) {
        $context = [
            'info' => $info
        ];

        return fusion_render(THEME.'twig/comments', 'ui.twig', $context);
    }

    public static function displayCommentsList($info) {
        $context = [
            'locale'         => fusion_get_locale(),
            'info'           => $info,
            'enabled_avatar' => fusion_get_settings('comments_avatar')
        ];

        return fusion_render(THEME.'twig/comments', 'list.twig', $context);
    }
}
