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
namespace MaterialTheme;

use PHPFusion\Panels;
use PHPFusion\SiteLinks;

class Main extends Core {
    public function __construct() {
        $settings = fusion_get_settings();
        $mt_settings = get_theme_settings('Material');

        $url = THEMES.'Material/assets/';
        add_to_head('<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Noto+Sans:300,400,700,300italic,400italic,700italic">');
        add_to_head('<link rel="stylesheet" href="'.$url.'mCustomScrollbar/jquery.mCustomScrollbar.min.css">');
        add_to_footer('<script src="'.$url.'mCustomScrollbar/jquery.mCustomScrollbar.min.js"></script>'); // https://github.com/malihu/malihu-custom-scrollbar-plugin
        add_to_footer('<script src="'.INCLUDES.'jscripts/js.cookie.min.js"></script>');
        $theme_js = file_exists($url.'js/scripts.min.js') ? $url.'js/scripts.min.js' : $url.'js/scripts.js';
        add_to_footer('<script async src="'.$theme_js.'?v='.filemtime($theme_js).'"></script>');

        echo '<main id="main-page-body" class="clearfix '.$this->getParam('body_class').'">';

            if ($this->getParam('menu') == TRUE) {
                $this->topNav();

                $settings_logo = !empty($mt_settings['logo']) ? $mt_settings['logo'] : NULL;

                switch ($settings_logo) {
                    case 1:
                        $logo = '';
                        break;
                    case 2:
                        $logo = '<a href="'.BASEDIR.$settings['opening_page'].'" class="navbar-brand hidden-sm">';
                        $logo .= '<img src="'.BASEDIR.$settings['sitebanner'].'" alt="'.$settings['sitename'].'" class="img-responsive"/>';
                        $logo .= '</a>';
                        break;
                    default:
                        $logo = '<a href="'.BASEDIR.$settings['opening_page'].'" class="navbar-brand hidden-sm">'.$settings['sitename'].'</a>';
                }

                $icon = '';
                if (defined('LEFT') && LEFT && $this->getParam('left_panel') == TRUE) {
                    $icon = '<div class="hamburger" style="display: none;"><div class="hamburger-box"><div class="hamburger-inner"></div></div></div>';
                }

                $menu_options = [
                    'id'             => 'main-menu',
                    'navbar_class'   => 'navbar-inverse',
                    'nav_class'      => 'nav navbar-nav primary navbar-right',
                    'grouping'       => TRUE,
                    'links_per_page' => 10,
                    'show_header'    => $icon.$logo
                ];

                echo '<div style="min-height: 64px;">';
                    echo SiteLinks::setSubLinks($menu_options)->showSubLinks();
                echo '</div>';
            }

            $content = ['sm' => 12, 'md' => 12, 'lg' => 12];
            $right   = ['sm' =>  4, 'md' =>  3, 'lg' => 3];

            if (defined('RIGHT') && RIGHT &&
                $this->getParam('right') == TRUE ||
                $this->getParam('right_pre_content') ||
                $this->getParam('right_middle_content') ||
                $this->getParam('right_post_content')
            ) {
                $content['sm'] = $content['sm'] - $right['sm'];
                $content['md'] = $content['md'] - $right['md'];
                $content['lg'] = $content['lg'] - $right['lg'];
            }

            if (defined('LEFT') && LEFT && $this->getParam('left_panel') == TRUE) {
                echo '<aside class="leftmenu">';
                    echo $this->getParam('left_pre_content');

                    echo '<div class="left-content">';
                        echo LEFT;
                    echo '</div>';

                    echo $this->getParam('left_post_content');
                echo '</aside>';
            }

            if ($this->getParam('notices') == TRUE) {
                echo '<div class="notices">';
                    echo renderNotices(getNotices(['all', FUSION_SELF]));
                echo '</div>';
            }

            $this->header();

            if ($this->getParam('container') == TRUE) {
                echo '<div class="container main-box p-t-20 '.$this->getParam('container_class').'">';
            }

            echo defined('AU_CENTER') && AU_CENTER ? AU_CENTER : '';

            echo '<div class="row '.$this->getParam('main_row_class').'">';
                echo '<div class="col-xs-12 col-sm-'.$content['sm'].' col-md-'.$content['md'].' col-lg-'.$content['lg'].'">';
                    echo showbanners(1);
                    echo defined('U_CENTER') && U_CENTER ? U_CENTER : '';

                    if ($this->getParam('content_container') == TRUE) {
                        echo '<div class="main-content">';
                    }

                    echo CONTENT;

                    if ($this->getParam('content_container') == TRUE) {
                        echo '</div>';
                    }

                    echo defined('L_CENTER') && L_CENTER ? L_CENTER : '';
                    echo showbanners(2);
                echo '</div>';

                if (defined('RIGHT') && RIGHT &&
                    $this->getParam('right') == TRUE ||
                    $this->getParam('right_pre_content') ||
                    $this->getParam('right_middle_content') ||
                    $this->getParam('right_post_content')
                ) {
                    echo '<div class="col-xs-12 col-sm-'.$right['sm'].' col-md-'.$right['md'].' col-lg-'.$right['lg'].'">';
                        echo self::getParam('right_card') == TRUE ? '<div class="card '.self::getParam('right_card_class').'">' : '';
                        echo $this->getParam('right_pre_content');
                        echo $this->getParam('right_middle_content');

                        if (defined('RIGHT') && RIGHT && $this->getParam('right_content') == TRUE) {
                            echo RIGHT;
                        }

                        echo $this->getParam('right_post_content');

                    echo self::getParam('right_card') == TRUE ? '</div>' : '';
                    echo '</div>';
                }
            echo '</div>';

            echo defined('BL_CENTER') && BL_CENTER ? BL_CENTER : '';

            if ($this->getParam('container') == TRUE) {
                echo '</div>';
            }
        echo '</main>';

        if ($this->getParam('footer') == TRUE) {
            $this->footer();
            echo '<div class="overlay"><!-- --></div>';
        }
    }

    private function topNav() {
        $locale = fusion_get_locale();
        $userdata = fusion_get_userdata();
        $settings = fusion_get_settings();
        $languages = fusion_get_enabled_languages();

        echo '<div class="top-nav">';
            echo '<div class="topnav-content">';
                echo '<div class="pull-left">';
                    if (defined('LEFT') && LEFT && $this->getParam('left_panel') == TRUE) {
                        echo '<div class="hamburger"><div class="hamburger-box"><div class="hamburger-inner"></div></div></div>';
                    }

                    $this->socialLinks();
                echo '</div>';

                echo '<ul class="right-links">';
                    echo '<li><a href="#" id="search-box" title="'.$locale['search'].'"><i class="fa fa-search"></i></a></li>';
                    echo '<li><a href="#" data-action="dark-mode" class="darkmode-switch'.(DARK_MODE ? ' active' : '').'" title="'.$this->setLocale('main_01').'"></a></li>';

                    if (count($languages) > 1) {
                        echo '<li class="dropdown language-switcher">';
                            echo '<a id="ddlangs" href="#" class="dropdown-toggle pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                                echo '<span class="hidden-xs"><i class="fa fa-globe-europe"></i> </span>';
                                echo '<img class="current" src="'.BASEDIR.'locale/'.LANGUAGE.'/'.LANGUAGE.'.png" alt="'.translate_lang_names(LANGUAGE).'"/>';
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
                        $msg_count = dbcount(
                            "('message_id')",
                            DB_MESSAGES, "message_to=:my_id AND message_read=:unread AND message_folder=:inbox",
                            [':inbox' => 0, ':my_id' => $userdata['user_id'], ':unread' => 0]
                        );

                        $messages_count = '';
                        if ($msg_count > 0) {
                            $messages_count = '<span class="label label-danger m-t-5 pull-right">'.$msg_count.'</span>';
                        }

                        echo '<li class="dropdown action-menu">';
                            echo '<a id="dduser" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">';
                                echo '<div class="display-inline'.($msg_count > 0 ? ' active' : '').'" title="'.$userdata['user_name'].'">';
                                echo display_avatar($userdata, '23px', '', FALSE, 'img-circle m-l-5');
                                echo '</div>';
                                echo '<span class="caret"></span>';
                            echo '</a>';

                            echo '<ul class="dropdown-menu" aria-labelledby="dduser">';
                                echo iADMIN ? '<li><a href="'.ADMIN.'index.php'.fusion_get_aidlink().'&pagenum=0"><i class="fa fa-tachometer-alt"></i><small class="text-overflow-hide">'.$locale['global_123'].'</small></a></li>' : '';
                                echo '<li><a href="'.BASEDIR.'messages.php"><i class="fa fa-envelope"></i><small class="text-overflow-hide">'.$locale['UM081'].'</small>'.$messages_count.'</a></li>';
                                echo '<li><a href="'.BASEDIR.'edit_profile.php"><i class="fa fa-pen"></i><small class="text-overflow-hide">'.$locale['UM080'].'</small></a></li>';
                                echo '<li><a href="'.BASEDIR.'profile.php?lookup='.$userdata['user_id'].'"><i class="fa fa-eye"></i><small class="text-overflow-hide">'.$locale['view'].' '.$locale['profile'].'</small></a></li>';
                                echo '<li><a href="'.BASEDIR.'members.php"><i class="fa fa-users"></i><small class="text-overflow-hide">'.$locale['UM082'].'</small></a></li>';
                                echo session_get('login_as') ? '<li><a href="'.BASEDIR.'index.php?logoff='.$userdata['user_id'].'"><i class="fa fa-sign-out-alt"></i><small class="text-overflow-hide">'.$locale['UM103'].'</small></a></li>' : '';
                                echo '<li><a href="'.BASEDIR.'index.php?logout=yes"><i class="fa fa-sign-out-alt"></i><small class="text-overflow-hide">'.$locale['logout'].'</small></a></li>';
                            echo '</ul>';
                        echo '</li>';
                    } else {
                        echo '<li><a href="#" id="login-register" title="'.$locale['login'].'"><i class="fa fa-sign-in-alt"></i> <span class="caret"></span></a></li>';

                        if ($settings['enable_registration']) {
                            echo '<li><a href="'.BASEDIR.'register.php" title="'.$locale['register'].'"><i class="fa fa-user-plus"></i></a></li>';
                        }

                        $modal = openmodal('login-register', $locale['login'], ['button_id' => 'login-register', 'class' => ' ']);
                        $modal .= $this->loginForm();
                        $mfooter = str_replace(['[LINK]', '[/LINK]'], ['<a href="'.BASEDIR.'register.php">', '</a>'], $locale['global_105']);
                        $modal .= $settings['enable_registration'] ? modalfooter($mfooter) : '';
                        $modal .= closemodal();

                        add_to_jquery('$("#login-register-Modal").on("shown.bs.modal", function () {$("#username").focus();});');

                        add_to_footer($modal);
                    }
                echo '</ul>';
            echo '</div>';

            $this->searchForm();
        echo '</div>';
    }

    private function header() {
        if ($this->getParam('header') == TRUE || $this->getParam('small_header') == TRUE) {
            $small = $this->getParam('small_header') == TRUE ? 'small_' : '';
            $styles = !empty($this->getParam('header_styles')) ? ' style="'.$this->getParam('header_styles').'"' : '';

            echo '<header class="'.$small.'header" id="'.$small.'header_'.$this->getParam($small.'header_id').'"'.$styles.'>';
                echo $this->getParam($small.'header_content');
            echo '</header>';
        }
    }

    private function footer() {
        $settings = fusion_get_settings();

        echo '<footer id="site-footer">';
            echo '<div id="prefooter">';
                echo '<div class="row text-dark">';
                    echo defined('USER1') && USER1 ? '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">'.USER1.'</div>' : '';
                    echo defined('USER2') && USER2 ? '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">'.USER2.'</div>' : '';
                    echo defined('USER3') && USER3 ? '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">'.USER3.'</div>' : '';
                    echo defined('USER4') && USER4 ? '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">'.USER4.'</div>' : '';
                echo '</div>';

                if (self::excludeFooterPanels()) {
                    echo '<div class="row">';
                        echo '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">'.self::getFooterPanel('footer_col_1').'</div>';
                        echo '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">'.self::getFooterPanel('footer_col_2').'</div>';
                        echo '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">'.self::getFooterPanel('footer_col_3').'</div>';
                        echo '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">'.self::getFooterPanel('footer_col_4').'</div>';
                    echo '</div>';
                }
            echo '</div>';

            echo '<div id="footer">';
                $errors = showFooterErrors();
                if ($errors) echo '<div class="errors">'.$errors.'</div>';

                echo '<div id="copyright" class="clearfix">';
                    echo '<div class="row">';
                        echo '<div class="col-xs-12 col-md-4">';
                            echo $this->themeCopyright();
                        echo '</div>';

                        echo '<div class="col-xs-12 col-md-8">';
                            echo '<div id="phpfusioncopyright">';
                                echo showcopyright(TRUE);
                                echo showprivacypolicy();
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';

                    if ($settings['rendertime_enabled'] == 1 || $settings['rendertime_enabled'] == 2) {
                        echo '<div id="rendertime"><small>';
                            echo showrendertime();
                            echo showMemoryUsage();
                        echo '</small></div>';
                    }
                echo '</div>';
            echo '</div>';
        echo '</footer>';
    }

    private function socialLinks() {
        $theme_settings = get_theme_settings('Material');

        if (!empty($theme_settings['social_links']) && $theme_settings['social_links'] == TRUE) {
            $result = dbquery("SELECT * FROM ".DB_MT_NETWORKS." ORDER BY network_order ASC");

            if (dbrows($result)) {
                echo '<ul class="social-networks pull-left">';
                    while ($data = dbarray($result)) {
                        if ($data['network_visible']) {
                            echo '<li><a href="'.$data['network_link'].'" title="'.$data['network_title'].'" target="_blank"><i class="'.$data['network_icon'].'"></i></a></li>';
                        }
                    }
                echo '</ul>';
            }
        }
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
            $html .= form_button('login', $locale['global_104'], '', ['class' => 'btn-primary m-t-5 m-b-5', 'icon' => 'fa fa-sign-in-alt', 'input_id' => 'loginbtn']);
            $html .= str_replace(['[LINK]', '[/LINK]'], ['<a href="'.BASEDIR.'lostpassword.php">', '</a>'], $locale['global_106']);
        $html .= closeform();

        return $html;
    }

    private function searchForm() {
        $settings = fusion_get_settings();

        echo '<div class="searchform">';
            echo openform('searchform', 'post', $settings['siteurl'].'search.php?stype=all', ['remote_url' => $settings['site_path'].'search.php']);
            echo '<div class="search-box">';
                echo form_text('stext', '', '', [
                    'placeholder'      => $this->setLocale('main_02'),
                    'prepend_value'    => '<i class="fa fa-times" id="search-back"></i>',
                    'append_value'     => '<button type="submit" class="btn-search"><i class="fa fa-search"></i></button>',
                    'class'            => 'm-b-0',
                    'autocomplete_off' => TRUE
                ]);
            echo '</div>';
            echo closeform();
        echo '</div>';
    }

    public static function headerContent($options = []) {
        $default_options = [
            'id'           => 'default',
            'title'        => '',
            'sub_title'    => '',
            'breadcrumbs'  => TRUE,
            'small_header' => FALSE,
            'background'   => FALSE,
            'random_image' => TRUE,
            'custom'       => ''
        ];

        $options += $default_options;

        $small = $options['small_header'] == TRUE ? 'small_' : '';
        self::setParam($small.'header', TRUE);
        self::setParam($small.'header_id', $options['id']);

        if (!empty($options['custom'])) {
            $header = $options['custom'];
        } else {
            $header = '<div class="container">';
            $header .= '<div class="center-y">';
            $header .= !empty($options['title']) ? '<h1 class="title">'.$options['title'].'</h1>' : '';
            $header .= !empty($options['sub_title']) ? '<br><h2 class="sub-title">'.$options['sub_title'].'</h2>' : '';
            $header .= '</div>';
            $header .= $options['breadcrumbs'] == TRUE ? render_breadcrumbs() : '';
            $header .= '</div>';
        }

        self::setParam($small.'header_content', $header);

        if ($options['background'] == TRUE || !empty($options['background'])) {
            if ($options['random_image'] == TRUE || $options['background'] !== NULL) {
                $background = !empty($options['background']) && file_exists($options['background']) ? $options['background'] : self::getRandImg();
                self::setParam('header_styles', 'background: url('.$background.')');
            }
        }
    }

    public static function getRandImg() {
        $arr = [];
        $folder = THEME.'images/header_bg/';
        $suffix = DARK_MODE ? 'dark': 'light';
        $files = glob($folder.'*_'.$suffix.'*');
        $list = preg_replace('/^.+[\\\\\\/]/', '', $files);
        $img = '';

        foreach ($list as $file) {
            if (!isset($img)) {
                $img = '';
            }

            if (is_file($folder.$file)) {
                $tmp = explode('.', $file);
                $ext = end($tmp);

                if ($ext === 'jpeg' || $ext === 'jpg' || $ext === 'png') {
                    array_push($arr, $file);
                    $img = $file;
                }
            }
        }

        if ($img != '') {
            $img = array_rand($arr);
            $img = $arr[$img];
        }

        $img = str_replace(["'", " "], ["\'", "%20"], $img);

        return $folder.$img;
    }

    public static function hidePanels() {
        Panels::getInstance(TRUE)->hide_panel('LEFT');
        Panels::getInstance(TRUE)->hide_panel('RIGHT');
        Panels::getInstance(TRUE)->hide_panel('U_CENTER');
        Panels::getInstance(TRUE)->hide_panel('L_CENTER');
        Panels::getInstance(TRUE)->hide_panel('AU_CENTER');
        Panels::getInstance(TRUE)->hide_panel('BL_CENTER');
    }
}
