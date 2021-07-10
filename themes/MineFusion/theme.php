<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: theme.php
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
defined('IN_FUSION') || exit;

const BOOTSTRAP4 = TRUE;
const FONTAWESOME = TRUE;

add_handler(function ($output = '') {
    return preg_replace_callback("/class=(['\"])[^('|\")]*/im", function ($m) {
        return strtr($m[0], [
            'btn-default'   => 'btn-secondary',
            'panel-group'   => 'panel-group',
            'panel'         => 'card',
            'panel-heading' => 'card-header',
            'panel-title'   => 'card-title',
            'panel-body'    => 'card-body',
            'panel-footer'  => 'card-footer',
            'img-rounded'   => 'rounded',
            'label'         => 'badge'
        ]);
    }, $output);
});

// Required Theme Components
function render_page() {
    $settings = fusion_get_settings();
    $locale = fusion_get_locale();

    echo '<header>';
    $menu_config = [
        'id'             => 'main-menu',
        'container'      => TRUE,
        'caret_icon'     => 'fas fa-angle-down',
        'custom_header'  => '
            <a class="navbar-brand" href="'.BASEDIR.$settings['opening_page'].'"><img height="80" src="'.BASEDIR.$settings['sitebanner'].'" alt="'.$settings['sitename'].'"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu_menu" aria-controls="main-menu_menu"><span class="navbar-toggler-icon"></span></button>
        ',
    ];
    echo \PHPFusion\SiteLinks::setSubLinks($menu_config)->showSubLinks();
    echo '</header>';

    echo '<div class="site-banner">';
        echo '<div class="bg-lefts"></div>';
        echo '<div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">

                <div class="row">
                    <div class="col-xs-12 col-sm-3 col-md-6 col-lg-6">
                        <img class="img-fluid center-y" src="'.THEME.'assets/img/img-ste1.png" alt="img1">
                    </div>

                    <div class="col-xs-12 col-sm-9 col-md-6 col-lg-6">
                        <h3 class="sitename mt-4">'.$settings['sitename'].'</h3>
                        <span class="hr-intro"></span>
                        <p class="docopation-intro">'.$settings['description'].'</p>
                    </div>
                </div>

                </div>

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <a href="'.BASEDIR.'register.php" class="register-btn"><span class="title">'.$locale['global_107'].'</span></a>
                </div>
            </div>
        </div>';
    echo '</div>';

    echo '<div class="container">';
        echo '<div class="notices">';
            echo renderNotices(getNotices(['all', FUSION_SELF]));
        echo '</div>';

        echo '<section class="main-content">';
            echo defined('AU_CENTER') && AU_CENTER ? AU_CENTER : '';
            echo showbanners(1);

            echo '<div class="row">';
            $content = ['sm' => 12, 'md' => 12, 'lg' => 12];
            $right   = ['sm' => 4,  'md' => 3,  'lg' => 3];

            if (defined('RIGHT') && RIGHT) {
                $content['sm'] = $content['sm'] - $right['sm'];
                $content['md'] = $content['md'] - $right['md'];
                $content['lg'] = $content['lg'] - $right['lg'];
            }

            echo '<div class="col-xs-12 col-sm-'.$content['sm'].' col-md-'.$content['md'].' col-lg-'.$content['lg'].'">';
                echo defined('U_CENTER') && U_CENTER ? U_CENTER : '';
                echo CONTENT;
                echo defined('L_CENTER') && L_CENTER ? L_CENTER : '';
                echo showbanners(2);
            echo '</div>';

            if (defined('RIGHT') && RIGHT || defined('LEFT') && LEFT) {
                echo '<div class="col-xs-12 col-sm-'.$right['sm'].' col-md-'.$right['md'].' col-lg-'.$right['lg'].'">';
                    echo defined('RIGHT') && RIGHT ? RIGHT : '';
                    echo defined('LEFT') && LEFT ? LEFT : '';
                echo '</div>';
            }

            echo '</div>';

            echo defined('BL_CENTER') && BL_CENTER ? BL_CENTER : '';
        echo '</section>';
    echo '</div>';

    echo '<footer class="site-footer">';
        echo '<div class="container">';
            echo '<div class="row m-t-10">';
                echo defined('USER1') && USER1 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER1.'</div>' : '';
                echo defined('USER2') && USER2 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER2.'</div>' : '';
                echo defined('USER3') && USER3 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER3.'</div>' : '';
                echo defined('USER4') && USER4 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER4.'</div>' : '';
            echo '</div>';

            echo showFooterErrors();
            if ($settings['rendertime_enabled'] == 1 || $settings['rendertime_enabled'] == 2) {
                echo showrendertime().showMemoryUsage().'<br>';
            }

            echo parse_text($settings['footer'], ['parse_smileys' => FALSE, 'add_line_breaks' => TRUE]);

            echo '<br>'.showcopyright('', TRUE).showprivacypolicy();

            echo '<br>'.showcounter();

            echo ' | Based on MC template by Nikita Zotov';
            echo ' | &copy; '.date('Y').' Created by <a href="https://github.com/RobiNN1" target="_blank">RobiNN</a>';
        echo '</div>';
    echo '</footer>';
}

function opentable($title = FALSE, $class = '') {
    echo '<div class="opentable">';
    echo $title ? '<h3 class="title">'.$title.'</h3>' : '';
    echo '<div class="'.$class.'">';
}

function closetable() {
    echo '</div>';
    echo '</div>';
}

function openside($title = FALSE, $class = '') {
    echo '<div class="openside '.$class.'">';
    echo $title ? '<div class="title">'.$title.'</div>' : '';
}

function closeside() {
    echo '</div>';
}

if (method_exists(PHPFusion\HomePage::class, 'setLimit')) { // Function works only in v9.10 and newer
    PHPFusion\HomePage::setLimit(10); // Here you can change number of items
}

function display_home($info) {
    if (!empty($info)) {
        // You can delete this statement to show all modules
        if (
            (defined('ARTICLES_EXISTS') && !empty($info[DB_ARTICLES])) ||
            (defined('BLOG_EXISTS') && !empty($info[DB_BLOG])) ||
            (defined('DOWNLOADS_EXISTS') && !empty($info[DB_DOWNLOADS]))
        ) {
            unset($info[DB_ARTICLES]);
            unset($info[DB_BLOG]);
            unset($info[DB_DOWNLOADS]);
        }

        foreach ($info as $module) {
            if (!empty($module['data'])) {
                foreach ($module['data'] as $data) {
                    echo '<div class="home-item clearfix">';
                    if (!empty($data['image'])) {
                        echo '<img class="img-responsive" src="'.$data['image'].'" alt="'.$data['title'].'">';
                    }

                    echo '<h3><a href="'.$data['url'].'">'.$data['title'].'</a></h3>';
                    echo '<div class="small m-b-10 overflow-hide">'.$data['meta'].'</div>';
                    echo '<p class="description">'.nl2br(trim_text(strip_tags($data['content']), 200)).'</p>';
                    echo '<a class="pull-right btn btn-primary" href="'.$data['url'].'"><i class="fas fa-eye"></i> '.fusion_get_locale('news_0001', NEWS_LOCALE).'</a>';
                    echo '</div>';
                }
            }
        }
    }
}
