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
namespace MDashboard;

use PHPFusion\Admins;

class AdminPanel {
    private $messages = [];
    private static $breadcrumbs = FALSE;
    private $pagenum;

    public function __construct() {
        $this->pagenum = (int)filter_input(INPUT_GET, 'pagenum');

        add_to_footer('<script type="text/javascript" src="'.INCLUDES.'jscripts/js.cookie.min.js"></script>');
        add_to_footer('<script type="text/javascript" src="'.MD.'assets/js/scripts.min.js"></script>');
        add_to_head('<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Noto+Sans:400,400i,700,700i&subset=cyrillic,cyrillic-ext,devanagari,greek,greek-ext,latin-ext,vietnamese"/>');
        add_to_head('<link rel="stylesheet" type="text/css" href="'. MD.'assets/mCustomScrollbar/jquery.mCustomScrollbar.min.css"/>');
        add_to_footer('<script type="text/javascript" src="'. MD.'assets/mCustomScrollbar/jquery.mCustomScrollbar.min.js"></script>'); // https://github.com/malihu/malihu-custom-scrollbar-plugin
        add_to_head('<script src="'.MD.'assets/js/jquery.mousewheel.min.js"></script>');

        $html = $this->navigation();
        $html .= $this->sidebar();

        $html .= '<div class="content-wrapper animate">';
            $html .= '<div id="updatechecker_result" class="alert alert-info m-b-0" style="display:none;"></div>';

            if (function_exists('renderNotices') && function_exists('getnotices')) {
                $html .= renderNotices(getnotices());
            }

            $html .= CONTENT;

            $html .= $this->footer();
        $html .= '</div>';

        echo $html;
    }

    private function sidebar() {
        $locale = fusion_get_locale('', MD_LOCALE);
        $userdata = fusion_get_userdata();

        $html = '<aside class="sidebar animate"><div class="sidebar-content">';
            $html .= '<div class="logo overflow-hide"><i class="phpfusion-icon"></i><span>'.$locale['md_001'].'</span></div>';

            $html .= '<ul class="user-actions">';
                $html .= '<li class="dropdown">';
                    $html .= '<a id="user-actions" class="icon-down" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" title="'.$userdata['user_name'].'"><i class="fa fa-user"></i></a>';
                    $html .= '<ul class="dropdown-menu m-t-10" aria-labelledby="user-actions">';
                        $html .= '<li><a href="'.BASEDIR.'edit_profile.php"><i class="fa fa-pencil fa-fw"></i> '.$locale['UM080'].'</a></li>';
                        $html .= '<li><a href="'.BASEDIR.'profile.php?lookup='.$userdata['user_id'].'"><i class="fa fa-eye fa-fw"></i> '.$locale['view'].' '.$locale['profile'].'</a></li>';
                        $html .= '<li class="divider"></li>';
                        $html .= IS_V9 ? '<li><a href="'.FUSION_REQUEST.'&logout"><i class="fa fa-sign-out fa-fw"></i> '.$locale['admin-logout'].'</a></li>' : '';
                        $html .= '<li><a href="'.BASEDIR.'index.php?logout=yes"><i class="fa fa-sign-out fa-fw"></i> <span class="text-danger">'.$locale['logout'].'</span></a></li>';
                    $html .= '</ul>';
                $html .= '</li>';

                $languages = fusion_get_enabled_languages();
                if (count($languages) > 1) {
                    $html .= '<li class="dropdown languages-menu">';
                        $html .= '<a id="languages-list" class="icon-down" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" title="'.translate_lang_names(LANGUAGE).'"><i class="fa fa-globe"></i></a>';
                        $html .= '<ul class="dropdown-menu m-t-10" aria-labelledby="languages-list">';
                            foreach ($languages as $language_folder => $language_name) {
                                $html .= '<li><a class="display-block" href="'.clean_request('lang='.$language_folder, ['lang'], FALSE).'"><img class="m-r-5" src="'.BASEDIR.'locale/'.$language_folder.'/'.$language_folder.'-s.png" alt="'.$language_folder.'"/> '.$language_name.'</a></li>';
                            }
                        $html .= '</ul>';
                    $html .= '</li>';
                }

                $messages_count = $this->messages();
                $messages_count = !empty($messages_count) ? '<span class="label label-danger msg-count">'.count($messages_count).'</span>' : '';

                $html .= '<li><a '.(!empty($messages_count) ? 'id="messages-box"' : '').' href="'.BASEDIR.'messages.php" title="'.$locale['message'].'"><i class="fa fa-envelope"></i>'.$messages_count.'</a></li>';
                $html .= '<li><a href="'.BASEDIR.'index.php" title="'.fusion_get_settings('sitename').'"><i class="fa fa-home"></i></a></li>';
            $html .= '</ul>';

            $html .= '<div id="sidebar-menu">';
                if (class_exists('\PHPFusion\AdminSearch')) {
                    $html .= '<div class="sidebar-form">';
                        $html .= '<input type="text" id="search_pages" name="search_pages" class="form-control" placeholder="'.$locale['md_002'].'">';
                        $html .= '<i class="fa fa-search input-search-icon"></i>';
                    $html .= '</div>';
                    $html .= '<ul class="admin-vertical-link" id="search_result" style="display: none;"></ul>';
                    $html .= '<img id="ajax-loader" style="width: 30px; display: none;" class="img-responsive center-x m-t-10" alt="Ajax Loader" src="'.IMAGES.'loader.svg"/>';
                }

                $html .= Admins::getInstance()->vertical_admin_nav(TRUE);
            $html .= '</div>';

            $messages = $this->getMessages();

            if (!empty($messages)) {
                $html .= '<ul id="pms-box" style="display: none;">';
                foreach ($messages as $message) {
                    $html .= '<li>';
                        $html .= '<div class="msg-block">';
                            $html .= '<div class="clearfix">';
                                $html .= $message['user']['user_name'];
                                $html .= '<br/><a href="'.BASEDIR.'messages.php?folder=inbox&msg_read='.$message['link'].'" title="'.$message['title'].'">'.trim_text($message['title'], 18).'</a><small class="pull-right msg-date">'.$message['datestamp'].'</small>';
                            $html .= '</div>';
                        $html .= '</div>';
                    $html .= '</li>';
                }
                $html .= '</ul>';
            }
        $html .= '</div></aside>';

        if (class_exists('\PHPFusion\AdminSearch')) {
            add_to_jquery('$("#search_pages").bind("keyup", function (e) {
                $.ajax({
                    url: "'.ADMIN.'includes/acp_search.php'.fusion_get_aidlink().'",
                    method: "get",
                    data: $.param({"pagestring": $(this).val()}),
                    dataType: "json",
                    beforeSend: function () {
                        $("#ajax-loader").show();
                    },
                    success: function (e) {
                        if ($("#search_pages").val() == "") {
                            $("#adl").show();
                            $("#search_result").html(e).hide();
                            $("#search_result li").html(e).hide();
                        } else {
                            var result = "";

                            if (!e.status) {
                                $.each(e, function (i, data) {
                                    if (data) {
                                        result += "<li><a href=\"" + data.link + "\"><img class=\"admin-image\" alt=\"" + data.title + "\" src=\"" + data.icon + "\"/> " + data.title + "</a></li>";
                                    }
                                });
                            } else {
                                result = "<li class=\"text-center\"><span class=\"p-5\">" + e.status + "</span></li>";
                            }

                            $("#search_result").html(result).show();
                            $("#adl").hide();
                        }
                    },
                    complete: function () {
                        $("#ajax-loader").hide();
                    }
                });
            });');
        }

        return $html;
    }

    private function navigation() {
        $html = '<nav class="topnav animate">';
            $html .= '<ul class="left top-navbar navbar-left pull-left m-b-0">';
                $html .= '<li><a href="#" id="toggle-sidebar" title="Sidebar"><i class="fa fa-bars"></i></a></li>';
            $html .= '</ul>';

            if (IS_V9) {
                $html .= $this->getPageTitle();
            }

            $html .= '<ul class="right top-navbar navbar-right pull-right m-r-5 m-b-0">';
                $sections = Admins::getInstance()->getAdminSections();
                if (!empty($sections)) {
                    $i = 0;
                    foreach ($sections as $section_name) {
                        $active = ((isset($_GET['pagenum']) && $this->pagenum === $i) || (!$this->pagenum && Admins::getInstance()->_isActive() === $i));
                        $html .= '<li'.($active ? ' class="active"' : '').'><a href="'.ADMIN.'index.php'.fusion_get_aidlink().'&pagenum='.$i.'" title="'.$section_name.'">'.Admins::getInstance()->get_admin_section_icons($i).'</a></li>';
                        $i++;
                    }
                }
            $html .= '</ul>';
        $html .= '</nav>';

        return $html;
    }

    private function footer() {
        $locale = fusion_get_locale();

        $html = '<footer class="main-footer">';
            if (function_exists('showFooterErrors')) {
                $html .= showFooterErrors();
            } else {
                if (!IS_V9) {
                    global $_errorHandler;

                    if (iADMIN && checkrights('ERRO') && count($_errorHandler) > 0) {
                        $html .= '<div>'.str_replace('[ERROR_LOG_URL]', ADMIN.'errors.php'.fusion_get_aidlink(), $locale['err_101']).'</div>';
                    }
                }
            }

            if (fusion_get_settings('rendertime_enabled')) {
                $html .= showrendertime().' '.showMemoryUsage().'<br />';
            }

            $html .= '<strong>MDashboard Theme &copy; '.date('Y').' '.$locale['md_003'].' <a href="https://github.com/RobiNN1" target="_blank">RobiNN</a></strong>';
            $html .= '<br/>'.str_replace('<br />', ' | ', showcopyright());
        $html .= '</footer>';

        return $html;
    }

    private function getPageTitle() {
        $sections = Admins::getInstance()->getAdminSections();
        $pages = Admins::getInstance()->getAdminPages();
        $current_page = Admins::getInstance()->getCurrentPage();

        if (!empty($sections) && !empty($pages)) {
            $pages = flatten_array($pages);

            if (!empty($current_page)) {
                foreach ($pages as $page_data) {
                    if ($page_data['admin_link'] == $current_page) {
                        return '<div class="page-title hidden-xs hidden-sm"><img class="img-responsive" src="'.get_image('ac_'.$page_data['admin_rights']).'" alt="'.$page_data['admin_title'].'"/> <span>'.$page_data['admin_title'].'</span></div>';
                    }
                }
            }
        }

        return NULL;
    }

    private function messages() {
        $userdata = fusion_get_userdata();

        if (column_exists(DB_MESSAGES, 'message_user')) {
            $msg_user = 'message_user';
        } else {
            $msg_user = 'message_to';
        }

        $result = dbquery("
            SELECT message_id, message_subject, message_from user_id, u.user_name, u.user_status, u.user_avatar, u.user_lastvisit, message_datestamp
            FROM ".DB_MESSAGES."
            INNER JOIN ".DB_USERS." u ON u.user_id=message_from
            WHERE message_to='".$userdata['user_id']."' AND ".$msg_user."='".$userdata['user_id']."' AND message_read='0' AND message_folder='0'
            GROUP BY message_id
            ORDER BY message_datestamp DESC
        ");

        if (dbcount("(message_id)", DB_MESSAGES, "message_to='".$userdata['user_id']."' AND ".$msg_user."='".$userdata['user_id']."' AND message_read='0' AND message_folder='0'")) {
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

    public static function openTable($title = FALSE, $class = NULL) {
        $html = '';

        if (!empty($title)) {
            $html .= '<section class="content-header">';
            $html .= '<h1>'.$title.'</h1>';

            if (self::$breadcrumbs == FALSE) {
                $html .= render_breadcrumbs();
                self::$breadcrumbs = TRUE;
            }
            $html .= '</section>';
        }

        $html .= '<section class="content '.$class.'">';

        echo $html;
    }

    public static function closeTable() {
        echo '</section>';
    }
}
