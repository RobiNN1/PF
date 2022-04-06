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
namespace Bluee;

use PHPFusion\Panels;
use PHPFusion\Search\Search_Engine;
use PHPFusion\SiteLinks;

class Main extends Core {
    public function __construct() {
        $locale = fusion_get_locale('', BLUEE_LOCALE);
        $settings = fusion_get_settings();

        $theme_js = file_exists(THEME.'assets/js/main.min.js') ? THEME.'assets/js/main.min.js' : THEME.'assets/js/main.js';
        add_to_footer('<script async src="'.$theme_js.'?v='.filemtime($theme_js).'"></script>');
        add_to_head('<meta name="robots" content="index, follow">');

        $menu_options = [
            'id'                => 'main-menu',
            'container'         => TRUE,
            'grouping'          => TRUE,
            'links_per_page'    => 5,
            'caret_icon'        => 'fas fa-angle-down',
            'custom_header'     => '
                <a class="navbar-brand" href="'.BASEDIR.$settings['opening_page'].'"><img height="80" src="'.BASEDIR.$settings['sitebanner'].'" alt="'.$settings['sitename'].'"></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu_menu" aria-controls="main-menu_menu"><span class="navbar-toggler-icon"></span></button>
            ',
            'html_post_content' => $this->userMenu()
        ];

        $content = ['sm' => 12, 'md' => 12, 'lg' => 12];
        $right = ['sm' => 3, 'md' => 3, 'lg' => 3];

        if ((defined('RIGHT') && RIGHT) || (defined('LEFT') && LEFT)) {
            $content['sm'] = $content['sm'] - $right['sm'];
            $content['md'] = $content['md'] - $right['md'];
            $content['lg'] = $content['lg'] - $right['lg'];
        }

        $result = dbquery("SELECT o.*, u.user_id, u.user_name, u.user_status, u.user_level
            FROM ".DB_ONLINE." o
            LEFT JOIN ".DB_USERS." u ON o.online_user=u.user_id
        ");

        $guests = 0;
        $members = [];

        while ($data = dbarray($result)) {
            if ($data['online_user'] == 0) {
                $guests++;
            } else {
                $members[$data['user_id']] = [
                    $data['user_id'],
                    $data['user_name'],
                    $data['user_status'],
                    $data['user_level']
                ];
            }
        }

        $newer_user = dbarray(dbquery("SELECT user_id, user_name, user_status FROM ".DB_USERS." WHERE user_status='0' ORDER BY user_joined DESC LIMIT 0,1"));

        $context = [
            'locale'        => $locale,
            'settings'      => $settings,
            'getparam'      => [
                'section' => $this->getParam('section'),
                'row'     => $this->getParam('row'),
                'header'  => $this->getParam('header'),
                'footer'  => $this->getParam('footer'),
            ],
            'notices'       => $this->getParam('notices') ? rendernotices(getnotices(['all', FUSION_SELF])) : '',
            'mainmenu'      => SiteLinks::setSubLinks($menu_options)->showSubLinks(),
            'banner1'       => showbanners(1),
            'banner2'       => showbanners(2),
            'content'       => $content,
            'ifleft'        => defined('LEFT') && LEFT,
            'ifright'       => defined('RIGHT') && RIGHT,
            'right'         => $right,
            'footer_text'   => parse_text($settings['footer'], ['parse_smileys' => FALSE, 'add_line_breaks' => FALSE]),
            'ifmember'      => !iMEMBER && $settings['enable_registration'],
            'newer_user'    => profile_link($newer_user['user_id'], $newer_user['user_name'], $newer_user['user_status']),
            'guests_count'  => format_word($guests, $locale['fmt_guest']),
            'members_count' => format_word(number_format(count($members)), $locale['fmt_member']),
            'members'       => implode(', ', array_map(function ($member) {
                return profile_link($member[0], $member[1], $member[2]);
            }, $members)),
            'counter'       => showcounter(),
            'errors'        => showfootererrors(),
            'ifrendertime'  => iADMIN && ($settings['rendertime_enabled'] == 1 || $settings['rendertime_enabled'] == 2),
            'rendertime'    => showrendertime(),
            'memoryusage'   => showmemoryusage(),
            'footer'        => self::footerLinks(),
            'copyright'     => showcopyright('', TRUE)
        ];

        echo fusion_render(THEME.'twig', 'theme.twig', $context);
    }

    private function userMenu() {
        $locale = fusion_get_locale();
        $userdata = fusion_get_userdata();
        $settings = fusion_get_settings();

        $msg_count = 0;

        if (iMEMBER) {
            $msg_count = dbcount(
                "('message_id')",
                DB_MESSAGES, "message_to=:my_id AND message_read=:unread AND message_folder=:inbox",
                [':inbox' => 0, ':my_id' => $userdata['user_id'], ':unread' => 0]
            );
        }

        $context = [
            'locale'       => $locale,
            'settings'     => $settings,
            'userdata'     => $userdata,
            'searchbox'    => $this->seacrhBox(),
            'languages'    => fusion_get_enabled_languages(),
            'current_lang' => translate_lang_names(LANGUAGE),
            'msg_count'    => $msg_count,
            'adminlink'    => iADMIN ? ADMIN.'index.php'.fusion_get_aidlink() : '',
            'loginas'      => session_get('login_as'),
            'loginform'    => $this->loginForm(),
            'lostpassword' => str_replace(['[LINK]', '[/LINK]'], ['<a href="'.BASEDIR.'lostpassword.php">', '</a>'], $locale['global_106'])
        ];

        return fusion_render(THEME.'twig', 'usermenu.twig', $context);
    }

    public function loginForm() {
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
        $html .= form_text('user_pass', '', '', ['placeholder' => $locale['global_102'], 'type' => 'password', 'password_toggle' => FALSE, 'required' => TRUE, 'input_id' => 'userpassword']);
        $html .= form_checkbox('remember_me', $locale['global_103'], '', ['value' => 'y', 'reverse_label' => TRUE, 'input_id' => 'rememberme']);
        $html .= form_button('login', $locale['global_104'], 'login', ['class' => 'btn-primary m-t-5 m-b-5', 'icon' => 'fas fa-sign-in-alt', 'input_id' => 'loginbtn']);
        $html .= closeform();

        return $html;
    }

    public function seacrhBox() {
        $locale = fusion_get_locale();

        $html = openform('searchform', 'post', BASEDIR.'search.php', ['class' => 'p-10']);
        $html .= form_text('stext', '', urldecode(Search_Engine::get_param('stext')), [
            'inline'      => FALSE,
            'placeholder' => $locale['search'],
            'class'       => 'm-b-0'
        ]);
        $html .= closeform();

        return $html;
    }

    public static function hideAll($theme_sections = TRUE) {
        Panels::getInstance(TRUE)->hideAll();

        if ($theme_sections == TRUE) {
            self::setParam('header', FALSE);
            self::setParam('section', FALSE);
            self::setParam('row', FALSE);
            self::setParam('footer', FALSE);
            self::setParam('notices', FALSE);
        }
    }
}
