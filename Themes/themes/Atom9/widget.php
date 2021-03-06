<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: widget.php
| Author: Frederick MC Chan
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
$settings = get_theme_settings('Atom9');
$locale = fusion_get_locale('', ATOM9_LOCALE);

$filter = '.|..|.htaccess|.DS_Store|index.php';

if (isset($_POST['save_settings'])) {
    $settings = [
        'ignition_pack'   => form_sanitizer($_POST['ignition_pack'], '', 'ignition_pack'),
        'facebook_url'    => form_sanitizer($_POST['facebook_url'], '', 'facebook_url'),
        'twitter_url'     => form_sanitizer($_POST['twitter_url'], '', 'twitter_url'),
        'panel_exlude'    => form_sanitizer($_POST['panel_exlude'], '', 'panel_exlude'),
        'footer_col1'     => form_sanitizer($_POST['footer_col1'], '', 'footer_col1'),
        'footer_col2'     => form_sanitizer($_POST['footer_col2'], '', 'footer_col2'),
        'footer_col3'     => form_sanitizer($_POST['footer_col3'], '', 'footer_col3'),
        'footer_col4'     => form_sanitizer($_POST['footer_col4'], '', 'footer_col4'),
        '2columns_layout' => form_sanitizer($_POST['2columns_layout'], 0, '2columns_layout'),
        'column_side'     => form_sanitizer($_POST['column_side'], '', 'column_side')
    ];

    if (\defender::safe()) {
        foreach ($settings as $settings_name => $settings_value) {
            $db = [
                'settings_name'  => $settings_name,
                'settings_value' => $settings_value,
                'settings_theme' => 'Atom9'
            ];

            dbquery_insert(DB_SETTINGS_THEME, $db, 'update');
        }

        addnotice('success', $locale['a9_1001']);
        redirect(FUSION_REQUEST);
    }
}

$ignition_packs = makefilelist(THEME.'IgnitionPacks/', $filter, TRUE, 'folders');
$packs = [];
foreach ($ignition_packs as $pack) {
    $packs[$pack] = $pack;
}

echo openform('main_settings', 'post', FUSION_REQUEST);

openside('');
echo form_select('ignition_pack', $locale['a9_1002'], $settings['ignition_pack'], [
    'options'  => $packs,
    'required' => TRUE,
    'inline'   => TRUE
]);
closeside();

openside('');
echo form_text('facebook_url', $locale['a9_1003'], $settings['facebook_url'], ['type' => 'url', 'inline' => TRUE]);
echo form_text('twitter_url', $locale['a9_1004'], $settings['twitter_url'], ['type' => 'url', 'inline' => TRUE]);
closeside();

openside('');
echo form_select('2columns_layout', $locale['a9_1010'], $settings['2columns_layout'], [
    'options' => [
        0 => $locale['no'],
        1 => $locale['yes']
    ],
    'inline' => TRUE
]);
echo form_select('column_side', $locale['a9_1011'], $settings['column_side'], [
    'options' => [
        'LEFT'  => $locale['a9_1012'],
        'RIGHT' => $locale['a9_1013']
    ],
    'inline' => TRUE
]);
closeside();

openside('');
$panels = [];
$file_list = makefilelist(THEME.'classes/Footer/', $filter);
foreach ($file_list as $files) {
    $panels[$files] = strtr($files, [
        'AboutUs.php'        => $locale['a9_002'],
        'LatestArticles.php' => $locale['a9_003'],
        'LatestBlogs.php'    => $locale['a9_009'],
        'LatestNews.php'     => $locale['a9_006'],
        'Users.php'          => $locale['a9_012']
    ]);
}

echo '<div class="alert alert-info">'.fusion_get_locale('424', LOCALE.LOCALESET.'admin/settings.php').'</div>';

echo form_textarea('panel_exlude', $locale['a9_1005'], $settings['panel_exlude'], ['autosize' => TRUE, 'inline' => TRUE]);
echo form_select('footer_col1', $locale['a9_1006'], $settings['footer_col1'], ['options' => $panels, 'inline' => TRUE]);
echo form_select('footer_col2', $locale['a9_1007'], $settings['footer_col2'], ['options' => $panels, 'inline' => TRUE]);
echo form_select('footer_col3', $locale['a9_1008'], $settings['footer_col3'], ['options' => $panels, 'inline' => TRUE]);
echo form_select('footer_col4', $locale['a9_1009'], $settings['footer_col4'], ['options' => $panels, 'inline' => TRUE]);
closeside();

echo form_button('save_settings', $locale['save_changes'], 'save', ['class' => 'btn-primary']);
echo closeform();
