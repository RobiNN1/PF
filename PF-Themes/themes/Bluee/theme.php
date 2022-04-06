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

require_once INCLUDES.'theme_functions_include.php';
require_once __DIR__.'/theme_autoloader.php';

if (is_file(__DIR__.'/vendor/autoload.php')) {
    require_once __DIR__.'/vendor/autoload.php';
}

if (file_exists(INCLUDES.'twig_includes.php')) {
    require_once INCLUDES.'twig_includes.php';
} else {
    require_once __DIR__.'/twig_includes.php';
}

if (!defined('BLUEE_LOCALE')) {
    if (file_exists(THEME.'locale/'.LANGUAGE.'.php')) {
        define('BLUEE_LOCALE', THEME.'locale/'.LANGUAGE.'.php');
    } else {
        define('BLUEE_LOCALE', THEME.'locale/English.php');
    }
}

const FONTAWESOME = TRUE;
const BOOTSTRAP4 = TRUE;

// theme uses own js code for this
const PM_JS = TRUE;
const USERNAME_CHECK = TRUE;
const EDITPROFILE_JS_CHECK = TRUE;
if (!defined('PWTOGGLE')) {
    define('PWTOGGLE', TRUE);
}

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
            'img-rounded'   => 'rounded'
        ]);
    }, $output);
});

function render_page() {
    new Bluee\Main();
}

function opentable($title = FALSE, $class = '') {
    echo '<div class="m-b-20 '.$class.'">';
    echo !empty($title) ? '<h3>'.$title.'</h3>' : '';
}

function closetable() {
    echo '</div>';
}

function openside($title = FALSE, $class = '') {
    echo '<div class="m-b-20 openside card '.$class.'">';
    echo !empty($title) ? '<div class="card-header"><h6 class="card-title m-b-0">'.$title.'</h6></div>' : '';
    echo '<div class="card-body p-10">';
}

function closeside() {
    echo '</div>';
    echo '</div>';
}

/*
 * Login/Register/LostPassword/Gateway TPL
 */
function display_loginform($info) {
    Bluee\Templates\Auth::loginForm($info);
}

function display_register_form($info) {
    Bluee\Templates\Auth::registerForm($info);
}

function display_lostpassword($content) {
    Bluee\Templates\Auth::lostPassword($content);
}

function display_gateway($info) {
    Bluee\Templates\Auth::fusionGateway($info);
}

/*
 * Comments TPL
 */
function display_comments_ui($info) {
    return Bluee\Templates\Comments::displayCommentsUi($info);
}

function display_comments_list($info) {
    return Bluee\Templates\Comments::displayCommentsList($info);
}

/*
 * Contact TPL
 */
function render_contact_form($info) {
    Bluee\Templates\Contact::renderContactForm($info);
}

/*
 * Downloads TPL
 */
function render_downloads($info) {
    echo Bluee\Templates\Downloads::renderDownloads($info);
}

/*
 * Errors TPL
 */
function display_error_page($info) {
    Bluee\Templates\Errors::displayErrorPage($info);
}

/*
 * Forum TPL
 */
function render_forum($info) {
    Bluee\Templates\Forum\Main::renderForum($info);
}

function render_postify($info) {
    Bluee\Templates\Forum\Main::renderPostify($info);
}

function display_forum_postform($info) {
    Bluee\Templates\Forum\NewThread::displayForumPostForm($info);
}

function display_forum_tags($info) {
    Bluee\Templates\Forum\Tags::displayForumTags($info);
}

function render_thread($info) {
    Bluee\Templates\Forum\ViewThread::renderThread($info);
}

/*
 * HomePage TPL
 */
function display_home($info) {
    Bluee\Templates\Home::displayHome($info);
}

/*
 * Maintenance TPL
 */
function display_maintenance($info) {
    Bluee\Templates\Maintenance::displayMaintenance($info);
}

/*
 * News TPL
 */
function display_main_news($info) {
    Bluee\Templates\News::displayMainNews($info);
}

function render_news_item($info) {
    Bluee\Templates\News::renderNewsItem($info);
}

/*
 * PrivateMessages TPL
 */
function display_inbox($info) {
    echo Bluee\Templates\PrivateMessages::displayInbox($info);
}

/*
 * Profile
 */
function display_user_profile($info) {
    Bluee\Templates\Profile::displayProfile($info);
}

/*
 * Search TPL
 */
function render_search($info) {
    return Bluee\Templates\Search::renderSearch($info);
}

function render_search_item_wrapper($info) {
    return Bluee\Templates\Search::renderSearchItemWrapper($info);
}

function render_search_item($info) {
    return Bluee\Templates\Search::renderSearchItem($info);
}

function render_search_item_list($info) {
    return Bluee\Templates\Search::renderSearchItemList($info);
}

function render_search_no_result($info) {
    return Bluee\Templates\Search::renderSearchNoResult($info);
}

function render_breadcrumbs($key = 'default', $class = '', $style = '') {
    $breadcrumbs = \PHPFusion\BreadCrumbs::getInstance($key);
    $html = '<ol class="'.$breadcrumbs->getCssClasses().' '.$class.'"'.($style ? ' style="'.$style.'"' : '').'>';
    foreach ($breadcrumbs->toArray() as $crumb) {
        $html .= '<li class="breadcrumb-item '.$crumb['class'].($crumb['link'] ? '' : ' active').'">';
        $html .= ($crumb['link']) ? '<a title="'.$crumb['title'].'" class="text-dark" href="'.$crumb['link'].'">'.$crumb['title'].'</a>' : $crumb['title'];
        $html .= '</li>';
    }
    $html .= '</ol>';

    return $html;
}

function render_user_tags($data, $tooltip) {
    $locale = fusion_get_locale();

    if (!defined('USERPOPOVER')) {
        define('USERPOPOVER', TRUE);
        add_to_jquery("$('[data-toggle=\"user-tooltip\"]').popover();");
    }

    if ($data['user_avatar'] && file_exists(IMAGES.'avatars/'.$data['user_avatar'])) {
        $src = fusion_get_settings('siteurl').'images/avatars/'.$data['user_avatar'];
    } else {
        $src = fusion_get_settings('siteurl').'themes/Bluee/assets/img/noavatar.png';
    }

    $link = BASEDIR.'profile.php?lookup='.$data['user_id'];

    $title = '<div class="user-tooltip">';
    $title .= '<div class="pull-left m-r-10">';
    $title .= '<img src="'.$src.'" alt="'.$data['user_name'].'" class="img-fluid img-circle">';
    $online = $data['user_lastvisit'] >= time() - 300;
    $title .= '<div class="user-status"><i title="'.($online ? $locale['online'] : $locale['offline']).'" class="fa fa-circle '.($online ? 'text-success' : 'text-danger').'"></i></div>';
    $title .= '</div>';

    $title .= '<div class="m-b-0 display-inline-block">';
    $title .= '<a class="strong" href="'.$link.'">'.$data['user_name'].'</a>';

    if ($data['user_id'] != 1) {
        if (iSUPERADMIN || (iADMIN && checkrights('M'))) {
            $link = ADMIN.'members.php'.fusion_get_aidlink().'&ref=edit&lookup='.$data['user_id'];
            $title .= '<a class="text-warning" href="'.$link.'" title="'.$locale['edit'].'"><i class="fas fa-pencil-alt m-l-5"></i></a>';
        }
    }
    $title .= '</div>';
    $title .= '<br/><span class="user_level">'.getuserlevel($data['user_level']).'</span>';
    $title .= '</div>';

    $title .= '</div>'; // .user-tooltip

    if (iMEMBER) {
        if (fusion_get_userdata('user_id') !== $data['user_id']) {
            $content = '<a class="btn btn-block btn-primary" href="'.BASEDIR.'messages.php?msg_send='.$data['user_id'].'"><i class="fwa fa-send"></i> '.$locale['send_message'].'</a>';
        } else {
            $content = '<a class="btn btn-block btn-primary" href="'.BASEDIR.'edit_profile.php"><i class="fas fa-pencil-alt"></i> '.$locale['UM080'].'</a>';
        }
    } else {
        $content = '<a class="btn btn-block btn-primary" href="'.$link.'"><i class="fas fa-eye"></i> '.$locale['view'].' '.$locale['profile'].'</a>';
    }

    $data_atr = " data-html='true' data-trigger='focus' data-placement='top' data-toggle='user-tooltip' ";
    $html = "<a class='strong pointer' tabindex='0' role='button' title='".$title."'".$data_atr."data-content='".$tooltip.$content."'>";
    $html .= '<span class="user-label">@'.$data['user_name'].'</span>';
    $html .= '</a>';

    return $html;
}

set_image('imagenotfound', THEME.'assets/img/noimage.svg');
