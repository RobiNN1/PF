<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: AdminPanel.php
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
namespace MaterialAdmin;

use PHPFusion\Admins;

class AdminPanel {
    private $messages = [];
    private $pagenum;

    public function __construct() {
        $locale = fusion_get_locale('', MDT_LOCALE);

        $this->pagenum = (int)filter_input(INPUT_GET, 'pagenum');

        add_to_head('<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Noto+Sans"/>');
        add_to_footer('<script src="'.INCLUDES.'jscripts/js.cookie.min.js"></script>');
        add_to_footer('<script src="'.MDT.'assets/js/scripts.min.js"></script>');
        add_to_head('<link rel="stylesheet" href="'.MDT.'assets/mCustomScrollbar/jquery.mCustomScrollbar.min.css"/>');
        add_to_footer('<script src="'.MDT.'assets/mCustomScrollbar/jquery.mCustomScrollbar.min.js"></script>');
        add_to_head('<script src="'.MDT.'assets/js/jquery.mousewheel.min.js"></script>');
        add_to_jquery('$(".sidebar, .sidebar-sm .admin-submenu, .sidebar-sm .search-box, .messages-box").mCustomScrollbar({theme: "minimal-dark", axis: "y", scrollInertia: 550, mouseWheel: {enable: !0, axis: "y", preventDefault: !0}});');

        $html = '<main class="clearfix">';
            $html .= $this->topMenu();
            $html .= $this->sidebar();

            $html .= '<div class="content">';
                $html .= '<ul id="nav-sections" class="nav nav-tabs nav-justified hidden-lg" style="margin-bottom: 20px;">';
                    $sections = Admins::getInstance()->getAdminSections();
                    if (!empty($sections)) {
                        $i = 0;
                        foreach ($sections as $section_name) {
                            $active = (isset($_GET['pagenum']) && $this->pagenum === $i) || (!$this->pagenum && Admins::getInstance()->_isActive() === $i);
                            $html .= '<li'.($active ? ' class="active"' : '').'><a href="'.ADMIN.'index.php'.fusion_get_aidlink().'&pagenum='.$i.'"><span class="visible-xs">'.Admins::getInstance()->get_admin_section_icons($i).'</span><span class="hidden-xs">'.$section_name.'</span></a></li>';
                            $i++;
                        }
                    }
                $html .= '</ul>';

                $html .= '<div class="hidden-xs">';
                    $html .= render_breadcrumbs();
                $html .= '</div>';

                $html .= '<div id="updatechecker_result" class="alert alert-info" style="display:none;"></div>';
                $html .= renderNotices(getnotices());
                $html .= CONTENT;

                $html .= '<footer class="copyright">';
                    if (fusion_get_settings('rendertime_enabled')) {
                        $html .= showrendertime().showMemoryUsage().'<br />';
                    }

                    $html .= 'Material Admin Theme &copy; '.date('Y').' '.$locale['material_013'].' <a href="https://github.com/RobiNN1" target="_blank">RobiNN</a> | '.showcopyright('', TRUE);
                $html .= '</footer>';

                $errors = showFooterErrors();
                if ($errors) {
                    $html .= '<div class="errors fixed">'.$errors.'</div>';
                }
            $html .= '</div>';

            if (!$this->isMobile()) {
                $html .= $this->messagesBox();
                $html .= $this->themeSettings();
            }
        $html .= '</main>';

        $html .= '<div class="overlay"></div>';

        echo $html;
    }

    private function topMenu() {
        $locale = fusion_get_locale();
        $aidlink = fusion_get_aidlink();
        $settings = fusion_get_settings();
        $userdata = fusion_get_userdata();

        $html = '<div class="top-menu navbar fixed">';
            $html .= '<div class="toggleicon" data-action="togglemenu"><span></span></div>';
            $html .= '<div class="brand"><img src="'.IMAGES.'phpfusion-icon.png" alt="PHP Fusion 9"/> PHP Fusion 9</div>';
            $html .= '<div class="pull-right hidden-sm hidden-md hidden-lg home-xs"><a title="'.$settings['sitename'].'" href="'.BASEDIR.'index.php"><i class="fa fa-home"></i></a></div>';

            $html .= '<ul class="nav navbar-nav navbar-left hidden-xs hidden-sm hidden-md">';
                $sections = Admins::getInstance()->getAdminSections();
                if (!empty($sections)) {
                    $i = 0;

                    foreach ($sections as $section_name) {
                        $active = (isset($_GET['pagenum']) && $this->pagenum === $i) || (!$this->pagenum && Admins::getInstance()->_isActive() === $i);
                        $html .= '<li'.($active ? ' class="active"' : '').'><a href="'.ADMIN.'index.php'.$aidlink.'&pagenum='.$i.'" data-toggle="tooltip" data-placement="bottom" title="'.$section_name.'">'.Admins::getInstance()->get_admin_section_icons($i).'</a></li>';
                        $i++;
                    }
                }

            $html .= '</ul>';

            $html .= '<ul class="nav navbar-nav navbar-right hidden-xs">';
                $languages = fusion_get_enabled_languages();
                if (count($languages) > 1) {
                    $html .= '<li class="dropdown languages-switcher">';
                        $html .= '<a id="ddlangs" class="dropdown-toggle pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="'.$locale['282'].'"><i class="fa fa-globe"></i><img class="current" src="'.BASEDIR.'locale/'.LANGUAGE.'/'.LANGUAGE.'.png" alt="'.translate_lang_names(LANGUAGE).'"/><span class="caret"></span></a>';
                        $html .= '<ul class="dropdown-menu" aria-labelledby="ddlangs">';
                            foreach ($languages as $language_folder => $language_name) {
                                $html .= '<li><a class="display-block" href="'.clean_request('lang='.$language_folder, ['lang'], FALSE).'"><img class="m-r-5" src="'.BASEDIR.'locale/'.$language_folder.'/'.$language_folder.'-s.png" alt="'.$language_folder.'"/> '.$language_name.'</a></li>';
                            }
                        $html .= '</ul>';
                    $html .= '</li>';
                }

                $html .= '<li class="dropdown user-s">';
                    $html .= '<a id="dduser" href="#" class="dropdown-toggle pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.display_avatar($userdata, '30px', '', FALSE, 'img-rounded').' <strong>'.$userdata['user_name'].'</strong><span class="caret"></span></a>';
                    $html .= '<ul class="dropdown-menu" aria-labelledby="dduser" role="menu">';
                        $html .= '<li><a href="'.BASEDIR.'edit_profile.php"><i class="fa fa-pencil fa-fw"></i> '.$locale['UM080'].'</a></li>';
                        $html .= '<li><a href="'.BASEDIR.'profile.php?lookup='.$userdata['user_id'].'"><i class="fa fa-eye fa-fw"></i> '.$locale['view'].' '.$locale['profile'].'</a></li>';
                        $html .= '<li class="divider"></li>';
                        $html .= '<li><a href="'.FUSION_REQUEST.'&logout"><i class="fa fa-sign-out fa-fw"></i> '.$locale['admin-logout'].'</a></li>';
                        $html .= '<li><a href="'.BASEDIR.'index.php?logout=yes"><i class="fa fa-sign-out fa-fw"></i> <span class="text-danger">'.$locale['logout'].'</span></a></li>';
                    $html .= '</ul>';
                $html .= '</li>';

                $messages = $this->messages();
                $messages = !empty($messages) ? '<span class="label label-danger messages">'.count($messages).'</span>' : '';

                if ($this->isMobile()) {
                    $html .= '<li><a title="'.$locale['message'].'" href="'.BASEDIR.'messages.php"><i class="fa fa-envelope-o"></i>'.$messages.'</a></li>';
                } else {
                    $html .= '<li><a title="'.$locale['message'].'" href="#" data-action="messages"><i class="fa fa-envelope-o"></i>'.$messages.'</a></li>';
                }

                $html .= '<li><a title="'.$settings['sitename'].'" href="'.BASEDIR.'index.php"><i class="fa fa-home"></i></a></li>';
            $html .= '</ul>';
        $html .= '</div>';

        return $html;
    }

    private function sidebar() {
        $locale = fusion_get_locale('', MDT_LOCALE);

        $html = '<aside class="sidebar fixed">';
            $html .= '<div class="header fixed hidden-xs hidden-sm hidden-md">';
                $html .= '<div class="pf-logo"></div>';
                $html .= '<div class="version">PHP Fusion 9</div>';
            $html .= '</div>';

            $html .= '<div class="sidebar-menu">';
                $html .= '<div class="search-box">';
                    $html .= '<i class="fa fa-search input-search-icon"></i>';
                    $html .= '<input type="text" id="search_box" name="search_box" class="form-control" placeholder="'.$locale['material_001'].'"/>';
                    $html .= '<ul id="search_result" style="display: none;"></ul>';
                    $html .= '<img id="ajax-loader" style="width: 30px; display: none;" class="img-responsive center-x m-t-10" alt="Ajax Loader" src="'.IMAGES.'loader.svg"/>';
                $html .= '</div>';

                $html .= Admins::getInstance()->vertical_admin_nav(TRUE);
            $html .= '</div>';
        $html .= '</aside>';

        add_to_jquery('search_ajax("'.ADMIN.'includes/acp_search.php'.fusion_get_aidlink().'");');

        return $html;
    }

    public function themeSettings() {
        $locale = fusion_get_locale('', MDT_LOCALE);

        $html = '<aside id="theme-settings" class="hidden-xs">';
            $html .= '<a href="#" title="'.$locale['material_002'].'" data-action="theme-settings" class="btn-theme-settings cogs-animation">';
                $html .= '<i class="fa fa-cog fa-spin"></i>';
                $html .= '<i class="fa fa-cog fa-spin"></i>';
                $html .= '<i class="fa fa-cog fa-spin"></i>';
            $html .= '</a>';

            $html .= '<div class="settings-box">';
                $html .= '<h4>'.$locale['material_002'].'</h4>';

                $html .= '<ul class="settings-menu">';
                    $theme_settings = [
                        ['name' => 'hide-sidebar',      'title' => '003'],
                        ['name' => 'sidebar-sm',        'title' => '004'],
                        ['name' => 'fixedmenu',         'title' => '005', 'toggle' => 'on'],
                        ['name' => 'fixedsidebar',      'title' => '006', 'toggle' => 'on']
                    ];

                    foreach ($theme_settings as $setting) {
                        $html .= '<li><a href="#" data-action="'.$setting['name'].'" id="'.$setting['name'].'">'.$locale['material_'.$setting['title']].'<div class="btn-toggle pull-right '.(!empty($setting['toggle']) ? $setting['toggle'] : '').'"></div></a></li>';
                    }
                $html .= '</ul>';
            $html .= '</div>';
        $html .= '</aside>';

        return $html;
    }

    public function messagesBox() {
        $locale = fusion_get_locale('', MDT_LOCALE);

        $html = '<aside class="messages-box hidden-xs">';
            $html .= '<div class="button-group">';
                $html .= '<a href="#" id="messages-box-close">'.fusion_get_locale('close').'</a>';
                $html .= '<a href="'.BASEDIR.'messages.php?msg_send=new" class="new-message">'.$locale['material_011'].'</a>';
            $html .= '</div>';
            $html .= '<h3 class="title">'.$locale['material_009'].'</h3>';

            $messages = $this->getMessages();
            if (!empty($messages)) {
                $html .= '<ul>';
                    foreach ($messages as $message) {
                        $html .= '<li>';
                            $html .= '<div class="message-block">';
                                $html .= display_avatar($message['user'], '40px', '', FALSE, 'avatar m-r-5');
                                $html .= '<div class="block">';
                                    $html .= '<span class="title">'.$message['user']['user_name'].' <small>'.$message['datestamp'].'</small></span>';
                                    $html .= '<br /><small>'.trim_text($message['title'], 20).'</small>';
                                    $html .= '<a href="'.BASEDIR.'messages.php?folder=inbox&msg_read='.$message['link'].'" class="read-message">'.$locale['material_010'].'</a>';
                                $html .= '</div>';
                            $html .= '</div>';
                        $html .= '</li>';
                    }
                $html .= '</ul>';
            } else {
                $html .= '<div class="no-messages">';
                    $html .= '<i class="fa fa-envelope icon"></i><br />';
                    $html .= $locale['material_012'];
                $html .= '</div>';
            }
        $html .= '</aside>';

        return $html;
    }

    public function messages() {
        $userdata = fusion_get_userdata();

        $result = dbquery("
            SELECT message_id, message_subject, message_from user_id, u.user_name, u.user_status, u.user_avatar, message_datestamp
            FROM ".DB_MESSAGES."
            INNER JOIN ".DB_USERS." u ON u.user_id=message_from
            WHERE message_to='".$userdata['user_id']."' AND message_user='".$userdata['user_id']."' AND message_read='0' AND message_folder='0'
            GROUP BY message_id
            ORDER BY message_datestamp DESC
        ");

        if (dbcount("(message_id)", DB_MESSAGES, "message_to='".$userdata['user_id']."' AND message_user='".$userdata['user_id']."' AND message_read='0' AND message_folder='0'")) {
            if (dbrows($result) > 0) {
                while ($data = dbarray($result)) {
                    $this->messages[] = [
                        'link'      => $data['message_id'],
                        'title'     => $data['message_subject'],
                        'user'      => [
                            'user_id'     => $data['user_id'],
                            'user_name'   => $data['user_name'],
                            'user_status' => $data['user_status'],
                            'user_avatar' => $data['user_avatar']
                        ],
                        'datestamp' => timer($data['message_datestamp'])
                    ];
                }
            }
        }

        return $this->messages;
    }

    public function getMessages() {
        return $this->messages;
    }

    public function isMobile() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER['HTTP_USER_AGENT']);
    }
}
