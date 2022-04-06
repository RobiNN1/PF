<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: wiki_changelog.php
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
defined('IN_FUSION') || exit;

$locale = fusion_get_locale();

$data = [
    'wiki_changelog_id'        => 0,
    'wiki_changelog_version'   => '',
    'wiki_changelog_codename'  => '',
    'wiki_changelog_published' => 0,
    'wiki_changelog_changes'   => '',
    'wiki_changelog_download'  => '',
    'wiki_changelog_status'    => 1,
    'wiki_changelog_access'    => 0,
    'wiki_changelog_language'  => LANGUAGE
];

if (isset($_POST['cancel'])) {
    redirect(FUSION_SELF.fusion_get_aidlink());
}

if ((isset($_GET['action']) && $_GET['action'] == 'delete') && (isset($_GET['log_id']) && isnum($_GET['log_id']))) {
    if (dbcount("(wiki_min_version)", DB_WIKI, "wiki_min_version='".intval($_GET['log_id'])."'")) {
        addnotice('danger', $locale['wiki_214']);
    } else {
        dbquery("DELETE FROM ".DB_WIKI_CHANGELOG." WHERE wiki_changelog_id='".intval($_GET['log_id'])."'");
        addnotice('success', $locale['wiki_215']);
    }
    redirect(clean_request('', ['ref', 'action', 'log_id']));
}

if (isset($_POST['save_page']) || isset($_POST['save_and_close'])) {
    $data = [
        'wiki_changelog_id'        => form_sanitizer($_POST['wiki_changelog_id'], 0, 'wiki_changelog_id'),
        'wiki_changelog_version'   => form_sanitizer($_POST['wiki_changelog_version'], '', 'wiki_changelog_version'),
        'wiki_changelog_codename'  => form_sanitizer($_POST['wiki_changelog_codename'], '', 'wiki_changelog_codename'),
        'wiki_changelog_published' => form_sanitizer($_POST['wiki_changelog_published'], 0, 'wiki_changelog_published'),
        'wiki_changelog_changes'   => form_sanitizer($_POST['wiki_changelog_changes'], '', 'wiki_changelog_changes'),
        'wiki_changelog_download'  => form_sanitizer($_POST['wiki_changelog_download'], '', 'wiki_changelog_download'),
        'wiki_changelog_status'    => form_sanitizer($_POST['wiki_changelog_status'], 0, 'wiki_changelog_status'),
        'wiki_changelog_access'    => form_sanitizer($_POST['wiki_changelog_access'], '', 'wiki_changelog_access'),
        'wiki_changelog_language'  => form_sanitizer($_POST['wiki_changelog_language'], LANGUAGE, 'wiki_changelog_language')
    ];

    if (dbcount("(wiki_changelog_id)", DB_WIKI_CHANGELOG, "wiki_changelog_id='".$data['wiki_changelog_id']."'")) {
        if (\defender::safe()) {
            dbquery_insert(DB_WIKI_CHANGELOG, $data, 'update');
            addnotice('success', $locale['wiki_216']);
        }
    } else {
        if (\defender::safe()) {
            dbquery_insert(DB_WIKI_CHANGELOG, $data, 'save');
            addnotice('success', $locale['wiki_217']);
        }
    }

    if (isset($_POST['save_and_close'])) {
        redirect(clean_request('', ['ref', 'action', 'wiki_id'], FALSE));
    } else {
        redirect(FUSION_REQUEST);
    }
}

if ((isset($_GET['action']) && $_GET['action'] == 'edit') && (isset($_GET['log_id']) && isnum($_GET['log_id']))) {
    $result = dbquery("SELECT * FROM ".DB_WIKI_CHANGELOG." ".(multilang_table('WIKI') ? "WHERE wiki_changelog_language='".LANGUAGE."' AND" : "WHERE")." wiki_changelog_id='".$_GET['log_id']."'");

    if (dbrows($result)) {
        $data = dbarray($result);
    } else {
        redirect(clean_request('', ['section', 'aid']));
    }
}

if (isset($_GET['ref']) && $_GET['ref'] == 'form') {
    echo openform('wikiform', 'post', FUSION_REQUEST, ['enctype' => TRUE]);
    echo form_hidden('wiki_changelog_id', '', $data['wiki_changelog_id']);
    echo '<div class="row">';
        echo '<div class="col-xs-12 col-sm-8">';
            echo form_text('wiki_changelog_version', $locale['wiki_040'], $data['wiki_changelog_version'], [
                'inline'      => TRUE,
                'required'    => TRUE,
                'placeholder' => '9.0'
            ]);

            echo '<label class="control-label display-block" for="wiki_changelog_changes">'.$locale['wiki_041'].'<span class="required">&nbsp;*</span></label>';

            echo '<div class="btn-group btn-group-sm">';
                echo '<button class="btn btn-success" value="[ADDED]" onclick="insertText(\'wiki_changelog_changes\', \'[ADDED]\', \'wikiform\')" type="button">'.$locale['wiki_042'].'</button>';
                echo '<button class="btn btn-info" value="[UPDATED]" onclick="insertText(\'wiki_changelog_changes\', \'[UPDATED]\', \'wikiform\')" type="button">'.$locale['wiki_043'].'</button>';
                echo '<button class="btn btn-primary" value="[FIXED]" onclick="insertText(\'wiki_changelog_changes\', \'[FIXED]\', \'wikiform\')" type="button">'.$locale['wiki_044'].'</button>';
                echo '<button class="btn btn-warning" value="[IMPROVED]" onclick="insertText(\'wiki_changelog_changes\', \'[IMPROVED]\', \'wikiform\')" type="button">'.$locale['wiki_045'].'</button>';
                echo '<button class="btn btn-danger" value="[REMOVED]" onclick="insertText(\'wiki_changelog_changes\', \'[REMOVED]\', \'wikiform\')" type="button">'.$locale['wiki_046'].'</button>';
            echo '</div>';

            echo form_textarea('wiki_changelog_changes', '', $data['wiki_changelog_changes'], [
                'required'  => TRUE,
                'autosize'  => TRUE,
                'html'      => TRUE,
                'inputform' => 'wikiform'
            ]);

        echo '</div>';

        echo '<div class="col-xs-12 col-sm-4">';
            openside('');
            echo form_text('wiki_changelog_codename', $locale['wiki_047'], $data['wiki_changelog_codename'], ['inline' => TRUE]);

            echo form_datepicker('wiki_changelog_published', $locale['wiki_048'], $data['wiki_changelog_published'], ['inline' => TRUE]);

            echo form_text('wiki_changelog_download', $locale['wiki_049'], $data['wiki_changelog_download'], ['inline' => TRUE, 'type' => 'url', 'required' => TRUE]);

            echo form_select('wiki_changelog_status', $locale['wiki_017'], $data['wiki_changelog_status'], [
                'options'     => [0 => $locale['unpublish'], 1 => $locale['publish']],
                'placeholder' => $locale['choose'],
                'inline'      => TRUE
            ]);

            echo form_select('wiki_changelog_access', $locale['wiki_018'], $data['wiki_changelog_access'], ['options' => fusion_get_groups(), 'inline' => TRUE]);

            if (multilang_table('WIKI')) {
                echo form_select('wiki_changelog_language', $locale['global_ML100'], $data['wiki_changelog_language'], [
                    'options'     => fusion_get_enabled_languages(),
                    'placeholder' => $locale['choose'],
                    'inline'      => TRUE
                ]);
            } else {
                echo form_hidden('wiki_changelog_language', '', $data['wiki_changelog_language']);
            }
            closeside();
        echo '</div>';

    echo '</div>';

    echo form_button('cancel', $locale['cancel'], $locale['cancel'], ['class' => 'btn-sm btn-default', 'icon' => 'fa fa-fw fa-times']);
    echo form_button('save_page', $locale['save'], $locale['save'], ['class' => 'btn-sm btn-success m-l-5', 'icon' => 'fa fa-fw fa-hdd-o']);
    echo form_button('save_and_close', $locale['save_and_close'], $locale['save_and_close'], ['class' => 'btn-sm btn-primary m-l-5', 'icon' => 'fa fa-floppy-o']);
    echo closeform();
} else {
    echo '<div class="m-t-15 m-b-20">';
    echo '<div class="clearfix">';
        echo '<div class="pull-right">';
            echo '<a class="btn btn-success btn-sm" href="'.clean_request('ref=form', ['ref'], FALSE).'"><i class="fa fa-fw fa-plus"></i> '.$locale['add'].'</a>';
        echo '</div>';
    echo '</div>';
    echo '</div>';

    $result = dbquery("SELECT * FROM ".DB_WIKI_CHANGELOG." ORDER BY wiki_changelog_version DESC");

    echo '<div class="table-responsive"><table class="table table-hover">';
        echo '<thead><tr>';
            echo '<th>'.$locale['wiki_040'].'</th>';
            echo '<th>'.$locale['wiki_047'].'</th>';
            echo '<th>'.$locale['wiki_017'].'</th>';
            echo '<th>'.$locale['wiki_018'].'</th>';
            echo '<th>'.$locale['language'].'</th>';
            echo '<th>'.$locale['wiki_033'].'</th>';
        echo '</tr></thead>';
        echo '<tbody>';
            if (dbrows($result) > 0) {
                while ($data = dbarray($result)) {
                    $edit_link = clean_request('&ref=form&action=edit&log_id='.$data['wiki_changelog_id'], ['ref', 'action', 'log_id'], FALSE);
                    $delete_link = clean_request('&ref=form&action=delete&log_id='.$data['wiki_changelog_id'], ['ref', 'action', 'log_id'], FALSE);

                    echo '<tr>';
                        echo '<td>'.$data['wiki_changelog_version'].'</td>';
                        echo '<td>'.(!empty($data['wiki_changelog_codename']) ? $data['wiki_changelog_codename'] : 'N/A').'</td>';
                        echo '<td><span class="badge">'.($data['wiki_changelog_status'] == 1 ? $locale['publish'] : $locale['unpublish']).'</span></td>';
                        echo '<td><span class="badge">'.getgroupname($data['wiki_changelog_access']).'</span></td>';
                        echo '<td>'.translate_lang_names($data['wiki_changelog_language']).'</td>';
                        echo '<td>';
                            echo '<a href="'.$edit_link.'" title="'.$locale['edit'].'">'.$locale['edit'].'</a> | ';
                            echo '<a href="'.$delete_link.'" title="'.$locale['delete'].'">'.$locale['delete'].'</a>';
                        echo '</td>';
                    echo '</tr>';
                }
            }
        echo '</tbody>';
    echo '</table></div>';
}
