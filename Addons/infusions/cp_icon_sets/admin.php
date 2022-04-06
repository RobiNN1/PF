<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
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

pageaccess('CIS');

$locale = fusion_get_locale();
$cp_settings = get_settings('cp_icon_sets');

opentable('Icon sets');

if (check_post('save_settings')) {
    $settings = [
        'icon_set' => sanitizer('icon_set', '', 'icon_set')
    ];

    if (\defender::safe()) {
        foreach ($settings as $settings_name => $settings_value) {
            $db = [
                'settings_name'  => $settings_name,
                'settings_value' => $settings_value,
                'settings_inf'   => 'cp_icon_sets'
            ];

            dbquery_insert(DB_SETTINGS_INF, $db, 'update', ['primary_key' => 'settings_name']);
        }

        addnotice('success', $locale['settings_updated']);
        redirect(FUSION_REQUEST);
    }
}

openside('');
echo openform('cp_settings', 'post', FUSION_REQUEST);

$sets = [];
$sets_list = makefilelist(CP_ICON_SETS, '.|..', TRUE, 'folders');

foreach ($sets_list as $set) {
    $sets[$set] = str_replace('_', ' ', ucfirst($set));
}

echo form_select('icon_set', 'Icon set', $cp_settings['icon_set'], [
    'options' => $sets
]);

echo form_button('save_settings', $locale['save'], $locale['save'], ['class' => 'btn-success', 'icon' => 'fa fa-hdd-o']);
echo closeform();
closeside();

closetable();

require_once THEMES.'templates/footer.php';
