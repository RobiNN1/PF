<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: admin.php
| Author: YOUR_NAMELICENSE_TEXT
+--------------------------------------------------------*/
require_once '../../maincore.php';
require_once THEMES.'templates/admin_header.php';

pageaccess('ADMIN_RIGHTS');

$locale = fusion_get_locale('', ADMIN_RIGHTS_LOCALE);

opentable($locale['LOCALE_PREFIX_title']);
// your code here
closetable();

require_once THEMES.'templates/footer.php';
