<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: theme.php
| Author: Frederick MC Chan
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

require_once INCLUDES.'theme_functions_include.php';

const THEME_BULLET = '&middot;';
const BOOTSTRAP = TRUE;
const FONTAWESOME = TRUE;

if (!defined('GLOWIE_LOCALE')) {
    if (file_exists(THEME.'locale/'.LANGUAGE.'.php')) {
        define('GLOWIE_LOCALE', THEME.'locale/'.LANGUAGE.'.php');
    } else {
        define('GLOWIE_LOCALE', THEME.'locale/English.php');
    }
}

function render_page() {
    $locale = fusion_get_locale();
    $settings = fusion_get_settings();
    $userdata = fusion_get_userdata();
    $languages = fusion_get_enabled_languages();

    add_to_head('<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto"/>');
    add_to_head('<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato"/>');

    echo '<header>';
        echo '<div class="header-top"><div class="container"><div class="row">';
            echo '<div class="col-xs-4 col-sm-2">';
                echo '<a class="header-logo" href="'.BASEDIR.$settings['opening_page'].'"><img src="'.BASEDIR.$settings['sitebanner'].'" alt="'.$settings['sitename'].'" class="img-responsive"/></a>';
            echo '</div>';

            echo '<div class="hidden-xs col-sm-6 header-search">';
                echo openform('searchform', 'post', $settings['siteurl'].'search.php?stype=all', [
                    'remote_url' => $settings['site_path'].'search.php',
                    'class'      => 'form-inline header-form'
                ]);

                echo form_text('stext', '', '', [
                    'class'              => 'm-t-3',
                    'placeholder'        => $locale['search'],
                    'append_button'      => TRUE,
                    'append_type'        => 'submit',
                    'append_form_value'  => $locale['search'],
                    'append_value'       => '<i class="fa fa-search"></i>',
                    'append_button_name' => 'search',
                    'append_class'       => 'btn btn-primary'
                ]);
                echo closeform();
            echo '</div>';

            echo '<div class="col-xs-8 col-sm-4">';
                echo '<ul class="list-style-none header-usermenu">';
                    echo '<li class="visible-xs-search hidden-sm hidden-md hidden-lg"><a href="'.BASEDIR.'search.php"><i class="fa fa-search"></i></a></li>';

                    if (count($languages) > 1) {
                        echo '<li class="dropdown language-switcher">';
                            echo '<a id="ddlangs" href="#" class="dropdown-toggle pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="'.LANGUAGE.'">';
                                echo '<i class="fa fa-globe"></i> ';
                                echo '<img class="current" style="margin-top: -5px;" src="'.BASEDIR.'locale/'.LANGUAGE.'/'.LANGUAGE.'-s.png" alt="'.translate_lang_names(LANGUAGE).'"/>';
                                echo '<span class="caret"></span>';
                            echo '</a>';

                            echo '<ul class="dropdown-menu" aria-labelledby="ddlangs">';
                                foreach ($languages as $language_folder => $language_name) {
                                    echo '<li><a class="display-block" href="'.clean_request('lang='.$language_folder, ['lang'], FALSE).'">';
                                    echo '<img class="m-r-5" src="'.BASEDIR.'locale/'.$language_folder.'/'.$language_folder.'-s.png" alt="'.$language_folder.'"/> ';
                                    echo $language_name;
                                    echo '</a></li>';
                                }
                            echo '</ul>';
                        echo '</li>';
                    }

                    if (iMEMBER) {
                        echo '<li class="dropdown user-menu">';
                            echo '<a id="dduser" href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                                echo display_avatar($userdata, '24px', '', FALSE, 'img-rounded header-avatar m-r-5');
                                echo $userdata['user_name'];
                                echo ' <span class="caret"></span>';
                            echo '</a>';

                            echo '<ul class="dropdown-menu text-left" aria-labelledby="dduser">';
                                echo '<li><a href="'.BASEDIR.'profile.php?lookup='.$userdata['user_id'].'">'.$locale['view'].' '.$locale['profile'].'</a></li>';
                                echo '<li class="divider"></li>';
                                echo '<li><a href="'.BASEDIR.'edit_profile.php">'.$locale['UM080'].'</a></li>';
                                echo iADMIN ? '<li role="separator" class="divider"></li>' : '';
                                echo iADMIN ? '<li><a href="'.ADMIN.'index.php'.fusion_get_aidlink().'&pagenum=0">'.$locale['global_123'].'</a></li>' : '';
                                echo '<li class="divider"></li>';
                                echo session_get('login_as') ? '<li><a href="'.BASEDIR.'index.php?logoff='.$userdata['user_id'].'">'.$locale['UM103'].'</a></li>' : '';
                                echo '<li><a href="'.BASEDIR.'index.php?logout=yes">'.$locale['logout'].'</a></li>';
                            echo '</ul>';
                        echo '</li>';

                        $msg_count = dbcount(
                            "('message_id')",
                            DB_MESSAGES, "message_to=:my_id AND message_read=:unread AND message_folder=:inbox",
                            [':inbox' => 0, ':my_id' => $userdata['user_id'], ':unread' => 0]
                        );

                        $messages_count = '';
                        if ($msg_count > 0) {
                            $messages_count = '<span class="label label-danger msg-count">'.$msg_count.'</span>';
                        }

                        echo '<li><a href="'.BASEDIR.'messages.php" title="'.$locale['message'].'"><i class="fa fa-envelope"></i>'.$messages_count.'</a></li>';

                        // echo '<li><a href="#"><i class="fa fa-bell"></i></a></li>';
                    } else {
                        echo '<li><a class="btn btn-success" href="'.BASEDIR.'login.php">'.$locale['login'].'</a></li>';

                        if ($settings['enable_registration']) {
                            echo '<li><a class="btn btn-primary" href="'.BASEDIR.'register.php">'.$locale['register'].'</a></li>';
                        }
                    }
                echo '</ul>';
            echo '</div>';
        echo '</div></div></div>';

        echo '<div class="header-menu"><div class="container">';
            $menu_options = [
                'id'           => 'main-menu',
                'navbar_class' => 'navbar-default',
                'show_header'  => TRUE
            ];

            echo \PHPFusion\SiteLinks::setSubLinks($menu_options)->showSubLinks();
        echo '</div></div>';
    echo '</header>';

    echo '<main id="main-container" class="clearfix">';

        echo defined('AU_CENTER') && AU_CENTER ? AU_CENTER : '';
        echo showbanners(1);

        echo '<div class="container">';
        echo '<div class="row">';

        $content = ['sm' => 12, 'md' => 12, 'lg' => 12];
        $left    = ['sm' => 3,  'md' => 2,  'lg' => 2];
        $right   = ['sm' => 3,  'md' => 2,  'lg' => 2];

        if (defined('LEFT') && LEFT) {
            $content['sm'] = $content['sm'] - $left['sm'];
            $content['md'] = $content['md'] - $left['md'];
            $content['lg'] = $content['lg'] - $left['lg'];
        }

        if (defined('RIGHT') && RIGHT) {
            $content['sm'] = $content['sm'] - $right['sm'];
            $content['md'] = $content['md'] - $right['md'];
            $content['lg'] = $content['lg'] - $right['lg'];
        }

        if (defined('LEFT') && LEFT) {
            echo '<div id="leftside" class="col-xs-12 col-sm-'.$left['sm'].' col-md-'.$left['md'].' col-lg-'.$left['lg'].'">';
                echo LEFT;
            echo '</div>';
        }

        echo '<div class="col-xs-12 col-sm-'.$content['sm'].' col-md-'.$content['md'].' col-lg-'.$content['lg'].'">';
            echo defined('U_CENTER') && U_CENTER ? U_CENTER : '';
            echo CONTENT;
            echo defined('L_CENTER') && L_CENTER ? L_CENTER : '';
            echo showbanners(2);
        echo '</div>';

        if (defined('RIGHT') && RIGHT) {
            echo '<div id="rightside" class="col-xs-12 col-sm-'.$right['sm'].' col-md-'.$right['md'].' col-lg-'.$right['lg'].'">';
                echo RIGHT;
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';

        echo defined('BL_CENTER') && BL_CENTER ? BL_CENTER : '';

    echo '</main>';

    echo '<footer class="m-t-20 m-b-20"><div class="container"><hr/>';
        echo '<div>Theme created by <a href="https://phpfusion.com" target="_blank">Frederick MC Chan</a> & <a href="https://github.com/RobiNN1" target="_blank">RobiNN</a></div>';
        echo showFooterErrors();

        echo '<div class="row m-t-10">';
            echo defined('USER1') && USER1 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER1.'</div>' : '';
            echo defined('USER2') && USER2 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER2.'</div>' : '';
            echo defined('USER3') && USER3 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER3.'</div>' : '';
            echo defined('USER4') && USER4 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER4.'</div>' : '';
        echo '</div>';

        echo showcopyright('', TRUE).showprivacypolicy();

        if ($settings['rendertime_enabled'] == 1 || $settings['rendertime_enabled'] == 2) {
            echo '<br/><small>'.showrendertime().showMemoryUsage().'</small>';
        }

        echo '<br/>'.showcounter();

        echo nl2br(parse_textarea($settings['footer'], FALSE));

    echo '</div></footer>';
}

function opentable($title = FALSE) {
    echo '<div class="opentable panel panel-default">';
    echo $title ? '<div class="panel-heading"><h4 class="panel-title">'.$title.'</h4></div>' : '';
    echo '<div class="panel-body">';
}

function closetable() {
    echo '</div>';
    echo '</div>';
}

function openside($title = FALSE) {
    echo '<aside class="sidepanel">';
    echo $title ? '<div class="sidepanel-heading">'.$title.'</div>' : '';
    echo '<div class="sidepanel-body">';
}

function closeside() {
    echo '</div>';
    echo '</aside>';
}
