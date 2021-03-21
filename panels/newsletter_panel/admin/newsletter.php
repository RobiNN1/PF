<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: newsletter.php
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

$locale = fusion_get_locale();

$subs = [];

if (column_exists('users', 'user_newsletter')) {
    $result_users = dbquery("SELECT user_email as sub_email, user_newsletter FROM ".DB_USERS." WHERE user_newsletter=1");
    if (dbrows($result_users) > 0) {
        while ($user = dbarray($result_users)) {
            $subs[] = $user;
        }
    }
}

$result_subs = dbquery("SELECT * FROM ".DB_NEWSLETTER_SUBS." WHERE sub_active=1");
if (dbrows($result_subs) > 0) {
    while ($sub = dbarray($result_subs)) {
        $subs[] = $sub;
    }
}

if (isset($_POST['send_nsl'])) {
    $tpl_data = dbarray(dbquery("SELECT * FROM ".DB_NEWSLETTER_TEMPLATES." WHERE tpl_id=:tpl_id", [':tpl_id' => (int) $_POST['template']]));

    foreach ($subs as $sub) {
        send_nsl($tpl_data, $sub, !empty($sub['sub_token']) ? $sub['sub_token'] : NULL);
    }

    redirect(FUSION_REQUEST);
}

echo openform('sendemail', 'post', FUSION_REQUEST);

$result_tpls = dbquery("SELECT * FROM ".DB_NEWSLETTER_TEMPLATES);
$templates = [];
$deactivate = TRUE;
if (dbrows($result_tpls) > 0) {
    while ($tpl = dbarray($result_tpls)) {
        $templates[$tpl['tpl_id']] = $tpl['tpl_name'];
    }

    $deactivate = FALSE;
}

echo form_select('template', $locale['nsl_004'], '', [
    'options' => $templates
]);

echo form_button('send_nsl', $locale['nsl_005'], 'send_nsl', ['deactivate' => $deactivate]);

echo closeform();

function send_nsl($data, $sub, $token) {
    if (!empty($data['tpl_file']) && file_exists(NEWSLETTER.'email_templates/'.$data['tpl_file'])) {
        $body = file_get_contents(NEWSLETTER.'email_templates/'.$data['tpl_file']);
    } else {
        $body = $data['tpl_body'];
    }

    send_newsletter($data['tpl_name'], $body, $sub['sub_email'], $data['tpl_priority'], $token, $data['tpl_style']);
}
