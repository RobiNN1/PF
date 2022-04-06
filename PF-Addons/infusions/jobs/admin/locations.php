<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: locations.php
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

$link = JOBS.'admin.php'.fusion_get_aidlink().'&section=locations';

$data = [
    'job_location_id'   => 0,
    'job_location_name' => ''
];

if ((isset($_GET['action']) && $_GET['action'] == 'delete') && (isset($_GET['location_id']) && isnum($_GET['location_id']))) {
    if (dbcount("(job_location)", DB_JOBS, "job_location='".intval($_GET['location_id'])."'")) {
        addnotice('danger', $locale['jb_137']);
    } else {
        dbquery("DELETE FROM ".DB_JOB_LOCATIONS." WHERE job_location_id='".intval($_GET['location_id'])."'");
        addnotice('success', $locale['jb_138']);
    }
    redirect($link);
}

if (isset($_POST['save_location'])) {
    $data = [
        'job_location_id'   => form_sanitizer($_POST['job_location_id'], '', 'job_location_id'),
        'job_location_name' => form_sanitizer($_POST['job_location_name'], '', 'job_location_name')
    ];

    if (dbcount('(job_location_id)', DB_JOB_LOCATIONS, "job_location_id='".$data['job_location_id']."'")) {
        dbquery_insert(DB_JOB_LOCATIONS, $data, 'update');

        if (\defender::safe()) {
            addnotice('success', $locale['jb_139']);
            redirect($link);
        }
    } else {
        dbquery_insert(DB_JOB_LOCATIONS, $data, 'save');
        if (\defender::safe()) {
            addnotice('success', $locale['jb_140']);
            redirect($link);
        }
    }
}

if ((isset($_GET['action']) && $_GET['action'] == 'edit') && (isset($_GET['location_id']) && isnum($_GET['location_id']))) {
    $result = dbquery("SELECT * FROM ".DB_JOB_LOCATIONS." WHERE job_location_id='".$_GET['location_id']."'");

    if (dbrows($result)) {
        $data = dbarray($result);
    } else {
        redirect(clean_request('', ['section', 'aid']));
    }
}

echo '<div class="row">';
    echo '<div class="col-xs-12 col-sm-6">';
        echo openform('inputform', 'post');
        echo form_hidden('job_location_id', '', $data['job_location_id']);

        echo form_text('job_location_name', $locale['jb_130'], $data['job_location_name'], [
            'required'   => TRUE,
            'error_text' => $locale['jb_141']
        ]);

        echo form_button('save_location', $locale['save'], $locale['save'], ['class' => 'btn-success', 'icon' => 'fa fa-hdd-o']);

        echo closeform();
    echo '</div>';

    echo '<div class="col-xs-12 col-sm-6">';
        echo '<div class="list-group">';

            $result = dbquery("SELECT * FROM ".DB_JOB_LOCATIONS);

            if (dbrows($result) > 0) {
                while ($data = dbarray($result)) {
                    echo '<div class="list-group-item">';
                        echo $data['job_location_name'];
                        echo '<div class="pull-right">';
                            echo '<a href="'.$link.'&action=edit&location_id='.$data['job_location_id'].'">'.$locale['edit'].'</a>';
                            echo ' | <a class="text-danger" href="'.$link.'&action=delete&location_id='.$data['job_location_id'].'">'.$locale['delete'].'</a>';
                        echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="list-group-item">'.$locale['jb_142'].'</div>';
            }

        echo '</div>';
    echo '</div>';

echo '</div>';
