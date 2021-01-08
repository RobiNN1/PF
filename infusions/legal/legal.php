<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: legal.php
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
require_once __DIR__.'/../../maincore.php';

if (!defined('LEGAL_EXIST')) {
    redirect(BASEDIR.'error.php?code=404');
}

require_once THEMES.'templates/header.php';
require_once LEGAL.'templates/legal.tpl.php';

$locale = fusion_get_locale('', LG_LOCALE);

$text = $locale['lg_04'];
$title = $locale['lg_title'];

$_GET['page'] = isset($_GET['page']) && in_array($_GET['page'], ['pp', 'cp']) ? stripinput($_GET['page']) : '';

if (!empty($_GET['page'])) {
    $data = dbarray(dbquery("SELECT * FROM ".DB_LEGAL." WHERE legal_type = '".((string) $_GET['page'])."' AND ".in_group('legal_language', LANGUAGE)));

    if (!empty($data['legal_text'])) {
        $text = parse_textarea($data['legal_text'], TRUE, FALSE, TRUE, IMAGES, TRUE);

        switch ($data['legal_type']) {
            case 'pp':
                $title = $locale['lg_02'];
                break;
            case 'cp':
                $title = $locale['lg_03'];
                break;
        }
    }
}

set_title($title);

render_legal($text, $title);

require_once THEMES.'templates/footer.php';
