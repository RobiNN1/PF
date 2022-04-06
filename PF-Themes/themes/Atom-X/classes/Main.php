<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Main.php
| Author: PHP Fusion Inc
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
namespace AtomXTheme;

use PHPFusion\Panels;
use PHPFusion\SiteLinks;

class Main extends Core {
    public function renderPage() {
        echo '<div class="page-box">';
            echo '<section id="topcontent"><div class="container-fluid">';
                echo '<div class="logo"><a href="'.BASEDIR.$this->settings['opening_page'].'"><img src="'.BASEDIR.$this->settings['sitebanner'].'" alt="Logo" class="img-responsive" style="width:25%;"></a></div>';

                if (defined('ATOMX_SOCIAL_NETWORKS') && is_array(ATOMX_SOCIAL_NETWORKS)) {
                    echo '<div class="social-links">';
                        foreach (ATOMX_SOCIAL_NETWORKS as $link => $icon) {
                            echo '<a href="'.$link.'" target="_blank"><i class="'.$icon.'"></i></a>';
                        }
                    echo '</div>';
                }

            echo '</div></section>'; // #topcontent

            echo '<header id="header"><div class="container-fluid"><div class="col-xs-12 col-md-12 col-lg-12">';
                $nav = SiteLinks::setSubLinks([
                    'id'              => 'atom-menu',
                    'navbar_class'    => 'navbar-default',
                    'nav_class'       => 'nav navbar-nav primary',
                    'container_fluid' => TRUE,
                    'show_header'     => TRUE,
                    'html_content'    => $this->userMenu()
                ]);
                $nav->addMenuLink(1000, $this->locale['search'], 0, '#', '', FALSE, FALSE,  FALSE, FALSE, 'search-btn');
                echo SiteLinks::getInstance('atom-menu')->showSubLinks();
            echo '</div></div></header>'; // #header

            $this->userInfoBar();

            new SearchEngine();

            echo '<div class="notices">';
                echo renderNotices(getnotices(['all', FUSION_SELF]));
            echo '</div>';

            if (self::getParam('section_header')) {
                echo '<section class="section-block '.self::getParam('section_header_class').'">';
                    echo self::getParam('section_header');
                echo '</section>';
            }

            $body_id = self::getParam('greybody') == TRUE ? 'greybody' : 'mainbody';
            echo '<section id="'.$body_id.'">';
                echo self::getParam('container_fluid') ? '<div class="container-fluid">' : '';

                echo defined('AU_CENTER') && AU_CENTER ? AU_CENTER : '';
                echo showbanners(1);

                echo '<div class="row">';

                $content = ['sm' => 12, 'md' => 12, 'lg' => 12];
                $left    = ['sm' => 3,  'md' => 2,  'lg' => 2];
                $right   = ['sm' => 3,  'md' => 3,  'lg' => 3];

                if (defined('LEFT') && LEFT) {
                    $content['sm'] = $content['sm'] - $left['sm'];
                    $content['md'] = $content['md'] - $left['md'];
                    $content['lg'] = $content['lg'] - $left['lg'];
                }

                if (defined('RIGHT') && RIGHT && $this->getParam('right') == TRUE || $this->getParam('right_content')) {
                    $content['sm'] = $content['sm'] - $right['sm'];
                    $content['md'] = $content['md'] - $right['md'];
                    $content['lg'] = $content['lg'] - $right['lg'];
                }

                if (defined('LEFT') && LEFT) {
                    echo '<div class="col-xs-12 col-sm-'.$left['sm'].' col-md-'.$left['md'].' col-lg-'.$left['lg'].'">';
                        echo LEFT;
                    echo '</div>';
                }

                echo '<div class="col-xs-12 col-sm-'.$content['sm'].' col-md-'.$content['md'].' col-lg-'.$content['lg'].'">';
                    echo defined('U_CENTER') && U_CENTER ? U_CENTER : '';
                    echo CONTENT;
                    echo defined('L_CENTER') && L_CENTER ? L_CENTER : '';
                    echo showbanners(2);
                echo '</div>';

                if (defined('RIGHT') && RIGHT && $this->getParam('right') == TRUE || $this->getParam('right_content')) {
                    echo '<div class="col-xs-12 col-sm-'.$right['sm'].' col-md-'.$right['md'].' col-lg-'.$right['lg'].'">';
                        echo ($this->getParam('right') == TRUE && defined('RIGHT') && RIGHT) ? RIGHT : '';
                        echo $this->getParam('right_content');
                    echo '</div>';
                }

                echo '</div>';

                echo defined('BL_CENTER') && BL_CENTER ? BL_CENTER : '';

                echo self::getParam('container_fluid') ? '</div>' : '';
            echo '</section>'; // #mainbody

            echo '<section class="section-block">';
                add_to_jquery('$("#backtotop").on("click", function (e) {e.preventDefault();$("html, body").animate({scrollTop: 0}, 200);});');
                echo '<div class="text-center m-t-10"><a href="#" id="backtotop" class="btn btn-primary btn-sm p-l-15 p-r-15"><i class="fa fa-chevron-up"></i></a></div>';

                echo '<div class="row m-t-10">';
                    echo defined('USER1') && USER1 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER1.'</div>' : '';
                    echo defined('USER2') && USER2 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER2.'</div>' : '';
                    echo defined('USER3') && USER3 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER3.'</div>' : '';
                    echo defined('USER4') && USER4 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER4.'</div>' : '';
                echo '</div>';
            echo '</section>';

            echo '<footer id="footer" class="m-b-20"><div class="container-fluid">';
                echo showFooterErrors();

                echo '<div class="row">';
                    echo '<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">';
                        echo nl2br(parse_textarea($this->settings['footer'], FALSE));
                        echo '<br>';
                        echo self::themeCopyright();
                    echo '</div>';
                    echo '<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-right">';
                        echo showcopyright().showprivacypolicy();

                        if ($this->settings['rendertime_enabled'] == 1 || $this->settings['rendertime_enabled'] == 2) {
                            echo '<br/><small>'.showrendertime().showMemoryUsage().'</small>';
                        }

                        echo '<br/>'.showcounter();
                    echo '</div>';
                echo '</div>';
            echo '</div></footer>'; // #footer
        echo '</div>'; // .page-box
    }

    private function userMenu() {
        $languages = fusion_get_enabled_languages();

        if (iMEMBER) {
            $name = $this->locale['ax9_001'].$this->userdata['user_name'];
        } else {
            $name = $this->locale['login'];
        }

        ob_start();
        echo '<ul class="nav navbar-nav secondary navbar-right m-r-0">';
            if (count($languages) > 1) {
                echo '<li class="dropdown language-switcher">';
                    echo '<a id="ddlangs" href="#" class="dropdown-toggle pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="'.LANGUAGE.'">';
                        echo '<i class="fa fa-globe"></i> ';
                        echo '<img src="'.BASEDIR.'locale/'.LANGUAGE.'/'.LANGUAGE.'-s.png" alt="'.translate_lang_names(LANGUAGE).'"/>';
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

            echo '<li id="user-info" class="dropdown">';
                echo '<button type="button" id="user-menu" class="dropdown-toggle btn btn-primary btn-sm m-t-10" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$name.' <span class="caret"></span></button>';

                if (iMEMBER) {
                    echo '<ul class="dropdown-menu" aria-labelledby="user-menu">';
                        echo '<li class="dropdown-header">'.$this->locale['ax9_002'].'</li>';
                        echo '<li><a href="'.BASEDIR.'profile.php?lookup='.$this->userdata['user_id'].'">'.$this->locale['view'].' '.$this->locale['profile'].'</a></li>';
                        echo '<li><a href="'.BASEDIR.'messages.php">'.$this->locale['message'].'</a></li>';

                        echo '<li><a href="'.BASEDIR.'edit_profile.php">'.$this->locale['UM080'].'</a></li>';
                        echo iADMIN ? '<li role="separator" class="divider"></li>' : '';
                        echo iADMIN ? '<li><a href="'.ADMIN.'index.php'.fusion_get_aidlink().'&pagenum=0">'.$this->locale['global_123'].'</a></li>' : '';
                        echo '<li role="separator" class="divider"></li>';
                        echo session_get('login_as') ? '<li><a href="'.BASEDIR.'index.php?logoff='.$this->userdata['user_id'].'">'.$this->locale['UM103'].'</a></li>' : '';
                        echo '<li><a href="'.BASEDIR.'index.php?logout=yes">'.$this->locale['logout'].'</a></li>';
                    echo '</ul>';
                } else {
                    echo '<ul class="dropdown-menu login-menu" aria-labelledby="user-menu">';
                        echo '<li class="dropdown-header">'.$this->locale['ax9_003'].'</li>';
                        echo '<li>';
                            $action_url = FUSION_SELF.(FUSION_QUERY ? '?'.FUSION_QUERY : '');
                            if (isset($_GET['redirect']) && strstr($_GET['redirect'], '/')) {
                                $action_url = cleanurl(urldecode($_GET['redirect']));
                            }

                            echo openform('loginform', 'post', $action_url, ['form_id' => 'login-form']);
                            switch ($this->settings['login_method']) {
                                case 2:
                                    $placeholder = $this->locale['global_101c'];
                                    break;
                                case 1:
                                    $placeholder = $this->locale['global_101b'];
                                    break;
                                default:
                                    $placeholder = $this->locale['global_101a'];
                            }

                            echo form_text('user_name', '', '', ['placeholder' => $placeholder, 'required' => TRUE, 'input_id' => 'username']);
                            echo form_text('user_pass', '', '', ['placeholder' => $this->locale['global_102'], 'type' => 'password', 'required' => TRUE, 'input_id' => 'userpassword']);
                            echo form_checkbox('remember_me', $this->locale['global_103'], '', ['value' => 'y', 'class' => 'm-0', 'reverse_label' => TRUE, 'input_id' => 'rememberme']);
                            echo form_button('login', $this->locale['global_104'], '', ['class' => 'btn-primary btn-sm m-b-5', 'input_id' => 'loginbtn']);
                            echo closeform();
                        echo '</li>';
                        echo '<li>'.str_replace(['[LINK]', '[/LINK]'], ['<a href="'.BASEDIR.'lostpassword.php">', '</a>'], $this->locale['global_106']).'</li>';
                        if ($this->settings['enable_registration']) echo '<li><a href="'.BASEDIR.'register.php">'.$this->locale['register'].'</a></li>';
                    echo '</ul>';
                }
            echo '</li>';
        echo '</ul>';

        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    private function userInfoBar() {
        if (iMEMBER) {
            $this->locale += fusion_get_locale('', LOCALE.LOCALESET.'user_fields.php');
            echo '<ul class="user-info-bar hidden-xs">';
                echo '<li>'.display_avatar($this->userdata, '40px', '', FALSE, 'img-rounded m-t-10 m-l-20 m-r-10').'</li>';

                echo '<li style="margin-left: 50px;"><ul class="info-bar-dropdown list-style-none"><li class="dropdown">';
                    echo '<a id="dduser" class="dropdown-toggle" data-toggle="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">';
                        echo '<h4>'.$this->userdata['user_name'].' <span class="caret"></span></h4>';
                        echo '<small>'.getuserlevel($this->userdata['user_level']).'</small>';
                    echo '</a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="dduser" style="min-width: 320px;"><li class="p-15">';
                        echo '<strong>'.$this->locale['ax9_004'].timer($this->userdata['user_joined']).'</strong>';
                        echo '<div class="row">';
                            echo '<div class="col-xs-6"><small>';
                                echo '<b>'.$this->locale['u066'].':</b> '.showdate('shortdate', $this->userdata['user_joined']).'<br/>';
                                echo '<b>'.$this->locale['u067'].':</b> '.($this->userdata['user_lastvisit'] ? showdate('shortdate', $this->userdata['user_lastvisit']) : $this->locale['u042']).'<br/>';
                                if (column_exists('users', 'user_location')) echo '<b>'.fusion_get_locale('uf_location', LOCALE.LOCALESET.'user_fields/user_location.php').':</b> '.(!empty($this->userdata['user_location']) ? $this->userdata['user_location'] : $this->locale['user_na']).'<br/>';
                            echo '</small></div>';
                            echo '<div class="col-xs-6"><small>';
                                echo '<b>'.fusion_get_locale('uf_comments-stat', LOCALE.LOCALESET.'user_fields/user_comments-stat.php').':</b> '.number_format(dbcount("(comment_id)", DB_COMMENTS, "comment_name='".$this->userdata['user_id']."'")).'<br/>';
                                if (column_exists('users', 'user_posts')) echo '<b>'.fusion_get_locale('uf_forum-stat', LOCALE.LOCALESET.'user_fields/user_forum-stat.php').':</b> '.number_format($this->userdata['user_posts']).'<br/>';
                                echo '<b>'.$this->locale['u049'].':</b> '.$this->userdata['user_ip'].'<br/>';
                            echo '</small></div>';
                        echo '</div>';
                    echo '</li></ul>';
                echo '</li></ul></li>'; // .info-bar-dropdown

                echo '<li class="user-icon"><a href="'.BASEDIR.'profile.php?lookup='.$this->userdata['user_id'].'"><i class="fa icon fa-user"></i></a></li>';

                $msg_count = dbcount(
                    "('message_id')",
                    DB_MESSAGES, "message_to=:my_id AND message_read=:unread AND message_folder=:inbox",
                    [':inbox' => 0, ':my_id' => $this->userdata['user_id'], ':unread' => 0]
                );

                $messages_count = '';
                if ($msg_count > 0) {
                    $messages_count = '<span class="msg-count label label-danger m-l-5">'.$msg_count.'</span>';
                }

                echo '<li><ul class="info-bar-dropdown list-style-none"><li class="dropdown">';
                    echo '<a id="ddmsgs" class="dropdown-toggle" data-toggle="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#"><i class="fa icon fa-envelope"></i>'.$messages_count.' <span class="caret"></span></a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="ddmsgs" style="width: 280px;padding-top: 0;">';
                        echo '<li class="mailbox">';
                            echo '<strong><a href="'.BASEDIR.'messages.php">'.$this->locale['ax9_005'].'</a></strong>';
                        echo '</li>';
                        echo '<li><ul>';
                            $result = dbquery("SELECT *
                                FROM ".DB_MESSAGES."
                                WHERE message_to='".$this->userdata['user_id']."' AND message_user='".$this->userdata['user_id']."' AND message_read='0' AND message_folder='0'
                                ORDER BY message_datestamp DESC LIMIT 0, 5
                            ");

                            if (dbrows($result) > 0) {
                                $i = 0;
                                while ($maildata = dbarray($result)) {
                                    echo '<li style="padding: 3px 10px; '.($i > 0 ? 'border-top: 1px dashed rgba(0,0,0,0.1);' : '').'"><a href="'.BASEDIR.'messages.php?folder=inbox&msg_read='.$maildata['message_id'].'">'.$maildata['message_subject'].'</a><small class="pull-right">'.timer($maildata['message_datestamp']).'</small></li>';
                                    $i++;
                                }
                            } else {
                                echo '<li class="text-center p-10">'.$this->locale['ax9_006'].'</li>';
                                echo '<li class="text-center p-10" style="border-top: 1px dashed rgba(0,0,0,0.1);"><a href="'.BASEDIR.'messages.php">'.$this->locale['ax9_007'].'</a></li>';
                            }
                        echo '</ul></li>';
                    echo '</ul>';
                echo '</li></ul></li>'; // .info-bar-dropdown

                echo '<li><ul class="info-bar-dropdown list-style-none"><li class="dropdown">';
                    echo '<a id="ddugroups" class="dropdown-toggle" style="border-right: 1px solid rgba(0,0,0,0.23);" data-toggle="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#"><i class="fa icon fa-users"></i> <span class="caret"></span></a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="ddugroups" style="width: 250px;">';
                        echo '<li class="dropdown-header"><b>'.$this->locale['u057'].'</b></li>';

                        $user_groups = strpos($this->userdata['user_groups'], ".") == 0 ? substr($this->userdata['user_groups'], 1) : $this->userdata['user_groups'];
                        $user_groups = explode('.', $user_groups);
                        if (!empty($user_groups['0'])) {
                            for ($i = 0; $i < count($user_groups); $i++) {
                                echo '<li><a href="'.BASEDIR.'profile.php?group_id='.$user_groups[$i].'">'.getgroupname($user_groups[$i]).': <small class="text-lighter">'.getgroupname($user_groups[$i], TRUE).'</small></a></li>';
                            }
                        } else {
                            echo '<li class="text-center">'.$this->locale['ax9_008'].'</li>';
                        }
                    echo '</ul>';
                echo '</li></ul></li>'; // .info-bar-dropdown
            echo '</ul>';
        }
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
