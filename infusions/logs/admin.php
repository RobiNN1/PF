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

pageaccess('LOG');

$locale = fusion_get_locale('', LOG_LOCALE);

add_to_title($locale['log_title']);

add_breadcrumb(['link' => INFUSIONS.'logs/admin.php'.fusion_get_aidlink(), 'title' => $locale['log_title']]);

opentable($locale['log_title']);

$allowed_section = ['urls'];
$_GET['section'] = isset($_GET['section']) && in_array($_GET['section'], $allowed_section) ? $_GET['section'] : 'urls';

$tab['title'][] = $locale['log_01'];
$tab['id'][]    = 'urls';
$tab['icon'][]  = '';

echo opentab($tab, $_GET['section'], 'logsadmin', TRUE, 'nav-tabs');
switch ($_GET['section']) {
    case 'urls':
        require_once 'admin/urls.php';
        break;
}
echo closetab();

closetable();

require_once THEMES.'templates/footer.php';
