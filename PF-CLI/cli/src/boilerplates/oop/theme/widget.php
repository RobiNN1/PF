<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: widget.php
| Author: YOUR_NAMELICENSE_TEXT
+--------------------------------------------------------*/
$settings = get_theme_settings('folder_name');
$locale = fusion_get_locale();

if (isset($_POST['save_settings'])) {
    $settings = [
        'facebook_url' => form_sanitizer($_POST['facebook_url'], '', 'facebook_url')
    ];

    if (\defender::safe()) {
        foreach ($settings as $settings_name => $settings_value) {
            $db = [
                'settings_name'  => $settings_name,
                'settings_value' => $settings_value,
                'settings_theme' => 'folder_name'
            ];

            dbquery_insert(DB_SETTINGS_THEME, $db, 'update');
        }

        addnotice('success', 'Settings has been updated');
        redirect(FUSION_REQUEST);
    }
}

echo openform('main_settings', 'post', FUSION_REQUEST);
openside('');
echo form_text('facebook_url', 'Facebook URL', $settings['facebook_url'], ['type' => 'url', 'inline' => TRUE]);
closeside();

echo form_button('save_settings', $locale['save_changes'], 'save', ['class' => 'btn-primary']);
echo closeform();
