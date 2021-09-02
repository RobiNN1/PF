<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: newsletter_panel.php
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

if (defined('NEWSLETTER_PANEL_EXISTS')) {
    $locale = fusion_get_locale('', NSL_LOCALE);
    $settings = fusion_get_settings();
    $nsl_settings = get_settings('newsletter_panel');

    $is_memeber = iMEMBER && column_exists('users', 'user_newsletter');
    $is_subscribed = column_exists('users', 'user_newsletter') && fusion_get_userdata('user_newsletter') == 1;

    $btn_title = $is_memeber ? ($is_subscribed ? $locale['nsl_056'] : $locale['nsl_057']) : $locale['submit'];

    $info = [
        'openform'  => openform('newsletter', 'post', FUSION_SELF),
        'closeform' => closeform(),
        'email'     => form_text('sub_email', '', '', ['placeholder' => 'email@email.com', 'required' => TRUE]),
        'submit'    => form_button('sub_submit', $btn_title, 'sub_submit'),
        'is_member' => $is_memeber
    ];

    if (post('sub_submit')) {
        if ($info['is_member']) {
            if (\defender::safe()) {
                $db = [
                    'user_id'         => fusion_get_userdata('user_id'),
                    'user_newsletter' => $is_subscribed == 1 ? 0 : 1
                ];

                dbquery_insert(DB_USERS, $db, 'update');
                addnotice('success', $locale['nsl_notice_24']);
            }
        } else {
            $data = [
                'sub_email' => sanitizer('sub_email', '', 'sub_email'),
                'sub_token' => random_token()
            ];

            if (\defender::safe()) {
                dbquery_insert(DB_NEWSLETTER_SUBS, $data, 'save');

                $body = file_get_contents(NEWSLETTER.'email_templates/newsletter_confirm.html');

                if (check_nsl_seo()) {
                    $link = $settings['siteurl'].'newsletter/subscribe/'.$data['sub_token'];
                } else {
                    $link = $settings['siteurl'].'infusions/newsletter_panel/newsletter.php?subscribe='.$data['sub_token'];
                }

                $body = strtr($body, [
                    '[BODY]'         => $locale['nsl_058'],
                    '[LINK]'         => $link,
                    '[LINK_TITLE]'   => $locale['nsl_059'],
                    '[CONFIRM_LINK]' => $locale['nsl_060'].' <br/>'.$link
                ]);

                send_newsletter($locale['nsl_061'], $body, $data['sub_email'], 3, 'html', $data['sub_token']);
                addnotice('success', $locale['nsl_notice_25']);
            }
        }

        redirect(FUSION_REQUEST);
    }

    if (!function_exists('render_newsletter')) {
        function render_newsletter($info) {
            $locale = fusion_get_locale();
            $nsl_settings = get_settings('newsletter_panel');

            if (checkgroup($nsl_settings['visibility'])) {
                openside($locale['nsl_title']);
                echo '<div class="m-b-10">'.str_replace('[SITENAME]', fusion_get_settings('sitename'), $locale['nsl_062']).'</div>';
                echo $info['openform'];
                if ($info['is_member']) {
                    echo $info['submit'];
                } else {
                    echo '<div class="row">';
                        echo '<div class="col-xs-9 col-sm-10">'.$info['email'].'</div>';
                        echo '<div class="col-xs-3 col-sm-2">'.$info['submit'].'</div>';
                    echo '</div>';
                }
                echo $info['closeform'];
                closeside();
            }
        }
    }

    render_newsletter($info);
}
