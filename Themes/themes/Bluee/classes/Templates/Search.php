<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Search.php
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

class Search {
    public static function renderSearch($info) {
        add_to_jquery('$("#advanced_options").click(function(e) {
            e.preventDefault();
            $(".advnaced-options").toggle();
        });');

        $context = [
            'locale' => fusion_get_locale(),
            'info'   => $info
        ];

        return fusion_render(THEME.'twig/search', 'search.twig', $context);
    }

    public static function renderSearchItemWrapper($info) {
        $context = [
            'info' => $info
        ];

        return fusion_render(THEME.'twig/search', 'item_wrapper.twig', $context);
    }

    public static function renderSearchItem($info) {
        $context = [
            'info' => $info
        ];

        return fusion_render(THEME.'twig/search', 'item.twig', $context);
    }

    public static function renderSearchItemList($info) {
        $context = [
            'info' => $info
        ];

        return fusion_render(THEME.'twig/search', 'item_list.twig', $context);
    }

    public static function renderSearchNoResult($info) {
        return '<div class="alert alert-warning m-t-10">'.$info['content'].'</div>';
    }
}
