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

pageAccess('LG');

$locale = fusion_get_locale('', LG_LOCALE);

add_to_title($locale['lg_title']);

add_breadcrumb(['link' => INFUSIONS.'legal/admin.php'.fusion_get_aidlink(), 'title' => $locale['lg_title']]);

opentable($locale['lg_title']);

$allowed_section = ['pp', 'cp', 'tos'];
$_GET['section'] = isset($_GET['section']) && in_array($_GET['section'], $allowed_section) ? $_GET['section'] : 'pp';

$tab['title'][] = $locale['lg_02'];
$tab['id'][]    = 'pp';
$tab['icon'][]  = '';
$tab['title'][] = $locale['lg_03'];
$tab['id'][]    = 'cp';
$tab['icon'][]  = '';
$tab['title'][] = $locale['lg_01'];
$tab['id'][]    = 'tos';
$tab['icon'][]  = '';

echo opentab($tab, $_GET['section'], 'legaladmin', TRUE, 'nav-tabs m-b-20');
switch ($_GET['section']) {
    case 'pp':
        require_once 'admin/pp.php';
        break;
    case 'cp':
        require_once 'admin/cp.php';
        break;
    case 'tos':
        require_once 'admin/tos.php';
        break;
}
echo closetab();

closetable();

require_once THEMES.'templates/footer.php';
