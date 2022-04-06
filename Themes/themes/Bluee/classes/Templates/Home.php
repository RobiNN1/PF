<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Home.php
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

use Bluee\Core;
use Bluee\Main;

class Home extends Core {
    public static function displayHome($info) {
        Main::hideAll(0);
        self::setParam('section', FALSE);
        self::setParam('row', FALSE);

        // Push News to top
        if (defined('NEWS_EXISTS') && !empty($info[DB_NEWS])) {
            $temp = [DB_NEWS => $info[DB_NEWS]];
            unset($info[DB_NEWS]);
            $info = $temp + $info;
        }

        foreach ($info as $key => $module) {
            if (!empty($module['data'])) {
                foreach ($module['data'] as $item_key => $item) {
                    $info[$key]['data'][$item_key]['content'] = trim_text(strip_tags($item['content']), 150);
                }
            }
        }

        $context = [
            'info' => $info
        ];

        echo fusion_render(THEME.'twig', 'home.twig', $context);
    }
}
