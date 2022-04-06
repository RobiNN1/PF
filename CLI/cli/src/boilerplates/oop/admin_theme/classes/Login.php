<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Login.php
| Author: YOUR_NAMELICENSE_TEXT
+--------------------------------------------------------*/
namespace ADDON_NAME;

class Login {
    public function __construct() {
        $locale = fusion_get_locale();
        $userdata = fusion_get_userdata();

        add_to_jquery('$("#admin_password").focus();');

        $html = '<div class="container text-center">';
            $html .= '<h1><strong>'.$locale['280'].'</strong></h1>';

            $html .= '<div class="login-box">';
                $html .= '<div class="clearfix m-b-20">';
                    $html .= '<div class="pull-left m-r-10">';
                        $html .= display_avatar($userdata, '90px', '', FALSE, 'img-rounded');
                    $html .= '</div>';
                    $html .= '<div class="text-left">';
                        $html .= '<h3><strong>'.$locale['welcome'].', '.$userdata['user_name'].'</strong></h3>';
                        $html .= '<p>'.getuserlevel($userdata['user_level']).'</p>';
                    $html .= '</div>';
                $html .= '</div>';

                $form_action = FUSION_SELF.fusion_get_aidlink() == ADMIN.'index.php'.fusion_get_aidlink() ? FUSION_SELF.fusion_get_aidlink().'&pagenum=0' : FUSION_REQUEST;
                $html .= openform('admin-login-form', 'post', $form_action);
                    $html .= form_text('admin_password', '', '', ['type' => 'password', 'callback_check' => 'check_admin_pass', 'placeholder' => $locale['281'], 'error_text' => $locale['global_182'], 'autocomplete_off' => TRUE, 'required' => TRUE]);
                    $html .= form_button('admin_login', $locale['login'], $locale['login'], ['class' => 'btn-primary btn-block']);
                $html .= closeform();
            $html .= '</div>';
        $html .='</div>';

        echo $html;
    }
}
