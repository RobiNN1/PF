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

pageaccess('WIKI');

class WikiAdmin {
    private $locale;

    public function __construct() {
        $this->locale = fusion_get_locale('', WIKI_LOCALE);

        if (isset($_GET['section']) && $_GET['section'] == 'back') {
            redirect(FUSION_SELF.fusion_get_aidlink());
        }
    }

    public function DisplayAdmin() {
        add_to_title($this->locale['wiki_title']);

        add_breadcrumb(['link' => WIKI.'admin.php'.fusion_get_aidlink(), 'title' => $this->locale['wiki_title']]);

        opentable($this->locale['wiki_title']);

        $edit = (isset($_GET['action']) && $_GET['action'] == 'edit') && isset($_GET['wiki_id']);
        $allowed_section = ['list', 'form', 'categories', 'changelog', 'submissions', 'settings'];
        $_GET['section'] = isset($_GET['section']) && in_array($_GET['section'], $allowed_section) ? $_GET['section'] : 'list';

        if (isset($_GET['section']) && $_GET['section'] == 'form' || isset($_GET['ref'])) {
            $tab['title'][] = $this->locale['back'];
            $tab['id'][]    = 'back';
            $tab['icon'][]  = 'fa fa-fw fa-arrow-left';
        }

        if (!isset($_GET['section']) && isset($_GET['ref']) && $_GET['ref'] == 'form') {
            $title = $edit ? $this->locale['edit'] : $this->locale['add'];
            $icon = 'fa fa-'.($edit ? 'pencil' : 'plus');
        } else {
            $title = $this->locale['wiki_title'];
            $icon = 'fa fa-fw fa-wikipedia-w';
        }

        $tab['title'][] = $title;
        $tab['id'][]    = 'list';
        $tab['icon'][]  = $icon;
        $tab['title'][] = $this->locale['wiki_001'];
        $tab['id'][]    = 'categories';
        $tab['icon'][]  = 'fa fa-folder';
        $tab['title'][] = $this->locale['wiki_002'];
        $tab['id'][]    = 'changelog';
        $tab['icon'][]  = 'fa fa-clipboard-list';
        $tab['title'][] = $this->locale['wiki_003'].'&nbsp;<span class="badge">'.dbcount("(submit_id)", DB_SUBMISSIONS, "submit_type='w'").'</span>';
        $tab['id'][]    = 'submissions';
        $tab['icon'][]  = 'fa fa-inbox';
        $tab['title'][] = $this->locale['wiki_004'];
        $tab['id'][]    = 'settings';
        $tab['icon'][]  = 'fa fa-cogs';

        echo opentab($tab, $_GET['section'], 'wikiadmin', TRUE, 'nav-tabs');
        switch ($_GET['section']) {
            case 'categories':
                require_once 'admin/wiki_cats.php';
                add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $this->locale['wiki_001']]);
                break;
            case 'changelog':
                require_once 'admin/wiki_changelog.php';
                add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $this->locale['wiki_002']]);
                break;
            case 'submissions':
                require_once 'admin/wiki_submissions.php';
                add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $this->locale['wiki_003']]);
                break;
            case 'settings':
                require_once 'admin/wiki_settings.php';
                add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $this->locale['wiki_004']]);
                break;
            default:
                if (dbcount("(wiki_cat_id)", DB_WIKI_CATS)) {
                    require_once 'admin/wiki.php';
                } else {
                    echo '<div class="well text-center">'.$this->locale['wiki_005'].'</div>';
                }

                if (isset($_GET['ref']) && $_GET['ref'] == 'form') {
                    add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $edit ? $this->locale['edit'] : $this->locale['add']]);
                }
                break;
        }
        echo closetab();

        closetable();
    }
}

$wiki = new WikiAdmin();
$wiki->DisplayAdmin();

require_once THEMES.'templates/footer.php';
