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
namespace AdminLTE3;

use PHPFusion\Admins;

class AdminPanel {
    protected static $instance = NULL;
    private $messages = [];
    private $pagenum;
    private static $breadcrumbs = FALSE;

    public function __construct() {
        add_to_head('<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700">');
        add_to_footer('<script type="text/javascript" src="'.ADMINLTE3.'js/adminlte.min.js"></script>');

        $this->pagenum = (int)filter_input(INPUT_GET, 'pagenum');

        $html = '<div class="wrapper">';
            $html .= $this->mainHeader();
            $html .= $this->mainSidebar();

            $html .= '<div class="content-wrapper">';
                $html .= '<div class="notices">';
                    $html .= '<div id="updatechecker_result" class="alert alert-info m-b-0" style="display:none;"></div>';
                    $html .= renderNotices(getnotices());
                $html .= '</div>';

                $html .= CONTENT;
            $html .= '</div>';

            $html .= $this->mainFooter();
        $html .= '</div>';

        echo $html;
    }

    private function mainHeader() {
        $aidlink = fusion_get_aidlink();

        $html = '<nav class="main-header navbar navbar-expand navbar-white navbar-light">';
            $html .= '<ul class="navbar-nav">';
                $html .= '<li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li>';

                $sections = Admins::getInstance()->getAdminSections();
                if (!empty($sections)) {
                    $i = 0;

                    foreach ($sections as $section_name) {
                        $active = (isset($_GET['pagenum']) && $this->pagenum === $i) || (!$this->pagenum && Admins::getInstance()->_isActive() === $i);
                        $html .= '<li class="nav-item d-none d-sm-inline-block'.($active ? ' active' : '').'"><a class="nav-link" href="'.ADMIN.'index.php'.$aidlink.'&pagenum='.$i.'" data-toggle="tooltip" data-placement="bottom" title="'.$section_name.'">'.Admins::getInstance()->get_admin_section_icons($i).'</a></li>';
                        $i++;
                    }
                }
            $html .= '</ul>';

            $html .= '<ul class="navbar-nav ml-auto">';
                $languages = fusion_get_enabled_languages();
                if (count($languages) > 1) {
                    $html .= '<li class="nav-item dropdown languages-menu">';
                        $html .= '<a id="ddlangs" href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                            $html .= '<i class="fa fa-globe"></i> <img style="margin-top: -3px;" src="'.BASEDIR.'locale/'.LANGUAGE.'/'.LANGUAGE.'-s.png" alt="'.translate_lang_names(LANGUAGE).'"/>';
                            $html .= '<span class="caret"></span>';
                        $html .= '</a>';
                        $html .= '<ul class="dropdown-menu" aria-labelledby="ddlangs">';
                            foreach ($languages as $language_folder => $language_name) {
                                $html .= '<li class="dropdown-item"><a class="display-block" href="'.clean_request('lang='.$language_folder, ['lang'], FALSE).'"><img class="m-r-5" src="'.BASEDIR.'locale/'.$language_folder.'/'.$language_folder.'-s.png" alt="'.$language_folder.'"/> '.$language_name.'</a></li>';
                            }
                        $html .= '</ul>';
                    $html .= '</li>';
                }

                $html .= $this->messagesMenu();
                $html .= $this->userMenu();

                $html .= '<li class="nav-item"><a class="nav-link" href="'.BASEDIR.'index.php"><i class="fa fa-home"></i></a></li>';
            $html .= '</ul>';
        $html .= '</nav>';

        return $html;
    }

    private function messagesMenu() {
        $locale = fusion_get_locale();
        $messages = $this->messages();
        $msg_icon = !empty($messages) ? '<span class="badge badge-danger navbar-badge">'.count($messages).'</span>' : '';

        $html = '<li class="nav-item dropdown messages-menu">';
            $html .= '<a id="ddmsg" href="'.BASEDIR.'messages.php" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                $html .= '<i class="fa fa-envelope-o"></i>'.$msg_icon;
                $html .= '<span class="caret"></span>';
            $html .= '</a>';
            $html .= '<ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right" aria-labelledby="ddmsg">';
                if (!empty($messages)) {
                    foreach ($messages as $message) {
                        $html .= '<li class="dropdown-item">';
                            $html .= '<a href="'.BASEDIR.'messages.php?folder=inbox&msg_read='.$message['link'].'">';
                                $html .= '<div class="media">';
                                    $html .= display_avatar($message['user'], '40px', '', FALSE, 'img-size-50 mr-3 img-circle');
                                    $html .= '<div class="media-body">';
                                        $html .= '<h3 class="dropdown-item-title">'.$message['user']['user_name'].'</h3>';
                                        $html .= '<p class="text-sm">'.trim_text($message['title'], 20).'</p>';
                                        $html .= '<p class="text-sm text-muted"><i class="fa fa-clock-o"></i> '.$message['datestamp'].'</p>';
                                    $html .= '</div>';
                                $html .= '</div>';
                            $html .= '</a>';
                        $html .= '</li>';
                    }
                } else {
                    $html .= '<li class="dropdown-item text-center">'.$locale['global_460'].'</li>';
                }
                $html .= '<li class="dropdown-item text-center"><a href="'.BASEDIR.'messages.php?msg_send=new" class="text-bold">'.$locale['send_message'].'</a></li>';
            $html .= '</ul>';
        $html .= '</li>';

        return $html;
    }

    private function userMenu() {
        $locale = fusion_get_locale();
        $userdata = fusion_get_userdata();

        $html = '<li class="nav-item dropdown user user-menu">';
            $html .= '<a id="dduser" href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$userdata['user_name'].'<span class="caret"></span></a>';
            $html .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dduser">';
                $html .= '<li class="dropdown-item"><a href="'.BASEDIR.'edit_profile.php"><i class="fa fa-pencil fa-fw"></i> '.$locale['UM080'].'</a></li>';
                $html .= '<li class="dropdown-item"><a href="'.BASEDIR.'profile.php?lookup='.$userdata['user_id'].'"><i class="fa fa-eye fa-fw"></i> '.$locale['view'].' '.$locale['profile'].'</a></li>';
                $html .= '<li class="dropdown-divider"></li>';
                $html .= '<li class="dropdown-item"><a href="'.FUSION_REQUEST.'&logout"><i class="fa fa-sign-out fa-fw"></i> '.$locale['admin-logout'].'</a></li>';
                $html .= '<li class="dropdown-item"><a href="'.BASEDIR.'index.php?logout=yes"><i class="fa fa-sign-out fa-fw"></i> <span class="text-danger">'.$locale['logout'].'</span></a></li>';
            $html .= '</ul>';
        $html .= '</li>';

        return $html;
    }

    private function mainSidebar() {
        $locale = fusion_get_locale();
        $aidlink = fusion_get_aidlink();
        $userdata = fusion_get_userdata();
        $useronline = $userdata['user_lastvisit'] >= time() - 900;

        $html = '<aside class="main-sidebar sidebar-dark-primary elevation-4">';
            $html .= '<a href="'.ADMIN.'index.php'.$aidlink.'" class="brand-link text-center"><span class="brand-text font-weight-light">PHPFusion</span></a>';
            $html .= '<div class="sidebar">';
                $html .= '<div class="user-card mt-3 pb-3 mb-3 d-flex">';
                    $html .= '<div class="image">';
                        $html .= display_avatar($userdata, '2.1rem', '', FALSE, 'img-circle elevation-2');
                    $html .= '</div>';
                    $html .= '<div class="info">';
                        $html .= '<span class="d-block text-white">'.$userdata['user_name'].'</span>';
                        $html .= '<a class="d-block online-status" href="#">';
                            $html .= '<i class="fa fa-circle '.($useronline ? 'text-success' : 'text-danger').'"></i> ';
                            $html .= $useronline ? $locale['online'] : $locale['offline'];
                        $html .= '</a>';
                    $html .= '</div>';
                $html .= '</div>';

                $html .= '<div class="sidebar-form">';
                    $html .= '<input type="text" id="search_pages" name="search_pages" class="form-control" placeholder="'.$locale['search'].'">';
                $html .= '</div>';
                $html .= '<nav class="mt-2"><ul class="sidebar-menu nav nav-pills nav-sidebar flex-column" id="search_result" style="display: none;"></ul></nav>';
                $html .= '<img id="ajax-loader" style="width: 30px; display: none;" class="img-responsive center-x m-t-10" alt="Ajax Loader" src="'.ADMINLTE3.'images/loader.svg"/>';

                $this->searchAjax();

                $html .= $this->sidebarMenu();

            $html .= '</div>';
        $html .= '</aside>';

        return $html;
    }

    private function searchAjax() {
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
                                    result += "<li class=\"nav-item\"><a class=\"nav-link\" href=\"" + data.link + "\"><img class=\"nav-icon\" alt=\"" + data.title + "\" src=\"" + data.icon + "\"/> " + data.title + "</a></li>";
                                }
                            });
                        } else {
                            result = "<li class=\"nav-item text-center text-white\">" + e.status + "</li>";
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

    private function sidebarMenu() {
        $aidlink = fusion_get_aidlink();
        $admin_sections = Admins::getInstance()->getAdminSections();
        $admin_pages = Admins::getInstance()->getAdminPages();

        $html = '<nav class="mt-2"><ul id="adl" class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">';
            foreach ($admin_sections as $i => $section_name) {
                $active = (isset($_GET['pagenum']) && $this->pagenum === $i) || (!$this->pagenum && Admins::getInstance()->_isActive() === $i);

                if (!empty($admin_pages[$i])) {
                    $html .= '<li class="nav-item has-treeview'.($active ? ' menu-open' : '').'">';
                        $html .= '<a href="#" class="nav-link'.($active ? ' active' : '').'">';
                            $html .= Admins::getInstance()->get_admin_section_icons($i);
                            $html .= ' <p>';
                                $html .= $section_name;
                                $html .= '<i class="fas fa-angle-left right"></i>';
                            $html .= '</p>';
                        $html .= '</a>';
                        $html .= '<ul class="nav nav-treeview">';
                            foreach ($admin_pages[$i] as $data) {
                                if (checkrights($data['admin_rights'])) {
                                    $sub_active = $data['admin_link'] == Admins::getInstance()->_currentPage();

                                    $icon = '<img class="nav-icon" src="'.get_image('ac_'.$data['admin_rights']).'" alt="'.$data['admin_title'].'">';

                                    if (!empty($admin_pages[$data['admin_rights']])) {
                                        if (checkrights($data['admin_rights'])) {
                                            $html .= '<li class="nav-item has-treeview '.($sub_active ? ' menu-open' : '').'">';
                                                $html .= '<a href="#" class="nav-link'.($active ? ' active' : '').'">'.$icon.' '.$data['admin_title'].'<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>';
                                                $html .= '<ul class="nav nav-treeview"'.($sub_active ? ' style="display: block;"' : '').'>';
                                                    foreach ($admin_pages[$data['admin_rights']] as $sub_page) {
                                                        $html .= '<li class="nav-item"><a class="nav-link'.($active ? ' active' : '').'" href="'.$sub_page['admin_link'].'"><p>'.$sub_page['admin_title'].'</p></a></li>';
                                                    }
                                                $html .= '</ul>';
                                            $html .= '</li>';
                                        }
                                    } else {
                                        $html .= '<li class="nav-item"><a class="nav-link'.($sub_active ? ' active' : '').'" href="'.ADMIN.$data['admin_link'].$aidlink.'">'.$icon.' <p>'.$data['admin_title'].'</p></a></li>';
                                    }
                                }
                            }
                        $html .= '</ul>';
                    $html .= '</li>';
                } else {
                    $html .= '<li class="nav-item"><a class="nav-link'.($active ? ' active' : '').'" href="'.ADMIN.'index.php'.$aidlink.'&pagenum=0">';
                        $html .= Admins::getInstance()->get_admin_section_icons($i).' <p>'.$section_name.'</p>';
                    $html .= '</a></li>';
                }
            }
        $html .= '</ul></nav>';

        return $html;
    }

    private function mainFooter() {
        $locale = fusion_get_locale();

        $html = '<footer class="main-footer">';
            $html .= showFooterErrors();

            if (fusion_get_settings('rendertime_enabled')) {
                $html .= showrendertime().' '.showMemoryUsage().'<br />';
            }

            $html .= '<strong>';
                $html .= 'AdminLTE v3 Admin Theme &copy; '.date('Y').' Created by <a href="https://github.com/RobiNN1" target="_blank">RobiNN</a> ';
                $html .= $locale['and'].' <a href="https://adminlte.io" target="_blank">Almsaeed Studio</a>';
            $html .= '</strong>';
            $html .= '<br/>'.str_replace('<br />', ' | ', showcopyright());
        $html .= '</footer>';

        return $html;
    }

    public function messages() {
        $userdata = fusion_get_userdata();

        $result = dbquery("
            SELECT message_id, message_subject, message_from, user_id, u.user_name, u.user_status, u.user_avatar, message_datestamp
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

    public static function openTable($title = FALSE, $class = NULL, $bg = TRUE) {
        $html = '';

        if (!empty($title)) {
            $html .= '<div class="content-header">';
            $html .= '<div class="container-fluid"><div class="row mb-2">';
            $html .= '<div class="col-sm-6"><h1 class="m-0 text-dark">'.$title.'</h1></div><h1></h1>';

            if (self::$breadcrumbs == FALSE) {
                $breadcrumbs = \PHPFusion\BreadCrumbs::getInstance();
                $breadcrumbs->setCssClasses('breadcrumb float-sm-right');
                $html .= '<div class="col-sm-6">';
                $html .= render_breadcrumbs();
                $html .= '</div>';
                self::$breadcrumbs = TRUE;
            }
            $html .= '</div></div>';
            $html .= '</div>';
        }

        $html .= '<section class="content '.$class.'">';
        $html .= '<div class="container-fluid">';

        if ($bg == TRUE) $html .= '<div class="p-15" style="background-color: #fff;">';

        echo $html;
    }

    public static function closeTable($bg = TRUE) {
        $html = '</div>';
        if ($bg === TRUE) $html .= '</div>';
        $html .= '</section>';

        echo $html;
    }
}
