<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Main.php
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
namespace CzechiaTheme;

use PHPFusion\Panels;
use PHPFusion\SiteLinks;

class Main extends Core {
    public function __construct() {
        $settings = fusion_get_settings();

        add_to_footer('<script src="'.THEME.'js/scripts.min.js"></script>');

        if ($this->getParam('header') == TRUE) {
            self::header();

            $icon = '';
            if (defined('LEFT') && LEFT) {
                $icon = '<a href="#" id="toggle-leftmenu" class="navbar-brand icon"><i class="fa fa-bars"></i></a>';
            }

            $logo = '<a class="navbar-brand hidden-md hidden-lg" href="'.BASEDIR.$settings['opening_page'].'">'.$settings['sitename'].'</a>';

            $menu_options = [
                'id'           => 'main-menu',
                'navbar_class' => 'navbar-default',
                'show_header'  => $icon.$logo
            ];

            echo '<div style="min-height: 60px;">';
                echo SiteLinks::setSubLinks($menu_options)->showSubLinks();
            echo '</div>';
        }

        echo '<main id="main-container" class="clearfix">';
            $content = ['sm' => 12, 'md' => 12, 'lg' => 12];
            $right   = ['sm' => 4,  'md' => 3,  'lg' => 3];

            if (defined('RIGHT') && RIGHT && $this->getParam('right') == TRUE) {
                $content['sm'] = $content['sm'] - $right['sm'];
                $content['md'] = $content['md'] - $right['md'];
                $content['lg'] = $content['lg'] - $right['lg'];
            }

            if (defined('LEFT') && LEFT) {
                echo '<aside class="leftmenu"><div class="left-content">'.LEFT.'</div></aside>';
            }

            echo '<div class="container">';
                if ($this->getParam('notices') == TRUE) {
                    echo renderNotices(getnotices(['all', FUSION_SELF]));
                }

                echo defined('AU_CENTER') && AU_CENTER ? AU_CENTER : '';

                echo '<div class="row m-t-20 m-b-20">';
                    echo '<div class="col-xs-12 col-sm-'.$content['sm'].' col-md-'.$content['md'].' col-lg-'.$content['lg'].'">';
                        echo showbanners(1);
                        echo defined('U_CENTER') && U_CENTER ? U_CENTER : '';
                        echo CONTENT;
                        echo defined('L_CENTER') && L_CENTER ? L_CENTER : '';
                    echo '</div>';

                    if (defined('RIGHT') && RIGHT && $this->getParam('right') == TRUE) {
                        echo '<div class="col-xs-12 col-sm-'.$right['sm'].' col-md-'.$right['md'].' col-lg-'.$right['lg'].'">';
                            echo defined('RIGHT') && RIGHT ? RIGHT : '';
                        echo '</div>';
                    }
                echo '</div>';

                echo defined('BL_CENTER') && BL_CENTER ? BL_CENTER : '';

                echo showbanners(2);

            echo '</div>'; // container

        echo '</main>';

        if ($this->getParam('footer') == TRUE) {
            self::footer();
        }
    }

    private function header() {
        $locale = fusion_get_locale();
        $userdata = fusion_get_userdata();
        $settings = fusion_get_settings();
        $languages = fusion_get_enabled_languages();

        echo '<header class="theme-header">';
            echo '<div class="container"><div class="row">';
                echo '<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">';
                    echo '<a href="'.BASEDIR.$settings['opening_page'].'" title="'.$settings['sitename'].'">';
                        echo '<img src="'.BASEDIR.$settings['sitebanner'].'" class="logo" alt="Logo"/>';
                    echo '</a>';
                echo '</div>';
                echo '<div class="col-xs-12 col-sm-8 col-md-9 col-lg-9">';
                    echo '<div class="navbar-header navbar-right">';
                        echo '<ul class="menu">';

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
                                echo '<li class="dropdown">';
                                    echo '<a id="dduser" href="#" title="'.$userdata['user_name'].'" class="dropdown-toggle pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                                        echo display_avatar($userdata, '18px', '', FALSE, 'img-circle m-l-5 link-avatar');
                                        echo ' <span class="caret"></span>';
                                    echo '</a>';

                                    $user_url = BASEDIR.'profile.php?lookup='.$userdata['user_id'];

                                    echo '<ul class="dropdown-menu dropdown-user" aria-labelledby="dduser" role="menu">';
                                        echo '<li><div class="navbar-login">';
                                            echo '<p class="text-left"><strong>'.$userdata['user_name'].'</strong></p>';
                                            echo '<p class="text-left small">'.getuserlevel($userdata['user_level']).'</p>';
                                            echo '<div class="btn-group btn-group-sm btn-group-justified">';
                                                echo '<a href="'.$user_url.'" class="btn btn-primary">'.$locale['view'].' '.$locale['profile'].'</a>';
                                                echo '<a href="'.BASEDIR.'edit_profile.php" class="btn btn-warning">'.$locale['UM080'].'</a>';
                                            echo '</div>';
                                        echo '</div></li>';
                                        echo '<li class="divider m-b-0"></li>';
                                        echo session_get('login_as') ? '<li><a href="'.BASEDIR.'index.php?logoff='.$userdata['user_id'].'">'.$locale['UM103'].'</a></li>' : '';
                                        echo '<li><p class="m-0 p-10 p-b-5"><a href="'.BASEDIR.'index.php?logout=yes" class="btn btn-danger btn-sm btn-block"><i class="fa fa-sign-out fa-fw"></i> '.$locale['logout'].'</a></p></li>';
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

                                echo '<li><a href="'.BASEDIR.'messages.php" title="'.$locale['UM081'].'"><i class="fa fa-envelope fa-fw"></i>'.$messages_count.'</a></li>';

                                if (iADMIN) {
                                    echo '<li><a href="'.ADMIN.'index.php'.fusion_get_aidlink().'&pagenum=0" title="'.$locale['global_123'].'"><i class="fa fa-dashboard"></i></a></li>';
                                }
                            } else {
                                echo '<li class="dropdown loginform">';
                                    echo '<a href="#" id="login-register" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-sign-in"></i> '.$locale['login'].' <span class="caret"></span></a>';

                                    echo '<ul class="dropdown-menu" aria-labelledby="login-register">';
                                        echo '<li>'.self::loginForm().'</li>';
                                        echo '<li>'.str_replace(['[LINK]', '[/LINK]'], ['<a class="display-block" href="'.BASEDIR.'lostpassword.php">', '</a>'], $locale['global_106']).'</li>';
                                    echo '</ul>';
                                echo '</li>';
                                if ($settings['enable_registration']) {
                                    echo '<li><a href="'.BASEDIR.'register.php"><i class="fa fa-user-plus"></i> '.$locale['register'].'</a></li>';
                                }
                            }
                        echo '</ul>';
                    echo '</div>';
                echo '</div>';
            echo '</div></div>';
        echo '</header>';
    }

    private function loginForm() {
        $locale = fusion_get_locale();

        $action_url = FUSION_SELF.(FUSION_QUERY ? '?'.FUSION_QUERY : '');
        if (isset($_GET['redirect']) && strstr($_GET['redirect'], '/')) {
            $action_url = cleanurl(urldecode($_GET['redirect']));
        }

        $html = openform('loginform', 'post', $action_url, ['form_id' => 'login-form']);
        switch (fusion_get_settings('login_method')) {
            case 2:
                $placeholder = $locale['global_101c'];
                break;
            case 1:
                $placeholder = $locale['global_101b'];
                break;
            default:
                $placeholder = $locale['global_101a'];
        }

        $html .= form_text('user_name', '', '', ['placeholder' => $placeholder, 'required' => TRUE, 'input_id' => 'username']);
        $html .= form_text('user_pass', '', '', ['placeholder' => $locale['global_102'], 'type' => 'password', 'required' => TRUE, 'input_id' => 'userpassword']);
        $html .= form_checkbox('remember_me', $locale['global_103'], '', ['value' => 'y', 'reverse_label' => TRUE, 'input_id' => 'rememberme']);
        $html .= form_button('login', $locale['global_104'], '', ['class' => 'btn-primary m-t-5 m-b-5', 'icon' => 'fa fa-sign-in', 'input_id' => 'loginbtn']);
        $html .= closeform();

        return $html;
    }

    private function footer() {
        $settings = fusion_get_settings();

        echo '<footer class="site-footer">';
            if (self::getParam('footer_panels') == TRUE) {
                echo '<div id="footer">';
                    echo '<div class="row">';
                        echo defined('USER1') && USER1 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER1.'</div>' : '';
                        echo defined('USER2') && USER2 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER2.'</div>' : '';
                        echo defined('USER3') && USER3 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER3.'</div>' : '';
                        echo defined('USER4') && USER4 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER4.'</div>' : '';
                    echo '</div>';

                    echo '<div class="p-20">'.nl2br(parse_textarea($settings['footer'], FALSE)).'</div>';

                    if ($settings['visitorcounter_enabled']) {
                        echo showcounter();
                    }
                echo '</div>';
            }

            $errors = showFooterErrors();
            if ($errors) {
                echo '<div class="errors">'.$errors.'</div>';
            }

            if ($settings['rendertime_enabled'] == 1 || $settings['rendertime_enabled'] == 2) {
                echo '<div id="rendertime">';
                    echo showrendertime();
                    echo showMemoryUsage();
                echo '</div>';
            }

            echo '<div id="copyright" class="clearfix p-b-10">';
                echo $this->themeCopyright();

                echo '<div class="pull-right">';
                    echo showcopyright('', TRUE).showprivacypolicy();
                echo '</div>';
            echo '</div>';
        echo '</footer>';
        echo '<div class="overlay"><!-- --></div>';
    }

    public static function hideAll() {
        Panels::getInstance(TRUE)->hide_panel('RIGHT');
        Panels::getInstance(TRUE)->hide_panel('LEFT');
        Panels::getInstance(TRUE)->hide_panel('AU_CENTER');
        Panels::getInstance(TRUE)->hide_panel('U_CENTER');
        Panels::getInstance(TRUE)->hide_panel('L_CENTER');
        Panels::getInstance(TRUE)->hide_panel('BL_CENTER');
        self::setParam('header', FALSE);
        self::setParam('footer', FALSE);
        self::setParam('notices', FALSE);
    }
}
