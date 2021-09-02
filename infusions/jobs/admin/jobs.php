<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: jobs.php
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

$link = JOBS.'admin.php'.fusion_get_aidlink();

$data = [
    'job_id'          => 0,
    'job_cat'         => 0,
    'job_location'    => 0,
    'job_title'       => '',
    'job_description' => ''
];

if ((isset($_GET['action']) && $_GET['action'] == 'delete') && (isset($_GET['job_id']) && isnum($_GET['job_id']))) {
    dbquery("DELETE FROM ".DB_JOBS." WHERE job_id='".intval($_GET['job_id'])."'");
    dbquery("DELETE FROM ".DB_JOB_FAQ." WHERE job_faq_job='".intval($_GET['job_id'])."'");
    addnotice('success', $locale['jb_112']);
    redirect($link);
}

if (isset($_POST['save_job'])) {
    $data = [
        'job_id'          => form_sanitizer($_POST['job_id'], 0, 'job_id'),
        'job_cat'         => form_sanitizer($_POST['job_cat'], 0, 'job_cat'),
        'job_location'    => form_sanitizer($_POST['job_location'], 0, 'job_location'),
        'job_title'       => form_sanitizer($_POST['job_title'], '', 'job_title'),
        'job_description' => form_sanitizer($_POST['job_description'], '', 'job_description')
    ];

    if (dbcount('(job_id)', DB_JOBS, "job_id='".$data['job_id']."'")) {
        dbquery_insert(DB_JOBS, $data, 'update');

        if (\defender::safe()) {
            addnotice('success', $locale['jb_113']);
            redirect(FUSION_REQUEST);
        }
    } else {
        dbquery_insert(DB_JOBS, $data, 'save');
        if (\defender::safe()) {
            addnotice('success', $locale['jb_114']);
            redirect($link);
        }
    }
}

if ((isset($_GET['action']) && $_GET['action'] == 'edit') && (isset($_GET['job_id']) && isnum($_GET['job_id']))) {
    $result = dbquery("SELECT * FROM ".DB_JOBS." WHERE job_id='".$_GET['job_id']."'");

    if (dbrows($result)) {
        $data = dbarray($result);
    } else {
        redirect(clean_request('', ['section', 'aid']));
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'faq' && (isset($_GET['job_id']) && isnum($_GET['job_id']))) {
    if (isset($_GET['move']) && isset($_GET['edit_faq']) && isnum($_GET['edit_faq'])) {
        $data = dbarray(dbquery("SELECT job_faq_id, job_faq_order FROM ".DB_JOB_FAQ." WHERE job_faq_id = '".intval($_GET['edit_faq'])."'"));
        if ($_GET['move'] == 'md') {
            dbquery("UPDATE ".DB_JOB_FAQ." SET job_faq_order = job_faq_order - 1 WHERE job_faq_order = '".($data['job_faq_order'] + 1)."'");
            dbquery("UPDATE ".DB_JOB_FAQ." SET job_faq_order = job_faq_order + 1 WHERE job_faq_id = '".$data['job_faq_id']."'");
        }
        if ($_GET['move'] == 'mup') {
            dbquery("UPDATE ".DB_JOB_FAQ." SET job_faq_order = job_faq_order + 1 WHERE job_faq_order = '".($data['job_faq_order'] - 1)."'");
            dbquery("UPDATE ".DB_JOB_FAQ." SET job_faq_order = job_faq_order - 1 WHERE job_faq_id = '".$data['job_faq_id']."'");
        }
        addnotice('success', $locale['jb_116']);
        redirect($link.'&section=form&action=faq&job_id='.$_GET['job_id'].'&edit_faq='.$_GET['edit_faq']);
    }

    echo '<div class="m-b-20 pull-right"><a class="btn btn-default" href="'.$link.'&action=edit&job_id='.$_GET['job_id'].'">'.$locale['back'].'</a></div>';

    $data = [
        'job_faq_id'       => 0,
        'job_faq_job'      => $_GET['job_id'],
        'job_faq_question' => '',
        'job_faq_answer'   => ''
    ];

    if ((isset($_GET['delete_faq']) && isnum($_GET['delete_faq']))) {
        dbquery("DELETE FROM ".DB_JOB_FAQ." WHERE job_faq_id='".intval($_GET['delete_faq'])."'");
        addnotice('success', $locale['jb_115']);
        redirect($link.'&action=faq&job_id='.$_GET['job_id']);
    }

    if (isset($_POST['save_job_faq'])) {
        $data = [
            'job_faq_id'       => form_sanitizer($_POST['job_faq_id'], '', 'job_faq_id'),
            'job_faq_job'      => $_GET['job_id'],
            'job_faq_question' => form_sanitizer($_POST['job_faq_question'], '', 'job_faq_question'),
            'job_faq_answer'   => form_sanitizer($_POST['job_faq_answer'], '', 'job_faq_answer')
        ];

        if (dbcount('(job_faq_id)', DB_JOB_FAQ, "job_faq_id='".$data['job_faq_id']."'")) {
            dbquery_insert(DB_JOB_FAQ, $data, 'update');

            if (\defender::safe()) {
                addnotice('success', $locale['jb_116']);
                redirect(FUSION_REQUEST);
            }
        } else {
            dbquery_insert(DB_JOB_FAQ, $data, 'save');
            if (\defender::safe()) {
                addnotice('success', $locale['jb_117']);
                redirect($link.'&section=form&action=faq&job_id='.$_GET['job_id']);
            }
        }
    }

    if ((isset($_GET['edit_faq']) && isnum($_GET['edit_faq']))) {
        $result = dbquery("SELECT * FROM ".DB_JOB_FAQ." WHERE job_faq_id='".$_GET['edit_faq']."'");

        if (dbrows($result)) {
            $data = dbarray($result);
        } else {
            redirect(clean_request('', ['section', 'aid']));
        }
    }

    echo '<div class="row">';

        echo '<div class="col-xs-12 col-sm-6">';

            echo openform('inputform', 'post');
            echo form_hidden('job_faq_id', '', $data['job_faq_id']);

            echo form_text('job_faq_question', $locale['jb_118'], $data['job_faq_question'], [
                'required'   => TRUE,
                'error_text' => $locale['jb_119']
            ]);

            echo form_textarea('job_faq_answer', $locale['jb_120'], $data['job_faq_answer'], [
                'required'   => TRUE,
                'error_text' => $locale['jb_121'],
                'type'       => 'html',
                'form_name'  => 'inputform',
            ]);

            echo form_button('save_job_faq', $locale['save'], $locale['save'], ['class' => 'btn-success', 'icon' => 'fa fa-hdd-o']);

            echo closeform();

        echo '</div>';

        echo '<div class="col-xs-12 col-sm-6">';
            echo '<div class="list-group">';

                $result = dbquery("SELECT * FROM ".DB_JOB_FAQ." WHERE job_faq_job = :job_id ORDER BY job_faq_order", [':job_id' => $_GET['job_id']]);

                if (dbrows($result) > 0) {
                    while ($data = dbarray($result)) {
                        echo '<div class="list-group-item">';
                            echo $data['job_faq_question'];
                            echo '<div class="pull-right">';

                            if ($data['job_faq_order'] == 1) {
                                echo '<a href="'.$link.'&section=form&action=faq&move=md&job_id='.$_GET['job_id'].'&edit_faq='.$data['job_faq_id'].'"><i class="fa fa-lg fa-angle-down"></i></a>';
                            } else if ($data['job_faq_order'] == dbrows($result)) {
                                echo '<a href="'.$link.'&section=form&action=faq&move=mup&job_id='.$_GET['job_id'].'&edit_faq='.$data['job_faq_id'].'"><i class="fa fa-lg fa-angle-up"></i></a>';
                            } else {
                                echo '<a href="'.$link.'&section=form&action=faq&move=mup&job_id='.$_GET['job_id'].'&edit_faq='.$data['job_faq_id'].'"><i class="fa fa-lg fa-angle-up m-r-10"></i></a>';
                                echo '<a href="'.$link.'&section=form&action=faq&move=md&job_id='.$_GET['job_id'].'&edit_faq='.$data['job_faq_id'].'"><i class="fa fa-lg fa-angle-down"></i></a>';
                            }

                            echo ' | <a href="'.$link.'&section=form&action=faq&job_id='.$_GET['job_id'].'&edit_faq='.$data['job_faq_id'].'">'.$locale['edit'].'</a>';
                            echo ' | <a class="text-danger" href="'.$link.'&section=form&action=faq&job_id='.$_GET['job_id'].'&delete_faq='.$data['job_faq_id'].'">'.$locale['delete'].'</a>';
                            echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="list-group-item">'.$locale['jb_122'].'</div>';
                }

            echo '</div>';
        echo '</div>';

    echo '</div>';
} else {
    if (isset($_GET['job_id'])) {
        echo '<div class="m-b-20 pull-right"><a class="btn btn-info" href="'.$link.'&section=form&action=faq&job_id='.$_GET['job_id'].'">'.$locale['jb_103'].'</a></div>';
    }

    echo openform('inputform', 'post');
    echo form_hidden('job_id', '', $data['job_id']);

    echo '<div class="row">';

        echo '<div class="col-xs-12 col-sm-6">';
            echo form_text('job_title', $locale['jb_123'], $data['job_title'], [
                'required'   => TRUE,
                'error_text' => $locale['jb_124']
            ]);

            echo form_textarea('job_description', $locale['jb_125'], $data['job_description'], [
                'required'   => TRUE,
                'error_text' => $locale['jb_126']
            ]);
        echo '</div>';

        echo '<div class="col-xs-12 col-sm-6">';
            function job_categories() {
                $cats = [];
                $result = dbquery("SELECT * FROM ".DB_JOB_CATS." WHERE ".(multilang_table('JB') ? in_group('job_cat_language', LANGUAGE) : '')."");

                if (dbrows($result) > 0) {
                    while ($data = dbarray($result)) {
                        $cats[$data['job_cat_id']] = $data['job_cat_name'];
                    }
                }

                return $cats;
            }

            echo form_select('job_cat', $locale['jb_127'], $data['job_cat'], [
                'options'    => job_categories(),
                'required'   => TRUE,
                'error_text' => $locale['jb_128']
            ]);

            $opts = [];
            $result_l = dbquery("SELECT * FROM ".DB_JOB_LOCATIONS);
            if (dbrows($result_l) > 0) {
                while ($l_data = dbarray($result_l)) {
                    $opts[$l_data['job_location_id']] = $l_data['job_location_name'];
                }
            }

            echo form_select('job_location', $locale['jb_130'], $data['job_location'], [
                'options' => $opts
            ]);
        echo '</div>';

    echo '</div>';

    echo form_button('save_job', $locale['save'], $locale['save'], ['class' => 'btn-success', 'icon' => 'fa fa-hdd-o']);

    echo closeform();
}
