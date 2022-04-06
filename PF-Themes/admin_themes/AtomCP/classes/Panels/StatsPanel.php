<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: StatsPanel.php
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
namespace AtomCP\Panels;

class StatsPanel {
    public $key;
    public $title;
    public $check;

    public function __construct() {
        $locale = fusion_get_locale();
        $theme_settings = atomcp_settings();
        $this->key = 'infstats';
        $this->title = $locale['cp_207'];
        $this->check = !empty($theme_settings['infstats']);
    }

    public function install() {
        dbquery("INSERT INTO ".DB_SETTINGS_THEME." (settings_name, settings_value, settings_theme) VALUES ('infstats', '1', 'AtomCP')");
    }

    public function uninstall() {
        dbquery("DELETE FROM ".DB_SETTINGS_THEME." WHERE settings_theme='AtomCP' AND settings_name='infstats'");
    }

    public function sidePanel() {
        global $forum, $download, $news, $articles, $weblinks, $photos;

        $locale = fusion_get_locale();

        if ($this->check) {
            $modules = [];

            if (defined('FORUM_EXISTS')) {
                $modules['forum'] = [
                    'title' => $locale['265'],
                    'icon' => 'fa fa-comments',
                    'stats' => [
                        ['title' => $locale['265'], 'count' => $forum['count']],
                        ['title' => $locale['256'], 'count' => $forum['thread']],
                        ['title' => $locale['259'], 'count' => $forum['post']],
                        ['title' => $locale['260'], 'count' => $forum['users']]
                    ]
                ];
            }

            if (defined('DOWNLOADS_EXISTS')) {
                $modules['downloads'] = [
                    'title' => $locale['268'],
                    'icon' => 'fa fa-cloud-download',
                    'stats' => [
                        ['title' => $locale['268'], 'count' => $download['download']],
                        ['title' => $locale['257'], 'count' => $download['comment']],
                        ['title' => $locale['254'], 'count' => $download['submit']]
                    ]
                ];
            }

            if (defined('NEWS_EXISTS')) {
                $modules['news'] = [
                    'title' => $locale['269'],
                    'icon' => 'fa fa-newspaper-o',
                    'stats' => [
                        ['title' => $locale['269'], 'count' => $news['news']],
                        ['title' => $locale['257'], 'count' => $news['comment']],
                        ['title' => $locale['254'], 'count' => $news['submit']]
                    ]
                ];
            }

            if (defined('ARTICLES_EXISTS')) {
                $modules['articles'] = [
                    'title' => $locale['270'],
                    'icon' => 'fa fa-book',
                    'stats' => [
                        ['title' => $locale['270'], 'count' => $articles['article']],
                        ['title' => $locale['257'], 'count' => $articles['comment']],
                        ['title' => $locale['254'], 'count' => $articles['submit']]
                    ]
                ];
            }

            if (defined('WEBLINKS_EXISTS')) {
                $modules['weblinks'] = [
                    'title' => $locale['271'],
                    'icon' => 'fa fa-link',
                    'stats' => [
                        ['title' => $locale['271'], 'count' => $weblinks['weblink']],
                        ['title' => $locale['254'], 'count' => $weblinks['submit']]
                    ]
                ];
            }

            if (defined('GALLERY_EXISTS')) {
                $modules['gallery'] = [
                    'title' => $locale['272'],
                    'icon' => 'fa fa-camera-retro',
                    'stats' => [
                        ['title' => $locale['261'], 'count' => $photos['photo']],
                        ['title' => $locale['257'], 'count' => $photos['comment']],
                        ['title' => $locale['254'], 'count' => $photos['submit']]
                    ]
                ];
            }

            if (!empty($modules)) {
                echo '<div class="info-boxes m-b-20">';

                foreach ($modules as $module) {
                    echo '<div class="info-box clearfix p-l-10 p-r-10">';
                        echo '<h5><strong class="display-block"><i class="'.$module['icon'].'"></i> '.$module['title'].' '.$locale['258'].'</strong></h5>';
                        echo '<div>';
                            if (!empty($module['stats'])) {
                                foreach ($module['stats'] as $stat) {
                                    echo '<div class="pull-left display-inline-block m-r-5">';
                                        echo '<span class="text-smaller">'.$stat['title'].'</span><br/>';
                                        echo '<h4 class="m-t-0">'.number_format($stat['count']).'</h4>';
                                    echo '</div>';
                                }
                            }
                        echo '</div>';
                    echo '</div>';
                }

                echo '</div>';
            }
        }
    }
}
