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

require_once INFUSIONS.'sitemap/includes/SitemapGenerator.php';

pageaccess('SMG');

$smg = new SitemapGenerator();
$smg->displayAdmin();

require_once THEMES.'templates/footer.php';
