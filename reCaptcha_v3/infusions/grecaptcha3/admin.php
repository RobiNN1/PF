<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: admin.php
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
require_once '../../maincore.php';
require_once THEMES.'templates/admin_header.php';

pageAccess('RC3');

$locale = fusion_get_locale('', RC3_LOCALE);
$rc3_settings = get_settings('grecaptcha3');

if (isset($_POST['savesettings'])) {
    $settings = [
        'private_key' => form_sanitizer($_POST['private_key'], '', 'private_key'),
        'public_key'  => form_sanitizer($_POST['public_key'], '', 'public_key'),
        'score'       => form_sanitizer($_POST['score'], '', 'score')
    ];

    if (\defender::safe()) {
        foreach ($settings as $key => $value) {
            if (\defender::safe()) {
                $data = [
                    'settings_name'  => $key,
                    'settings_value' => $value,
                    'settings_inf'   => 'grecaptcha3'
                ];
                dbquery_insert(DB_SETTINGS_INF, $data, 'update', ['primary_key' => 'settings_name']);
            }
        }

        addNotice('success', $locale['rc3_01']);
    }

    redirect(FUSION_REQUEST);
}

opentable($locale['rc3_title']);
echo openform('settingsform', 'post', FUSION_REQUEST);

echo '<div class="alert alert-info">'.str_replace(['[LINK]', '[/LINK]'], ['<a target="_blank" href="https://www.google.com/recaptcha/admin">', '</a>'], $locale['rc3_02']).'</div>';

echo form_text('public_key', $locale['rc3_03'], $rc3_settings['public_key'], ['inline' => TRUE]);
echo form_text('private_key', $locale['rc3_04'], $rc3_settings['private_key'], ['inline' => TRUE]);

$opts = [
    '1.0' => '1.0',
    '0.9' => '0.9',
    '0.8' => '0.8',
    '0.7' => '0.7',
    '0.6' => '0.6',
    '0.5' => '0.5',
    '0.4' => '0.4',
    '0.3' => '0.3',
    '0.2' => '0.2',
    '0.1' => '0.1'
];

echo form_select('score', $locale['rc3_05'], $rc3_settings['score'], [
    'options' => $opts,
    'inline'  => TRUE,
    'ext_tip' => $locale['rc3_06']
]);

echo form_button('savesettings', $locale['save'], $locale['save'], ['class' => 'btn-success', 'icon' => 'fa fa-hdd-o']);
echo closeform();
closetable();

require_once THEMES.'templates/footer.php';
