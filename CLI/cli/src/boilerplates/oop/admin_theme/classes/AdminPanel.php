<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: AdminPanel.php
| Author: YOUR_NAMELICENSE_TEXT
+--------------------------------------------------------*/
namespace ADDON_NAME;

use PHPFusion\Admins;

class AdminPanel {
    public function __construct() {
        $locale = fusion_get_locale();
        $userdata = fusion_get_userdata();
        $settings = fusion_get_settings();
        $aidlink = fusion_get_aidlink();

        $pagenum = (int)filter_input(INPUT_GET, 'pagenum');

        add_to_jquery('
            search_ajax("'.ADMIN.'includes/acp_search.php'.$aidlink.'");
            function search_ajax(url) {
                $("#search_pages").bind("keyup", function () {
                    $.ajax({
                        url: url,
                        get: "GET",
                        data: $.param({"pagestring": $(this).val()}),
                        dataType: "json",
                        success: function (e) {
                            if ($("#search_pages").val() === "") {
                                $(".nav-search-dropdown").removeClass("open");
                            } else {
                                var result = "";

                                if (!e.status) {
                                    $.each(e, function (i, data) {
                                        if (data) {
                                            result += "<li><a href=\"" + data.link + "\"><img class=\"admin-image\" alt=\"" + data.title + "\" src=\"" + data.icon + "\"/> " + data.title + "</a></li>";
                                        }
                                    });
                                } else {
                                    result = "<li class=\"p-10\"><span>" + e.status + "</span></li>";
                                }

                                $("#search_result").html(result);
                                $(".nav-search-dropdown").addClass("open");
                            }
                        }
                    });
                });
            }
        ');

        echo '<div class="container m-t-20 m-b-20">';

            echo '<div class="top-menu navbar fixed">';
                echo '<ul class="nav navbar-nav navbar-left hidden-xs hidden-sm hidden-md">';
                    $sections = Admins::getInstance()->getAdminSections();
                    if (!empty($sections)) {
                        $i = 0;

                        foreach ($sections as $section_name) {
                            $active = (isset($_GET['pagenum']) && $pagenum === $i) || (!$pagenum && Admins::getInstance()->_isActive() === $i);
                            echo '<li'.($active ? ' class="active"' : '').'><a href="'.ADMIN.'index.php'.$aidlink.'&pagenum='.$i.'" data-toggle="tooltip" data-placement="bottom" title="'.$section_name.'">'.Admins::getInstance()->get_admin_section_icons($i).'</a></li>';
                            $i++;
                        }
                    }
                echo '</ul>';

                echo '<ul class="nav navbar-nav navbar-right hidden-xs">';
                    $languages = fusion_get_enabled_languages();
                    if (count($languages) > 1) {
                        echo '<li class="dropdown languages-switcher">';
                            echo '<a id="ddlangs" class="dropdown-toggle pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="'.$locale['282'].'"><i class="fa fa-globe"></i><img class="current" src="'.BASEDIR.'locale/'.LANGUAGE.'/'.LANGUAGE.'.png" alt="'.translate_lang_names(LANGUAGE).'"/><span class="caret"></span></a>';
                            echo '<ul class="dropdown-menu" aria-labelledby="ddlangs">';
                                foreach ($languages as $language_folder => $language_name) {
                                    echo '<li><a class="display-block" href="'.clean_request('lang='.$language_folder, ['lang'], FALSE).'"><img class="m-r-5" src="'.BASEDIR.'locale/'.$language_folder.'/'.$language_folder.'-s.png" alt="'.$language_folder.'"/> '.$language_name.'</a></li>';
                                }
                            echo '</ul>';
                        echo '</li>';
                    }

                    echo '<li class="dropdown user-s">';
                        echo '<a id="dduser" href="#" class="dropdown-toggle pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.display_avatar($userdata, '30px', '', FALSE, 'img-rounded').' <strong>'.$userdata['user_name'].'</strong><span class="caret"></span></a>';
                        echo '<ul class="dropdown-menu" aria-labelledby="dduser" role="menu">';
                            echo '<li><a href="'.BASEDIR.'edit_profile.php"><i class="fa fa-pencil fa-fw"></i> '.$locale['UM080'].'</a></li>';
                            echo '<li><a href="'.BASEDIR.'profile.php?lookup='.$userdata['user_id'].'"><i class="fa fa-eye fa-fw"></i> '.$locale['view'].' '.$locale['profile'].'</a></li>';
                            echo '<li class="divider"></li>';
                            echo '<li><a href="'.FUSION_REQUEST.'&logout"><i class="fa fa-sign-out fa-fw"></i> '.$locale['admin-logout'].'</a></li>';
                            echo '<li><a href="'.BASEDIR.'index.php?logout=yes"><i class="fa fa-sign-out fa-fw"></i> <span class="text-danger">'.$locale['logout'].'</span></a></li>';
                        echo '</ul>';
                    echo '</li>';
                    echo '<li><a title="'.$locale['message'].'" href="'.BASEDIR.'messages.php"><i class="fa fa-envelope-o"></i></a></li>';
                    echo '<li><a title="'.$settings['sitename'].'" href="'.BASEDIR.'index.php"><i class="fa fa-home"></i></a></li>';
                echo '</ul>';
            echo '</div>';

            echo '<div class="row">';
                echo '<div class="col-xs-12 col-sm-2">';
                    echo '<div class="dropdown nav-search-dropdown">';
                        echo '<div class="navbar-form 10 m-b-20"><input class="form-control input-sm" type="text" id="search_pages" name="search_pages" placeholder="'.$locale['search'].'"/></div>';
                        echo '<ul class="dropdown-menu m-l-15" aria-labelledby="search_pages" id="search_result"></ul>';
                    echo '</div>';

                    echo Admins::getInstance()->vertical_admin_nav(TRUE);
                echo '</div>';

                echo '<div class="col-xs-12 col-sm-10">';
                    echo '<div id="updatechecker_result" class="alert alert-info" style="display:none;"></div>';
                    echo renderNotices(getnotices());

                    echo CONTENT;
                echo '</div>';
            echo '</div>';

            echo showFooterErrors();

            if ($settings['rendertime_enabled']) {
                echo '<br/>'.showrendertime().'<br/>'.showMemoryUsage();
            }

            echo showcopyright();

        echo '</div>';
    }
}
