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

pageaccess('SCL');

$locale = fusion_get_locale('', SCL_LOCALE);

add_to_title($locale['scl_title']);

add_breadcrumb(['link' => INFUSIONS.'script_loader/admin.php'.fusion_get_aidlink(), 'title' => $locale['scl_title']]);

opentable($locale['scl_title']);

$allowed_section = ['head', 'footer'];
$_GET['section'] = isset($_GET['section']) && in_array($_GET['section'], $allowed_section) ? $_GET['section'] : 'head';

$tab['title'][] = $locale['scl_head'];
$tab['id'][] = 'head';
$tab['icon'][] = '';

$tab['title'][] = $locale['scl_footer'];
$tab['id'][] = 'footer';
$tab['icon'][] = '';

if (isset($_POST['savecode'])) {
    $data = [
        'type' => form_sanitizer($_POST['type'], '', 'type'),
        'code' => form_sanitizer($_POST['code'], '', 'code')
    ];

    if (\defender::safe()) {
        dbquery_insert(DB_SCRIPT_LOADER, $data, 'update', ['primary_key' => 'type']);
        addnotice('success', $locale['scl_notice']);
    }

    redirect(FUSION_REQUEST);
}

echo opentab($tab, $_GET['section'], 'scriptsadmin', TRUE, 'nav-tabs');
$type = '';
switch ($_GET['section']) {
    case 'head':
        $type = 'head';
        break;
    case 'footer':
        $type = 'footer';
        break;
}

$result = dbquery("SELECT * FROM ".DB_SCRIPT_LOADER." WHERE type=:type", [':type' => $type]);
$data = dbarray($result);

echo openform('codeform', 'post', FUSION_REQUEST);
echo form_hidden('type', 'type', $data['type']);

echo '<div class="well m-b-10">'.$locale['scl_'.$type.'_desc'].'</div>';

echo form_textarea('code', $locale['scl_code'], $data['code'], [
    'censor_words' => FALSE,
    'descript'     => FALSE
]);
echo form_button('savecode', $locale['save'], 'savecode');
echo closeform();

echo closetab();
closetable();

require_once THEMES.'templates/footer.php';
