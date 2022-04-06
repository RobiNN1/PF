<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: wiki_settings.php
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
$wiki_settings = get_settings('wiki');

if (isset($_POST['savesettings'])) {
    $settings = [
        'wiki_allow_submission' => form_sanitizer($_POST['wiki_allow_submission'], 0, 'wiki_allow_submission'),
        'is_helpful_stat'       => form_sanitizer($_POST['is_helpful_stat'], 0, 'is_helpful_stat')
    ];

    if (\defender::safe()) {
        foreach ($settings as $settings_name => $settings_value) {
            $inputSettings = [
                'settings_name'  => $settings_name,
                'settings_value' => $settings_value,
                'settings_inf'   => 'wiki'
            ];

            dbquery_insert(DB_SETTINGS_INF, $inputSettings, 'update', ['primary_key' => 'settings_name']);
        }

        addnotice('success', $locale['wiki_218']);
        redirect(FUSION_REQUEST);
    }
}

echo openform('settingsform', 'post', FUSION_REQUEST);

echo form_select('wiki_allow_submission', $locale['wiki_050'], $wiki_settings['wiki_allow_submission'], [
    'inline'  => TRUE,
    'options' => [$locale['disable'], $locale['enable']]
]);

echo form_select('is_helpful_stat', $locale['wiki_051'], $wiki_settings['is_helpful_stat'], [
    'inline'  => TRUE,
    'options' => [$locale['no'], $locale['yes']]
]);

echo form_button('savesettings', $locale['save'], $locale['save'], ['class' => 'btn-success', 'icon' => 'fa fa-hdd-o']);
echo closeform();
