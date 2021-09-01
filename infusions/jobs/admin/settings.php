<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: settings.php
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
$jobs_settings = get_settings('jobs');

$link = JOBS.'admin.php'.fusion_get_aidlink().'&section=settings';

if (isset($_POST['savesettings'])) {
    $settings = [
        'cv_max_b'    => form_sanitizer($_POST['calc_b'], 5242880, 'calc_b') * form_sanitizer($_POST['calc_c'], 1, 'calc_c'),
        'cv_types'    => form_sanitizer($_POST['cv_types'], '.doc,.docx,.docm,.txt', 'cv_types'),
        'internship'  => isset($_POST['internship']) ? 1 : 0,
        'email'       => form_sanitizer($_POST['email'], fusion_get_settings('siteemail'), 'email'),
        'captcha'     => form_sanitizer($_POST['captcha'], 0, 'captcha'),
        'required_cv' => isset($_POST['required_cv']) ? 1 : 0
    ];

    if (\defender::safe()) {
        foreach ($settings as $key => $value) {
            if (\defender::safe()) {
                $data = [
                    'settings_name'  => $key,
                    'settings_value' => $value,
                    'settings_inf'   => 'jobs'
                ];
                dbquery_insert(DB_SETTINGS_INF, $data, 'update', ['primary_key' => 'settings_name']);
            }
        }

        addNotice('success', $locale['jb_164']);
    }

    redirect($link);
}

$calc_opts = fusion_get_locale('1020', LOCALE.LOCALESET.'admin/settings.php');
$calc_c = calculate_byte($jobs_settings['cv_max_b']);
$calc_b = $jobs_settings['cv_max_b'] / $calc_c;

echo openform('settingsform', 'post', FUSION_REQUEST);
echo '<div class="row">';

echo '<div class="col-xs-12 col-sm-6">';
openside('');
echo form_text('email', $locale['jb_165'], $jobs_settings['email'], [
    'type' => 'email'
]);

echo '<div class="display-block overflow-hide">';
echo '<label class="control-label col-xs-12 p-l-0" for="calc_b">'.$locale['jb_166'].'</label>';
echo form_text('calc_b', '', $calc_b, [
    'required'   => TRUE,
    'type'       => 'number',
    'inline'     => TRUE,
    'width'      => '100px',
    'max_length' => 4,
    'class'      => 'pull-left m-r-10'
]);
echo form_select('calc_c', '', $calc_c, [
    'options'     => $calc_opts,
    'placeholder' => $locale['choose'],
    'class'       => 'pull-left',
    'inner_width' => '100%',
    'width'       => '180px'
]);
echo '</div>';

$mime = mimeTypes();
$mime_opts = [];

foreach ($mime as $m => $Mime) {
    $ext = ".$m";
    $mime_opts[$ext] = $ext;
}

echo form_select('cv_types[]', $locale['jb_167'], $jobs_settings['cv_types'], [
    'options'     => $mime_opts,
    'input_id'    => 'cvtype',
    'placeholder' => $locale['choose'],
    'multiple'    => TRUE,
    'tags'        => TRUE,
    'width'       => '100%'
]);
closeside();
echo '</div>';

echo '<div class="col-xs-12 col-sm-6">';
openside('');

echo form_select('captcha', $locale['jb_168'], $jobs_settings['captcha'], [
    'options' => [$locale['disable'], $locale['enable']]
]);

echo form_checkbox('internship', $locale['jb_148'], $jobs_settings['internship']);

echo form_checkbox('required_cv', $locale['jb_167a'], $jobs_settings['required_cv']);

closeside();
echo '</div>';

echo '</div>';

echo form_button('savesettings', $locale['save'], $locale['save'], ['class' => 'btn-success', 'icon' => 'fa fa-hdd-o']);
echo closeform();
