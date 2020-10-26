<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: user_newsletter_include.php
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

if ($profile_method == "input") {
    $options = [
            'reverse_label' => TRUE,
            'class'         => 'user-newsletter',
            'inner_width'   => '370px;max-width:370px;margin-left:-15px;'
        ] + $options;
    $user_fields = form_checkbox('user_newsletter', $locale['uf_newsletter_title'], $field_value, $options);
}
