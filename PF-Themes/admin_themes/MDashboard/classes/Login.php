<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Login.php
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

class Login {
    public function __construct() {
        $locale = fusion_get_locale('', MD_LOCALE);
        $userdata = fusion_get_userdata();

        add_to_head('<style type="text/css">body, html {height: 100%;}body{background: url("'.MD.'images/login.jpg") no-repeat fixed center;background-size: cover;}</style>');
        add_to_jquery('$("#admin_password").focus();');

        $html = '<div class="login-container">';
            $html .= '<h1 class="title">'.$locale['md_001'].'</h1>';
            $html .= '<div class="login-box">';
                $html .= '<div class="clearfix m-b-20">';
                    $html .= '<div class="pull-left m-r-10">';
                        $html .= display_avatar($userdata, '90px', '', FALSE, 'img-rounded');
                    $html .= '</div>';
                    $html .= '<div class="text-left">';
                        $html .= '<h3>'.$locale['welcome'].',<br/>'.$userdata['user_name'].'</h3>';
                    $html .= '</div>';
                $html .= '</div>';

                $form_action = FUSION_SELF.fusion_get_aidlink() == ADMIN.'index.php'.fusion_get_aidlink() ? FUSION_SELF.fusion_get_aidlink().'&pagenum=0' : FUSION_REQUEST;
                $html .= openform('admin-login-form', 'post', $form_action, ['class' => 'm-t-30']);
                    $html .= form_text('admin_password', '', '', ['type' => 'password', 'callback_check' => 'check_admin_pass', 'placeholder' => $locale['281'], 'error_text' => $locale['global_182'], 'autocomplete_off' => TRUE, 'required' => TRUE]);
                    $html .= form_button('admin_login', $locale['login'], $locale['login'], ['class' => 'btn-primary btn-block m-t-15']);
                $html .= closeform();
            $html .= '</div>';
        $html .= '</div>';

        echo $html;
    }
}
