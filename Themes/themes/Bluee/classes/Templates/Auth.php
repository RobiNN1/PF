<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Auth.php
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
namespace Bluee\Templates;

use Bluee\Core;
use Bluee\Main;

class Auth extends Core {
    public static function loginForm($info) {
        global $placeholder;

        $locale = fusion_get_locale('', BLUEE_LOCALE);
        $userdata = fusion_get_userdata();
        $settings = fusion_get_settings();

        define('THEME_BODY', '<body class="auth-page">');

        Main::hideAll();

        $links = [];
        if (iMEMBER) {
            $adminlink = [];
            if (iADMIN) {
                $adminlink = ['link' => ADMIN.'index.php'.fusion_get_aidlink(), 'title' => $locale['global_123']];
            }

            $msg_count = dbcount("(message_id)", DB_MESSAGES, "message_to='".$userdata['user_id']."'
             AND message_read='0' AND message_folder='0'");

            $links = [
                ['link' => BASEDIR.'edit_profile.php', 'title' => $locale['global_120']],
                ['link' => BASEDIR.'messages.php', 'title' => $locale['global_121'].' - '.sprintf($locale['global_125'], $msg_count)],
                ['link' => BASEDIR.'members.php', 'title' => $locale['global_122']],
                $adminlink,
                ['link' => BASEDIR.'index.php?logout=yes', 'title' => $locale['global_124']],
                ['link' => BASEDIR.$settings['opening_page'], 'title' => $locale['home']]
            ];
        }

        $context = [
            'locale'    => $locale,
            'userdata'  => $userdata,
            'settings'  => $settings,
            'notices'   => rendernotices(getnotices(['all', FUSION_SELF])),
            'info'      => $info,
            'links'     => $links,
            'form'      => [
                'name'  => form_text('user_name', '', '', ['placeholder' => $placeholder]),
                'pass'  => form_text('user_pass', '', '', [
                    'input_id'    => 'userpass',
                    'placeholder' => $locale['global_102'],
                    'type'        => 'password'
                ]),
                'check' => form_checkbox('remember_me', $locale['global_103'], ''),
                'link'  => str_replace(
                    ['[LINK]', '[/LINK]'],
                    ['<a class="display-inline-block pull-right text-dark" href="'.BASEDIR.'lostpassword.php">', '</a>']
                    , $locale['global_106']
                ),
                'login' => form_button('login', $locale['global_104'], $locale['global_104'], ['class' => 'btn-primary btn-block m-b-15'])
            ],
            'footer'    => self::footerLinks(),
            'copyright' => showcopyright('', TRUE)
        ];

        echo fusion_render(THEME.'twig/auth', 'login.twig', $context);
    }

    public static function registerForm($info) {
        define('THEME_BODY', '<body class="auth-page register-page">');

        Main::hideAll();

        $open = NULL;
        $close = NULL;

        if (isset($info['section']) && count($info['section']) > 1) {
            $tab_title = [];
            foreach ($info['section'] as $page_section) {
                $tab_title['title'][$page_section['id']] = $page_section['name'];
                $tab_title['id'][$page_section['id']] = $page_section['id'];
                $tab_title['icon'][$page_section['id']] = '';
            }
            $open = opentab($tab_title, get('section'), 'user-profile-form', TRUE);
            $close = closetab();
        }

        $ufs = NULL;
        if (!empty($info['user_field'])) {
            foreach ($info['user_field'] as $fieldData) {
                //$ufs .= !empty($fieldData['title']) ? $fieldData['title'] : '';
                if (!empty($fieldData['fields']) && is_array($fieldData['fields'])) {
                    foreach ($fieldData['fields'] as $cFieldData) {
                        $ufs .= !empty($cFieldData) ? $cFieldData : '';
                    }
                }
            }
        }

        $context = [
            'locale'    => fusion_get_locale('', BLUEE_LOCALE),
            'settings'  => fusion_get_settings(),
            'notices'   => rendernotices(getnotices(['all', FUSION_SELF])),
            'info'      => $info,
            'open_tab'  => $open,
            'close_tab' => $close,
            'no_uf'     => empty($info['user_name']) && empty($info['user_field']),
            'ufs'       => $ufs,
            'button'    => form_button('register', fusion_get_locale('global_107'), fusion_get_locale('global_107'), [
                'class' => 'btn-primary btn-block m-b-15'
            ]),
            'footer'    => self::footerLinks(),
            'copyright' => showcopyright('', TRUE)
        ];

        echo fusion_render(THEME.'twig/auth', 'register.twig', $context);
    }

    public static function lostPassword($content) {
        define('THEME_BODY', '<body class="auth-page lostpassword-page">');

        Main::hideAll();

        $context = [
            'locale'    => fusion_get_locale('', BLUEE_LOCALE),
            'content'   => $content,
            'footer'    => self::footerLinks(),
            'copyright' => showcopyright('', TRUE)
        ];

        echo fusion_render(THEME.'twig/auth', 'lostpassword.twig', $context);
    }

    public static function fusionGateway($info) {
        global $locale;

        define('THEME_BODY', '<body class="auth-page gateway-page">');

        Main::hideAll();

        $context = [
            'locale'    => $locale,
            'settings'  => fusion_get_settings(),
            'info'      => $info,
            'validated' => !isset($_SESSION['validated']),
            'incorrect' => isset($info['incorrect_answer']) && $info['incorrect_answer'] == TRUE,
            'footer'    => self::footerLinks(),
            'copyright' => showcopyright('', TRUE)
        ];

        echo fusion_render(THEME.'twig/auth', 'gateway.twig', $context);
    }
}
