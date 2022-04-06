<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: search_wiki_include.php
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
namespace PHPFusion\Search;

use PHPFusion\ImageRepo;

defined('IN_FUSION') || exit;

if (defined('WIKI_EXISTS')) {
    if (file_exists(WIKI.'locale/'.LOCALESET.'search/wiki.php')) {
        $locale = fusion_get_locale('', WIKI.'locale/'.LOCALESET.'search/wiki.php');
    } else {
        $locale = fusion_get_locale('', WIKI.'locale/English/search/wiki.php');
    }
    $item_count = '0 '.$locale['wiki402'].' '.$locale['522'].'<br/>';
    $date_search = (Search_Engine::get_param('datelimit') != 0 ? ' AND wiki_datestamp>='.(time() - Search_Engine::get_param('datelimit')) : '');
    $formatted_result = '';

    if (Search_Engine::get_param('stype') == 'wiki' || Search_Engine::get_param('stype') == 'all') {
        $sort_by = [
            'datestamp' => 'wiki_datestamp',
            'subject'   => 'wiki_name',
            'author'    => 'wiki_user',
        ];

        $order_by = [
            '0' => ' DESC',
            '1' => ' ASC',
        ];

        $sortby = !empty(Search_Engine::get_param('sort')) ? "ORDER BY ".$sort_by[Search_Engine::get_param('sort')].$order_by[Search_Engine::get_param('order')] : '';
        $limit = (Search_Engine::get_param('stype') != 'all' ? " LIMIT ".Search_Engine::get_param('rowstart').',10' : '');

        switch (Search_Engine::get_param('fields')) {
            case 2:
                Search_Engine::search_column('wiki_name', 'wiki');
                Search_Engine::search_column('wiki_description', 'wiki');
                Search_Engine::search_column('wiki_user', 'wiki');
                break;
            case 1:
                Search_Engine::search_column('wiki_description', 'wiki');
                Search_Engine::search_column('wiki_name', 'wiki');
                break;
            default:
                Search_Engine::search_column('wiki_name', 'wiki');
        }

        if (!empty(Search_Engine::get_param('search_param'))) {
            $query = "SELECT w.*, wc.*
                FROM ".DB_WIKI." w
                INNER JOIN ".DB_WIKI_CATS." wc ON w.wiki_cat=wc.wiki_cat_id
                ".(multilang_table('WIKI') ? "WHERE ".in_group('wc.wiki_cat_language', LANGUAGE)." AND " : "WHERE ")
                .groupaccess('wiki_access')." AND ".Search_Engine::search_conditions('wiki').$date_search;

            $result = dbquery($query, Search_Engine::get_param('search_param'));

            $rows = dbrows($result);
        } else {
            $rows = 0;
        }

        if ($rows != 0) {
            $item_count = '<a href="'.BASEDIR.'search.php?stype=wiki&stext='.Search_Engine::get_param('stext').'&'.Search_Engine::get_param('composevars').'">'.$rows.' '.($rows == 1 ? $locale['wiki401'] : $locale['wiki402']).' '.$locale['522'].'</a><br/>';

            $result = dbquery("SELECT w.*, wc.*, u.user_id, u.user_name, u.user_status, u.user_avatar, u.user_joined, u.user_level
                FROM ".DB_WIKI." w
                INNER JOIN ".DB_WIKI_CATS." wc ON w.wiki_cat=wc.wiki_cat_id
                LEFT JOIN ".DB_USERS." u ON w.wiki_user=u.user_id
                ".(multilang_table('WIKI') ? "WHERE ".in_group('wc.wiki_cat_language', LANGUAGE)." AND " : "WHERE ")."
                ".Search_Engine::search_conditions('wiki').$date_search.$sortby.$limit, Search_Engine::get_param('search_param'));

            $search_result = '';

            while ($data = dbarray($result)) {
                $text_all = $data['wiki_description'];
                $text_all = Search_Engine::search_striphtmlbbcodes($text_all);
                $text_frag = Search_Engine::search_textfrag($text_all);

                $context = '<span class="small2">'.$locale['global_070'].profile_link($data['user_id'], $data['user_name'], $data['user_status']);
                if ($text_frag != '') {
                    $context .= '<div class="quote" style="width: auto;height: auto;overflow: auto;">'.$text_frag.'</div><br/>';
                }

                $search_result .= render_search_item([
                    'item_url'         => WIKI.'documentation.php?page_id='.$data['wiki_id'].'&sref=search',
                    'item_image'       => '<i class="fa fa-wikipedia-w fa-lg"></i>',
                    'item_title'       => $data['wiki_name'],
                    'item_description' => $context
                ]);
            }

            // Pass strings for theme developers
            $formatted_result = render_search_item_wrapper([
                'image'          => '<img src="'.ImageRepo::getimage('ac_WIKI').'" alt="'.$locale['wiki400'].'" style="width:32px;"/>',
                'icon_class'     => 'fa fa-wikipedia-w fa-lg fa-fw',
                'search_title'   => $locale['wiki400'],
                'search_result'  => $item_count,
                'search_content' => $search_result
            ]);
        }

        Search_Engine::search_navigation($rows);
        Search_Engine::search_globalarray($formatted_result);
        Search_Engine::append_item_count($item_count);
    }
}
