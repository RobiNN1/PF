<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: tos.php
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

$locale = fusion_get_locale('', LOCALE.LOCALESET.'admin/settings.php');
$settings = fusion_get_settings();

$is_multilang = count(fusion_get_enabled_languages()) > 1;

if (isset($_POST['save_item'])) {
    $inputData = [
        'license_agreement'  => form_sanitizer($_POST['license_agreement'], '', 'license_agreement', $is_multilang),
        'enable_terms'       => form_sanitizer($_POST['enable_terms'], '0', 'enable_terms'),
        'license_lastupdate' => ($_POST['license_agreement'] != fusion_get_settings('license_agreement') ? time() : fusion_get_settings('license_lastupdate'))
    ];

    if (\defender::safe()) {
        foreach ($inputData as $settings_name => $settings_value) {
            dbquery("UPDATE ".DB_SETTINGS." SET settings_value=:settings_value WHERE settings_name=:settings_name", [
                ':settings_value' => $settings_value,
                ':settings_name'  => $settings_name
            ]);
        }

        addNotice('success', $locale['lg_06']);
        redirect(FUSION_REQUEST);
    }
}

echo openform('inputform', 'post', FUSION_REQUEST);

$opts = ['1' => $locale['yes'], '0' => $locale['no']];

echo form_select('enable_terms', $locale['558'], $settings['enable_terms'], ['options' => $opts]);
if ($is_multilang == TRUE) {
    echo \PHPFusion\QuantumFields::quantum_multilocale_fields('license_agreement', $locale['559'], $settings['license_agreement'], [
        'form_name' => 'settingsform',
        'input_id'  => 'enable_license_agreement',
        'autosize'  => (bool)fusion_get_settings('tinymce_enabled'),
        'type'      => (fusion_get_settings('tinymce_enabled') ? 'tinymce' : 'html'),
        'function'  => 'form_textarea'
    ]);
} else {
    echo form_textarea('license_agreement', $locale['559'], $settings['license_agreement'], [
        'form_name' => 'settingsform',
        'autosize'  => (bool)fusion_get_settings('tinymce_enabled'),
        'html'      => !fusion_get_settings('tinymce_enabled')
    ]);
}

echo form_button('save_item', $locale['save'], $locale['save'], ['class' => 'btn-success', 'icon' => 'fa fa-hdd-o']);
echo closeform();
