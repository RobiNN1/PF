<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: pp.php
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

$locale = fusion_get_locale();

$data = [
    'legal_id'       => 0,
    'legal_type'     => 'pp',
    'legal_text'     => '',
    'legal_language' => LANGUAGE
];

$link = LEGAL.'admin.php'.fusion_get_aidlink().'&section=pp';

if ((isset($_GET['action']) && $_GET['action'] == 'delete') && (isset($_GET['legal_id']) && isnum($_GET['legal_id']))) {
    dbquery("DELETE FROM ".DB_LEGAL." WHERE legal_id='".$_GET['legal_id']."'");

    addNotice('success', $locale['lg_07']);
    redirect($link);
}

if (isset($_POST['save_item'])) {
    $data = [
        'legal_id'       => form_sanitizer($_POST['legal_id'], '', 'legal_id'),
        'legal_type'     => form_sanitizer($_POST['legal_type'], '', 'legal_type'),
        'legal_text'     => form_sanitizer($_POST['legal_text'], '', 'legal_text'),
        'legal_language' => form_sanitizer($_POST['legal_language'], '', 'legal_language')
    ];

    if (dbcount('(legal_id)', DB_LEGAL, "legal_id='".$data['legal_id']."'")) {
        dbquery_insert(DB_LEGAL, $data, 'update');

        if (\defender::safe()) {
            addNotice('success', $locale['lg_06']);
            redirect($link);
        }
    } else {
        dbquery_insert(DB_LEGAL, $data, 'save');
        if (\defender::safe()) {
            addNotice('success', $locale['lg_05']);
            redirect($link);
        }
    }
}

if ((isset($_GET['action']) && $_GET['action'] == 'edit') && (isset($_GET['legal_id']) && isnum($_GET['legal_id']))) {
    $result = dbquery("SELECT * FROM ".DB_LEGAL." WHERE legal_id='".$_GET['legal_id']."' AND legal_type = 'pp'");

    if (dbrows($result)) {
        $data = dbarray($result);
    } else {
        redirect(clean_request('', ['section', 'aid'], TRUE));
    }
}

echo '<div class="row">';

echo '<div class="col-xs-12 col-sm-8">';
echo openform('inputform', 'post', FUSION_REQUEST);
echo form_hidden('legal_id', '', $data['legal_id']);
echo form_hidden('legal_type', '', $data['legal_type']);

echo form_textarea('legal_text', $locale['lg_02'], $data['legal_text'], [
    'autosize'  => 1,
    'form_name' => 'inputform',
    'html'      => !fusion_get_settings('tinymce_enabled'),
    'required'  => TRUE
]);

echo form_select('legal_language[]', $locale['global_ML100'], $data['legal_language'], [
    'options'     => fusion_get_enabled_languages(),
    'placeholder' => $locale['choose'],
    'width'       => '100%',
    'multiple'    => TRUE
]);

echo form_button('save_item', $locale['save'], $locale['save'], ['class' => 'btn-success', 'icon' => 'fa fa-hdd-o']);
echo closeform();
echo '</div>';

echo '<div class="col-xs-12 col-sm-4">';
echo '<div class="list-group">';

$result = dbquery("SELECT * FROM ".DB_LEGAL." WHERE legal_type = 'pp'");

if (dbrows($result) > 0) {
    while ($data = dbarray($result)) {
        echo '<div class="list-group-item">';
        echo '#'.$data['legal_id'].' ';
        echo $data['legal_language'];
        echo '<div class="pull-right">';
        echo '<a href="'.$link.'&action=edit&legal_id='.$data['legal_id'].'">'.$locale['edit'].'</a>';
        echo ' | <a class="text-danger" href="'.$link.'&action=delete&legal_id='.$data['legal_id'].'">'.$locale['delete'].'</a>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo '<div class="list-group-item">'.$locale['lg_04'].'</div>';
}

echo '</div>';
echo '</div>';

echo '</div>';
