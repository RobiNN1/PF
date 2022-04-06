<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: wiki/index.php
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

if (!defined('WIKI_EXISTS')) {
    redirect(BASEDIR.'error.php?code=404');
}

require_once THEMES.'templates/header.php';
require_once WIKI.'includes/functions.php';
require_once WIKI.'templates/wiki.tpl.php';

$locale = fusion_get_locale('', WIKI_LOCALE);
$aidlink = fusion_get_aidlink();

set_title($locale['wiki_title']);

add_breadcrumb(['link' => WIKI, 'title' => $locale['wiki_title']]);

add_to_footer('<script src="'.WIKI.'includes/ajax/scripts.min.js"></script>');

$info = [
    'no_pages' => ''
];

if (isset($_GET['page']) && $_GET['page'] === 'changelog') {
    add_to_title(': '.$locale['wiki_002']);

    add_breadcrumb(['link' => WIKI, 'title' => $locale['wiki_002']]);

    $result = dbquery("SELECT *
        FROM ".DB_WIKI_CHANGELOG."
        WHERE ".(multilang_column('WIKI') ? "wiki_changelog_language='".LANGUAGE."' AND" : '')."
        ".groupaccess('wiki_changelog_access')." AND wiki_changelog_status = 1
        ORDER BY wiki_changelog_version DESC
    ");

    if (dbrows($result) > 0) {
        while ($data = dbarray($result)) {
            $info['versions'][] = $data['wiki_changelog_version'];

            $changes = strtr($data['wiki_changelog_changes'], [
                '[ADDED]'    => '<label class="label label-success">'.$locale['wiki_042'].'</label>',
                '[UPDATED]'  => '<label class="label label-info">'.$locale['wiki_043'].'</label>',
                '[FIXED]'    => '<label class="label label-primary">'.$locale['wiki_044'].'</label>',
                '[IMPROVED]' => '<label class="label label-warning">'.$locale['wiki_045'].'</label>',
                '[REMOVED]'  => '<label class="label label-danger">'.$locale['wiki_046'].'</label>'
            ]);
            $changes = parse_text($changes, [
                'decode'               => FALSE,
                'default_image_folder' => NULL,
                'add_line_breaks'      => TRUE
            ]);

            $admin_link = [];
            if (iADMIN && checkrights('WIKI')) {
                $admin_link = [
                    'edit'   => WIKI.'admin.php'.$aidlink.'&section=changelog&ref=form&action=edit&log_id='.$data['wiki_changelog_id'],
                    'delete' => WIKI.'admin.php'.$aidlink.'&section=changelog&ref=form&action=delete&log_id='.$data['wiki_changelog_id']
                ];
            }

            $info['changelog'][] = [
                'version'    => $data['wiki_changelog_version'],
                'codename'   => !empty($data['wiki_changelog_codename']) ? $locale['wiki_047'].': '.$data['wiki_changelog_codename'] : '',
                'published'  => showdate('%B %d, %Y', $data['wiki_changelog_published']),
                'download'   => $data['wiki_changelog_download'],
                'changes'    => $changes,
                'admin_link' => $admin_link
            ];
        }
    } else {
        $info['no_pages'] = $locale['wiki_300'];
    }
} else if (isset($_GET['cat_id'])) {
    if (validate_wiki_cat($_GET['cat_id'])) {
        require_once WIKI.'includes/OpenGraphWiki.php';

        $data = dbarray(dbquery("SELECT wiki_cat_id, wiki_cat_name, wiki_cat_description FROM ".DB_WIKI_CATS." WHERE ".(multilang_column('WIKI') ? in_group('wiki_cat_language', LANGUAGE)." AND " : '')." wiki_cat_id=:wiki_cat_id", [':wiki_cat_id' => intval($_GET['cat_id'])]));
        add_to_title(': '.$data['wiki_cat_name']);
        add_breadcrumb(['link' => WIKI.'index.php?cat_id='.intval($_GET['cat_id']), 'title' => $data['wiki_cat_name']]);
        $info['cat_name'] = $data['wiki_cat_name'];
        $info['description'] = parse_text($info['description'], ['add_line_breaks' => TRUE]);

        $admin_link = [];
        if (iADMIN && checkrights('WIKI')) {
            $admin_link = [
                'edit'   => WIKI.'admin.php'.$aidlink.'&section=categories&ref=wiki_cat_form&action=edit&cat_id='.$data['wiki_cat_id'],
                'delete' => WIKI.'admin.php'.$aidlink.'&section=categories&ref=wiki_cat_form&action=delete&cat_id='.$data['wiki_cat_id']
            ];
        }

        $info['admin_link'] = $admin_link;

        OpenGraphWiki::ogWikiCat($_GET['cat_id']);

        $pages = dbquery(get_wiki_query(['condition' => "w.wiki_cat=:cat_id AND w.wiki_parent=0", 'order' => 'w.wiki_order']), [':cat_id' => intval($_GET['cat_id'])]);

        if (dbrows($pages) > 0) {
            while ($page = dbarray($pages)) {
                $info['pages'][] = $page;
            }
        } else {
            $info['no_pages'] = $locale['wiki_301'];
        }

    } else {
        redirect(WIKI);
    }
} else {
    $info += [
        'no_cats'  => '',
        'no_pages' => ''
    ];

    $result_cats = dbquery("SELECT *
        FROM ".DB_WIKI_CATS."
        ORDER BY wiki_cat_order
    ");

    if (dbrows($result_cats) > 0) {
        while ($categorie = dbarray($result_cats)) {
            $info['categories'][] = $categorie;
        }
    } else {
        $info['no_cats'] = $locale['wiki_039'];
    }

    $result_pages = dbquery(get_wiki_query(['condition' => "w.wiki_type='page' AND w.wiki_parent=0", 'order' => 'w.wiki_datestamp DESC', 'limit' => 12]));

    if (dbrows($result_pages) > 0) {
        require_once WIKI.'includes/Parsedown.php';
        $parsedown = new Parsedown;

        while ($page = dbarray($result_pages)) {
            $page['wiki_description'] = trim_text(strip_tags(parse_wiki_text($page['wiki_description'])), 200);
            $page['wiki_description'] = strip_tags($parsedown->text($page['wiki_description']));
            $info['latest_pages'][] = $page;
        }
    } else {
        $info['no_pages'] = $locale['wiki_301'];
    }
}

render_wiki($info);

require_once THEMES.'templates/footer.php';
