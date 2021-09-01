<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: categories.php
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

$link = JOBS.'admin.php'.fusion_get_aidlink().'&section=categories';

$data = [
    'job_cat_id'       => 0,
    'job_cat_name'     => '',
    'job_cat_language' => LANGUAGE
];

if ((isset($_GET['action']) && $_GET['action'] == 'delete') && (isset($_GET['cat_id']) && isnum($_GET['cat_id']))) {
    dbquery("DELETE FROM ".DB_JOB_CATS." WHERE job_cat_id='".intval($_GET['cat_id'])."'");
    addNotice('success', $locale['jb_131']);
    redirect($link);
}

if (isset($_POST['save_cat'])) {
    $data = [
        'job_cat_id'       => form_sanitizer($_POST['job_cat_id'], '', 'job_cat_id'),
        'job_cat_name'     => form_sanitizer($_POST['job_cat_name'], '', 'job_cat_name'),
        'job_cat_language' => form_sanitizer($_POST['job_cat_language'], LANGUAGE, 'job_cat_language')
    ];

    if (dbcount('(job_cat_id)', DB_JOB_CATS, "job_cat_id='".$data['job_cat_id']."'")) {
        dbquery_insert(DB_JOB_CATS, $data, 'update');

        if (\defender::safe()) {
            addNotice('success', $locale['jb_132']);
            redirect($link);
        }
    } else {
        dbquery_insert(DB_JOB_CATS, $data, 'save');
        if (\defender::safe()) {
            addNotice('success', $locale['jb_133']);
            redirect($link);
        }
    }
}

if ((isset($_GET['action']) && $_GET['action'] == 'edit') && (isset($_GET['cat_id']) && isnum($_GET['cat_id']))) {
    $result = dbquery("SELECT * FROM ".DB_JOB_CATS." WHERE job_cat_id='".$_GET['cat_id']."'");

    if (dbrows($result)) {
        $data = dbarray($result);
    } else {
        redirect(clean_request('', ['section', 'aid']));
    }
}

echo '<div class="row">';
    echo '<div class="col-xs-12 col-sm-6">';
        echo openform('inputform', 'post');
        echo form_hidden('job_cat_id', '', $data['job_cat_id']);

        echo form_text('job_cat_name', $locale['jb_134'], $data['job_cat_name'], [
            'required'   => TRUE,
            'error_text' => $locale['jb_135']
        ]);

        if (multilang_table('JB')) {
            echo form_select('job_cat_language[]', $locale['global_ML100'], $data['job_cat_language'], [
                'options'     => fusion_get_enabled_languages(),
                'placeholder' => $locale['choose'],
                'width'       => '100%',
                'multiple'    => TRUE
            ]);
        } else {
            echo form_hidden('job_cat_language', '', $data['job_cat_language']);
        }

        echo form_button('save_cat', $locale['save'], $locale['save'], ['class' => 'btn-success', 'icon' => 'fa fa-hdd-o']);

        echo closeform();
    echo '</div>';

    echo '<div class="col-xs-12 col-sm-6">';
        echo '<div class="list-group">';

            $result = dbquery("SELECT * FROM ".DB_JOB_CATS.(multilang_table('JB') ? " WHERE ".in_group('job_cat_language', LANGUAGE) : ''));

            if (dbrows($result) > 0) {
                while ($data = dbarray($result)) {
                    echo '<div class="list-group-item">';
                        echo $data['job_cat_name'];
                        echo '<div class="pull-right">';
                            echo '<a href="'.$link.'&action=edit&cat_id='.$data['job_cat_id'].'">'.$locale['edit'].'</a>';
                            echo ' | <a class="text-danger" href="'.$link.'&action=delete&cat_id='.$data['job_cat_id'].'">'.$locale['delete'].'</a>';
                        echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="list-group-item">'.$locale['jb_136'].'</div>';
            }

        echo '</div>';
    echo '</div>';

echo '</div>';
