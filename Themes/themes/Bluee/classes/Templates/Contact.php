<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Contact.php
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

class Contact extends Core {
    public static function renderContactForm($info) {
        $locale = fusion_get_locale();

        Main::hideAll(0);

        define('THEME_BODY', '<body class="contact-page">');
        self::setParam('fixed_menu', FALSE);
        self::setParam('section', FALSE);
        self::setParam('row', FALSE);

        $context = [
            'locale'   => $locale,
            'mailname' => form_text('mailname', $locale['CT_402'], $info['input']['mailname'], [
                'required'   => TRUE,
                'error_text' => $locale['CT_420'],
                'max_length' => 64
            ]),
            'email'    => form_text('email', $locale['CT_403'], $info['input']['email'], [
                'required'   => TRUE,
                'error_text' => $locale['CT_421'],
                'type'       => 'email',
                'max_length' => 64
            ]),
            'subject'  => form_text('subject', $locale['CT_404'], $info['input']['subject'], [
                'required'   => TRUE,
                'error_text' => $locale['CT_422'],
                'max_length' => 64
            ]),
            'message'  => form_textarea('message', $locale['CT_405'], $info['input']['message'], [
                'required'   => TRUE,
                'error_text' => $locale['CT_423'],
                'max_length' => 128
            ]),
            'button'   => form_button('sendmessage', $locale['CT_406'], $locale['CT_406'], [
                'class' => 'btn-primary',
                'icon'  => 'fas fa-paper-plane'
            ]),
            'info'     => $info
        ];

        echo fusion_render(THEME.'twig', 'contact.twig', $context);
    }
}
