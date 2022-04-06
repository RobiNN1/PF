<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Downloads.php
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

class Downloads {
    public static function renderDownloads($info) {
        $locale = fusion_get_locale();

        Main::hideAll(0);

        $html = '<h1 class="main-title">'.$locale['download_1000'].'</h1>';

        $html .= '<ul class="nav p-t-20 p-b-20">';
            $download_cat_menu = self::displayCatMenu($info['download_categories']);

            if (!empty($download_cat_menu)) {
                $html .= '<li class="nav-item dropdown">';
                    $html .= '<a id="ddcats" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">'.$locale['download_1003'].' <span class="caret"></span></a>';
                    $html .= '<ul class="dropdown-menu" aria-labelledby="ddcats">'.$download_cat_menu.'</ul>';
                $html .= '</li>';
            }

            foreach ($info['download_filter'] as $key => $filter) {
                $active = isset($_GET['type']) && $_GET['type'] === $key ? ' active' : '';
                $html .= '<li class="nav-item"><a class="nav-link'.$active.'" href="'.$filter['link'].'">'.$filter['title'].'</a></li>';
            }
        $html .= '</ul>';

        if ($info['get']['download_id'] && !empty($info['download_item'])) {
            $html .= self::displayDownloadItem($info);
        } else {
            $html .= self::displayDownloadIndex($info);
        }

        return $html;
    }

    private static function displayDownloadIndex($info) {
        $locale = fusion_get_locale();
        $dl_settings = get_settings('downloads');

        if (!empty($info['download_item'])) {
            foreach ($info['download_item'] as $download_id => $data) {
                if ($dl_settings['download_screenshot'] == 1) {
                    if (!empty($data['download_image_thumb']) && is_file(IMAGES_D.$data['download_image_thumb'])) {
                        $img = IMAGES_D.$data['download_image_thumb'];
                    } else if (!empty($data['download_thumb']) && is_file(IMAGES_D.$data['download_thumb'])) {
                        $img = IMAGES_D.$data['download_thumb'];
                    } else {
                        $img = THEME.'assets/img/noimage.svg';
                    }

                    $info['download_item'][$download_id]['img'] = $img;
                }
            }
        }

        $context = [
            'locale'      => $locale,
            'dl_settings' => $dl_settings,
            'info'        => $info,
        ];

        return fusion_render(THEME.'twig/downloads', 'index.twig', $context);
    }

    private static function displayDownloadItem($info) {
        $locale = fusion_get_locale();
        $dl_settings = get_settings('downloads');

        $context = [
            'locale'      => $locale,
            'dl_settings' => $dl_settings,
            'data'        => $info['download_item'],
            'ifimage'     => ($info['download_item']['download_image'] && file_exists(DOWNLOADS.'images/'.$info['download_item']['download_image'])),
            'image'       => DOWNLOADS.'images/'.$info['download_item']['download_image']
        ];

        return fusion_render(THEME.'twig/downloads', 'item.twig', $context);
    }

    private static function displayCatMenu($info, $cat_id = 0, $level = 0) {
        $html = '';

        if (!empty($info[$cat_id])) {
            foreach ($info[$cat_id] as $download_cat_id => $cdata) {
                $active = !empty($_GET['cat_id']) && $_GET['cat_id'] == $download_cat_id;
                $active = $active ? ' active' : '';
                $html .= str_repeat('&nbsp;', $level);
                $html .= '<li class="nav-item"><a class="nav-link'.$active.'" href="'.DOWNLOADS.'downloads.php?cat_id='.$download_cat_id.'">'.$cdata['download_cat_name'].'</a></li>';

                if (!empty($info[$download_cat_id])) {
                    $html .= self::displayCatMenu($info, $download_cat_id, $level + 1);
                }
            }
        }

        return $html;
    }
}
