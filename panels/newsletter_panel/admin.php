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

pageAccess('NSL');

class NewsLetterAdmin {
    private $locale;

    public function __construct() {
        $this->locale = fusion_get_locale('', NSL_LOCALE);

        if (isset($_GET['section']) && $_GET['section'] == 'back') {
            redirect(FUSION_SELF.fusion_get_aidlink());
        }
    }

    public function displayAdmin() {
        add_to_title($this->locale['nsl_title']);

        add_breadcrumb(['link' => INFUSIONS.'newsletter/admin.php'.fusion_get_aidlink(), 'title' => $this->locale['nsl_title']]);

        if (!empty($_GET['section'])) {
            switch ($_GET['section']) {
                case 'newsletter':
                    add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $this->locale['nsl_title']]);
                    break;
                case 'templates':
                    add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $this->locale['nsl_001']]);
                    break;
                case 'subscribers':
                    add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $this->locale['nsl_002']]);
                    break;
                case 'settings':
                    add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $this->locale['nsl_003']]);
                    break;
            }
        }

        opentable($this->locale['nsl_title']);

        $allowed_section = ['newsletter', 'templates', 'subscribers', 'settings'];
        $_GET['section'] = isset($_GET['section']) && in_array($_GET['section'], $allowed_section) ? $_GET['section'] : 'newsletter';

        if (isset($_GET['section']) && $_GET['section'] == 'templates' || $_GET['section'] == 'subscribers') {
            $tab['title'][] = $this->locale['back'];
            $tab['id'][]    = 'back';
            $tab['icon'][]  = 'fa fa-fw fa-arrow-left';
        }

        $tab['title'][] = $this->locale['nsl_title'];
        $tab['id'][]    = 'newsletter';
        $tab['icon'][]  = 'fa fa-fw fa-newspaper';
        $tab['title'][] = $this->locale['nsl_001'];
        $tab['id'][]    = 'templates';
        $tab['icon'][]  = 'fa fa-envelope-open-text';
        $tab['title'][] = $this->locale['nsl_002'];
        $tab['id'][]    = 'subscribers';
        $tab['icon'][]  = 'fa fa-users';
        $tab['title'][] = $this->locale['nsl_003'];
        $tab['id'][]    = 'settings';
        $tab['icon'][]  = 'fa fa-cogs';

        echo opentab($tab, $_GET['section'], 'nsladmin', TRUE, 'nav-tabs m-b-20');
        switch ($_GET['section']) {
            case 'newsletter':
                require_once 'admin/newsletter.php';
                break;
            case 'templates':
                require_once 'admin/templates.php';
                break;
            case 'subscribers':
                require_once 'admin/subscribers.php';
                break;
            case 'settings':
                require_once 'admin/settings.php';
                break;
        }
        echo closetab();

        closetable();
    }
}

$nsl = new NewsLetterAdmin();
$nsl->displayAdmin();

require_once THEMES.'templates/footer.php';
