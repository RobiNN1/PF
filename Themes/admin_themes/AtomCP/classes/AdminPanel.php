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
namespace AtomCP;

use AtomCP\Panels\ChatPanel;
use PHPFusion\Admins;

class AdminPanel {
    private $messages = [];
    private static $breadcrumbs = FALSE;
    private static $icon = FALSE;
    private $pagenum;

    public function __construct() {
        $locale = fusion_get_locale('', ATOMCP_LOCALE);
        $userdata = fusion_get_userdata();
        $aidlink = fusion_get_aidlink();
        $languages = fusion_get_enabled_languages();

        $this->pagenum = (int)filter_input(INPUT_GET, 'pagenum');

        add_to_head('<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans">');
        add_to_footer('<script src="'.INCLUDES.'jscripts/js.cookie.min.js"></script>');
        add_to_footer('<script src="'.ATOMCP.'assets/scripts.min.js?v='.filemtime(ATOMCP.'assets/scripts.min.js').'"></script>');
        add_to_jquery('search_ajax("'.ADMIN.'includes/acp_search.php'.fusion_get_aidlink().'");');

        echo '<main class="wrapper">';

        echo '<header class="atomcp-navbar"><div class="navbar navbar-inverse navbar-fixed-top">';
            echo '<a href="#" class="sidebar-toggle"><i class="fa fa-fw fa-bars"></i></a>';
            echo '<a href="'.ADMIN.'index.php'.$aidlink.'" class="navbar-brand hidden-xs">AtomCP</a>';

            echo '<ul class="nav navbar-nav user-nav">';
                echo '<li class="dropdown nav-search-dropdown">';
                    echo '<a href="#" id="navbar-search"><input class="form-control" type="text" id="search_pages" name="search_pages" placeholder="'.$locale['search'].'" autocomplete="off"/></a>';
                    echo '<ul class="dropdown-menu scrollable" aria-labelledby="search_pages" id="search_result"></ul>';
                echo '</li>';

                $messages_count = $this->messages();
                $messages_count = !empty($messages_count) ? '<span class="label label-danger msg-count">'.count($messages_count).'</span>' : '';

                echo '<li class="nav-link dropdown messages-menu">';
                    echo '<a id="ddmsgs" href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="'.$locale['message'].'"><i class="fa fa-envelope-o"></i> <span class="caret"></span>'.$messages_count.'</a>';
                    $messages = $this->getMessages();

                    echo '<ul class="dropdown-menu" aria-labelledby="ddmsgs">';
                        echo '<li><ul class="menu">';
                            if (!empty($messages)) {
                                foreach ($messages as $message) {
                                    echo '<li>';
                                        echo '<a href="'.BASEDIR.'messages.php?folder=inbox&msg_read='.$message['link'].'">';
                                            echo '<div class="pull-left">';
                                                echo display_avatar($message['user'], '35px', '', FALSE, 'img-circle');
                                            echo '</div>';
                                            echo '<h4>';
                                                echo $message['user']['user_name'];
                                                echo '<small><i class="fa fa-clock-o"></i> '.$message['datestamp'].'</small>';
                                            echo '</h4>';
                                            echo '<p>'.trim_text($message['title'], 20).'</p>';
                                        echo '</a>';
                                    echo '</li>';
                                }
                            } else {
                                echo '<li class="text-center p-15">'.$locale['cp_001'].'</li>';
                            }
                        echo '</ul></li>';
                        echo '<li class="footer"><a href="'.BASEDIR.'messages.php?msg_send=new" class="text-bold">'.$locale['cp_002'].'</a></li>';
                    echo '</ul>';
                echo '</li>';

                echo '<li class="nav-link dropdown user-menu">';
                    echo '<a id="dduser" href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user-tie"></i><span class="hidden-xs"> '.$userdata['user_name'].'</span> <span class="caret"></span></a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="dduser">';
                        echo '<li><a href="'.BASEDIR.'edit_profile.php"><i class="fa fa-pencil fa-fw"></i> '.$locale['UM080'].'</a></li>';
                        echo '<li><a href="'.BASEDIR.'profile.php?lookup='.$userdata['user_id'].'"><i class="fa fa-eye fa-fw"></i> '.$locale['view'].' '.$locale['profile'].'</a></li>';
                        echo '<li class="divider"></li>';
                        echo '<li><a href="'.FUSION_REQUEST.'&logout">'.$locale['admin-logout'].'</a></li>';
                        echo '<li><a href="'.BASEDIR.'index.php?logout=yes">'.$locale['logout'].'</a></li>';
                    echo '</ul>';
                echo '</li>';

                if (count($languages) > 1) {
                    echo '<li class="nav-link dropdown languages-menu">';
                        echo '<a id="ddlangs" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="'.translate_lang_names(LANGUAGE).'"><i class="fa fa-globe"></i> <span class="caret"></span></a>';
                        echo '<ul class="dropdown-menu" aria-labelledby="ddlangs">';
                            foreach ($languages as $language_folder => $language_name) {
                                echo '<li><a class="display-block" href="'.clean_request('lang='.$language_folder, ['lang'], FALSE).'"><img class="m-r-5" src="'.BASEDIR.'locale/'.$language_folder.'/'.$language_folder.'-s.png" alt="'.$language_folder.'"> '.$language_name.'</a></li>';
                            }
                        echo '</ul>';
                    echo '</li>';
                }

                echo '<li class="nav-link"><a href="'.BASEDIR.'index.php"><i class="fa fa-home"></i></a></li>';

                if (db_exists(DB_ACPCHAT)) {
                    echo '<li class="nav-link" id="chat"><a href="#"><i class="far fa-comments"></i></a></li>';
                }

                echo '<li class="nav-link"><a href="#" class="dark-mode"><i class="fa fa-fw fa-adjust"></i></a></li>';

                if (iSUPERADMIN) {
                    echo '<li class="nav-link"><a href="'.ATOMCP.'settings.php'.$aidlink.'"><i class="fa fa-sliders-h"></i></a></li>';
                }

            echo '</ul>';
        echo '</div></header>';

        echo '<aside id="sidebar">';
            echo '<ul id="sidebar-sections">';

            $admin_sections = Admins::getInstance()->getAdminSections();
            $admin_pages = Admins::getInstance()->getAdminPages();

            foreach ($admin_sections as $i => $section_name) {
                $active = (isset($_GET['pagenum']) && $this->pagenum === $i) || (!$this->pagenum && Admins::getInstance()->isActive() === $i);

                if (!empty($admin_pages[$i])) {
                    echo '<li class="dropdown'.($active ? ' active' : '').'">';
                        echo '<a id="ddsection'.$i.'" class="pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="'.ADMIN.'index.php'.$aidlink.'&pagenum='.$i.'">';
                            echo '<span title="'.$section_name.'" data-toggle="tooltip" data-placement="right">';
                                echo Admins::getInstance()->getAdminSectionIcons($i);
                            echo '</span>';
                        echo '</a>';

                        echo '<ul class="dropdown-menu" aria-labelledby="ddsection'.$i.'">';
                        foreach ($admin_pages[$i] as $data) {
                            if (checkrights($data['admin_rights'])) {
                                $sub_active = $data['admin_link'] == Admins::getInstance()->currentPage();

                                $icon = '<img class="m-r-5 admin-image" src="'.get_image('ac_'.$data['admin_rights']).'" alt="'.$data['admin_title'].'">';

                                if (!empty($admin_pages[$data['admin_rights']])) {
                                    if (checkrights($data['admin_rights'])) {
                                        echo '<li><a href="'.ADMIN.$data['admin_link'].$aidlink.'">'.$data['admin_title'].' '.$icon.'</a></li>';

                                        foreach ($admin_pages[$data['admin_rights']] as $sub_page) {
                                            echo '<li><a class="p-l-20" href="'.$sub_page['admin_link'].'">'.$sub_page['admin_title'].'</a></li>';
                                        }
                                    }
                                } else {
                                    echo '<li'.($sub_active ? ' class="active"' : '').'><a href="'.ADMIN.$data['admin_link'].$aidlink.'">'.$data['admin_title'].' '.$icon.'</a></li>';
                                }
                            }
                        }
                        echo '</ul>';
                    echo '</li>';
                } else {
                    echo '<li class="'.($active ? ' active' : '').'"><a href="'.ADMIN.'index.php'.$aidlink.'&pagenum=0">';
                        echo '<span title="'.$section_name.'" data-toggle="tooltip" data-placement="right">';
                            echo Admins::getInstance()->getAdminSectionIcons($i);
                        echo '</span>';
                    echo '</a></li>';
                }
            }
            echo '</ul>';
        echo '</aside>';

        echo '<div id="content" class="container-fluid">';
            echo '<div class="notices">';
                echo '<div id="updatechecker_result" class="alert alert-info" style="display:none;"></div>';
                echo renderNotices(getnotices());
            echo '</div>';

            echo CONTENT;

            echo '<footer class="p-t-15 p-b-20">';
                echo '<div class="visible-xs">';
                    echo showFooterErrors();

                    if (fusion_get_settings('rendertime_enabled')) {
                        echo showrendertime().showMemoryUsage().'<br />';
                    }
                echo '</div>';

                echo '<span><b style="color: #ff6c2c;">AtomCP</b> &copy; '.date('Y').' '.$locale['cp_004'].' <a href="https://github.com/RobiNN1" target="_blank">RobiNN</a>. Icons by <a href="https://icons8.com/" target="_blank">Icons8</a></span>';
                echo '<br/>'.showcopyright('', TRUE);
            echo '</footer>';
        echo '</div>';

        $chat = new ChatPanel();
        $chat->displayChat();

        echo '</main>';

        $debugger = new Debugger();
        $debugger->show(TRUE);
    }

    private function messages() {
        $userdata = fusion_get_userdata();

        $result = dbquery("
            SELECT message_id, message_subject, message_from user_id, u.user_name, u.user_status, u.user_avatar, u.user_lastvisit, message_datestamp
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
                            'user_id'        => $data['user_id'],
                            'user_name'      => $data['user_name'],
                            'user_status'    => $data['user_status'],
                            'user_avatar'    => $data['user_avatar'],
                            'user_lastvisit' => $data['user_lastvisit']
                        ],
                        'datestamp' => timer($data['message_datestamp'])
                    ];
                }
            }
        }

        return $this->messages;
    }

    private function getMessages() {
        return $this->messages;
    }

    private static function getPageIcon() {
        $sections = Admins::getInstance()->getAdminSections();
        $pages = Admins::getInstance()->getAdminPages();
        $current_page = Admins::getInstance()->getCurrentPage();

        if (!empty($sections) && !empty($pages)) {
            $pages = flatten_array($pages);

            if (!empty($current_page)) {
                foreach ($pages as $page_data) {
                    if ($page_data['admin_link'] == $current_page) {
                        return '<img class="img-responsive display-inline-block m-r-10" style="width: 35px; height: 35px;" src="'.get_image('ac_'.$page_data['admin_rights']).'" alt="'.$page_data['admin_title'].'"/>';
                    }
                }
            }
        }

        return NULL;
    }

    public static function openTable($title = FALSE, $class = NULL) {
        $html = '<section class="opentable">';
        if (!empty($title)) {
            $html .= '<div class="content-header">';

            $html .= '<h1>';
                if (self::$icon == FALSE) {
                    $html .= self::getPageIcon();
                    self::$icon = TRUE;
                }
                $html .= $title;
            $html .= '</h1>';

            if (self::$breadcrumbs == FALSE) {
                $html .= render_breadcrumbs();
                self::$breadcrumbs = TRUE;
            }
            $html .= '</div>';
        }

        $html .= '<div class="section-content '.$class.'">';

        echo $html;
    }

    public static function closeTable() {
        echo '</div>';
        echo '</section>';
    }
}
