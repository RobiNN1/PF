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
namespace AtomCP;

class Login {
    public function __construct() {
        $locale = fusion_get_locale();
        add_to_footer('<script src="'.ATOMCP.'assets/scripts.min.js?v='.filemtime(ATOMCP.'assets/scripts.min.js').'"></script>');
        add_to_css('body {background-color: #f0eff0;}');
        add_to_jquery('$("#admin_password").focus();');

        echo '<div class="login-container">';
            echo '<h1 class="brand">AtomCP</h1>';

            $form_action = FUSION_SELF.fusion_get_aidlink() == ADMIN.'index.php'.fusion_get_aidlink() ? FUSION_SELF.fusion_get_aidlink().'&pagenum=0' : FUSION_REQUEST;
            echo openform('admin-login-form', 'post', $form_action, ['class' => 'login-form m-t-30']);
                echo form_text('admin_password', $locale['281'], '', [
                    'type'             => 'password',
                    'callback_check'   => 'check_admin_pass',
                    'error_text'       => $locale['global_182'],
                    'autocomplete_off' => TRUE,
                    'required'         => TRUE,
                    'prepend_id'       => 'password-icon',
                    'prepend_value'    => '<i class="fa fa-user"></i>'
                ]);

                echo form_button('admin_login', $locale['login'], $locale['login'], ['class' => 'btn-login btn-block']);
            echo closeform();
        echo '</div>';
    }
}
