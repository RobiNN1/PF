<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: OpenGraphWiki.php
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

class OpenGraphWiki extends \PHPFusion\OpenGraph {
    public static function ogWiki($wiki_id = 0) {
        $settings = fusion_get_settings();
        $info = [];

        $result = dbquery("SELECT wiki_id, wiki_name, wiki_description FROM ".DB_WIKI." WHERE wiki_id = :wiki_id", [':wiki_id' => $wiki_id]);

        if (dbrows($result)) {
            $data = dbarray($result);
            $info['title'] = $data['wiki_name'].' - '.$settings['sitename'];
            $info['description'] = !empty($data['wiki_description']) ? fusion_first_words(strip_tags(html_entity_decode($data['wiki_description'])), 50) : $settings['description'];
            $info['url'] = $settings['siteurl'].'infusions/wiki/documentation.php?page_id='.$wiki_id;
            $info['keywords'] = $settings['keywords'];
            $info['type'] = 'article';
        }

        self::setValues($info);
    }

    public static function ogWikiCat($cat_id = 0) {
        $settings = fusion_get_settings();
        $info = [];

        $result = dbquery("SELECT wiki_cat_id, wiki_cat_name, wiki_cat_description FROM ".DB_WIKI_CATS." WHERE wiki_cat_id = :wiki_cat_id", [':wiki_cat_id' => $cat_id]);

        if (dbrows($result)) {
            $data = dbarray($result);
            $info['title'] = $data['wiki_cat_name'].' - '.$settings['sitename'];
            $info['description'] = $settings['description'];
            $info['url'] = $settings['siteurl'].'infusions/wiki/documentation.php?page_id='.$cat_id;
            $info['keywords'] = $settings['keywords'];
        }

        self::setValues($info);
    }
}
