<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: applicants.php
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
$jobs_settings = get_settings('jobs');

$link = JOBS.'admin.php'.fusion_get_aidlink().'&section=applicants';

if ((isset($_GET['action']) && $_GET['action'] == 'delete') && (isset($_GET['applicant_id']) && isnum($_GET['applicant_id']))) {
    $result = dbquery("SELECT * FROM ".DB_JOB_APPLICANTS." WHERE job_applicant_id='".$_GET['applicant_id']."'");

    if (dbrows($result) > 0) {
        $data = dbarray($result);

        if (!empty($data['job_applicant_cv']) && file_exists(JOBS.'cv_files/'.$data['job_applicant_cv'])) {
            unlink(JOBS.'cv_files/'.$data['job_applicant_cv']);
        }
    }

    dbquery("DELETE FROM ".DB_JOB_APPLICANTS." WHERE job_applicant_id='".intval($_GET['applicant_id'])."'");

    addnotice('success', $locale['jb_143']);
    redirect(clean_request('', ['action', 'applicant_id'], FALSE));
}

if ((isset($_GET['action']) && $_GET['action'] == 'view') && (isset($_GET['applicant_id']) && isnum($_GET['applicant_id']))) {
    $result = dbquery("SELECT a.*, j.*, c.*
        FROM ".DB_JOB_APPLICANTS." AS a
        LEFT JOIN ".DB_JOBS." AS j ON j.job_id = a.job_applicant_job
        LEFT JOIN ".DB_JOB_CATS." AS c ON c.job_cat_id = j.job_cat
        WHERE ".(multilang_table('JB') ? in_group('c.job_cat_language', LANGUAGE)." AND " : '')."
        a.job_applicant_id = :applicant_id
    ", [':applicant_id' => (int) $_GET['applicant_id']]);

    if (dbrows($result) > 0) {
        $data = dbarray($result);

        echo '<div class="clearfix m-b-20"><a class="btn btn-default pull-right" href="'.$link.'">'.$locale['back'].'</a></div>';

        echo '<div class="row">';

            echo '<div class="col-xs-12 col-sm-6">';
                openside('');
                if (!empty($data['job_applicant_cv']) && file_exists(JOBS.'cv_files/'.$data['job_applicant_cv'])) {
                    echo '<a class="btn btn-primary pull-right" href="'.JOBS.'cv_files/'.$data['job_applicant_cv'].'">'.$locale['jb_156'].'</a>';
                }
                echo '<h3 class="m-t-0">'.$data['job_applicant_firstname'].' '.$data['job_applicant_lastname'].'</h3>';
                echo '<hr>';

                echo '<div class="m-b-10"><b style="width: 30%;display: inline-block;">'.$locale['jb_144'].'</b>'.$data['job_title'].'</div>';
                echo '<div class="m-b-10"><b style="width: 30%;display: inline-block;">'.$locale['jb_145'].'</b><a href="mailto:'.$data['job_applicant_email'].'">'.$data['job_applicant_email'].'</a></div>';
                echo '<div class="m-b-10"><b style="width: 30%;display: inline-block;">'.$locale['jb_146'].'</b><a href="tel:'.$data['job_applicant_phone'].'">'.$data['job_applicant_phone'].'</a></div>';

                echo '<div class="m-b-10"><b style="width: 30%;display: inline-block;">'.$locale['jb_147'].'</b>';
                    switch ($data['job_applicant_hearaboutus']) {
                        case 'friend':
                            echo $locale['jb_180'];
                            break;
                        case 'internet':
                            echo $locale['jb_181'];
                            break;
                        case 'ad':
                            echo $locale['jb_182'];
                            break;
                        default:
                            echo $locale['jb_189'];
                    }
                echo '</div>';

                if ($jobs_settings['internship']) {
                    echo '<div class="m-b-10"><b style="width: 30%;display: inline-block;">'.$locale['jb_148'].'</b>'.($data['job_applicant_internship'] ? $locale['yes'] : $locale['no']).'</div>';
                }

                echo '<b>'.$locale['jb_149'].'</b><p>'.htmlentities($data['job_applicant_message']).'</p>';
                closeside();
            echo '</div>';

            if (isset($_POST['sendemail'])) {
                require_once INCLUDES.'sendmail_include.php';

                $input = [
                    'subject' => form_sanitizer($_POST['subject'], '', 'subject'),
                    'message' => form_sanitizer($_POST['message'], '', 'message')
                ];

                $email = TRUE;
                if (!sendemail($data['job_applicant_firstname'].' '.$data['job_applicant_lastname'], $data['job_applicant_email'],
                    fusion_get_userdata('user_name'), $jobs_settings['email'], $input['subject'], $input['message'])) {
                    \defender::stop();
                    addnotice('danger', $locale['jb_150']);
                    $email = FALSE;
                }

                if ($email == TRUE) {
                    $data = [
                        'job_applicant_id'     => $_GET['applicant_id'],
                        'job_applicant_status' => 1
                    ];

                    dbquery_insert(DB_JOB_APPLICANTS, $data, 'update');
                }

                if (\defender::safe()) {
                    addnotice('success', $locale['jb_151']);
                    redirect($link);
                }
            }

            echo '<div class="col-xs-12 col-sm-6">';
            openside($locale['jb_145']);
            echo openform('emailform', 'post', FUSION_REQUEST);
                echo form_text('subject', $locale['jb_152'], !empty($_POST['subject']) ? $_POST['subject'] : '');
                echo form_textarea('message', $locale['jb_149'], !empty($_POST['message']) ? $_POST['message'] : '');
                echo form_button('sendemail', $locale['jb_153'], 'sendemail');
            echo closeform();
            closeside();
            echo '</div>';

        echo '</div>';
    } else {
        redirect($link);
    }
} else {
    $limit = 15;
    $total_rows = dbcount("(job_applicant_id)", DB_JOB_APPLICANTS);
    $rowstart = isset($_GET['rowstart']) && ($_GET['rowstart'] <= $total_rows) ? $_GET['rowstart'] : 0;

    $result = dbquery("SELECT a.*, j.*, c.*
        FROM ".DB_JOB_APPLICANTS." AS a
        LEFT JOIN ".DB_JOBS." AS j ON j.job_id = a.job_applicant_job
        LEFT JOIN ".DB_JOB_CATS." AS c ON c.job_cat_id = j.job_cat
        WHERE ".(multilang_table('JB') ? in_group('job_cat_language', LANGUAGE) : '')."
        LIMIT $rowstart, $limit
    ");

    $rows = dbrows($result);

    if ($rows > 0) {
        echo '<div class="table-responsive"><table class="table table-bordered table-striped">';
        echo '<thead><tr>';
            echo '<th>'.$locale['jb_154'].'</th>';
            echo '<th>'.$locale['jb_155'].'</th>';
            echo '<th>'.$locale['jb_156'].'</th>';
            echo '<th>'.$locale['jb_157'].'</th>';
            echo '<th>'.$locale['jb_158'].'</th>';
            echo '<th>'.$locale['jb_159'].'</th>';
        echo '</tr></thead>';
        echo '<tbody>';

        while ($data = dbarray($result)) {
            echo '<tr>';
                echo '<td>'.timer($data['job_applicant_datestamp']).'</td>';
                echo '<td>'.$data['job_applicant_firstname'].' '.$data['job_applicant_lastname'].'</td>';
                if (!empty($data['job_applicant_cv']) && file_exists(JOBS.'cv_files/'.$data['job_applicant_cv'])) {
                    echo '<td><a href="'.JOBS.'cv_files/'.$data['job_applicant_cv'].'">'.$locale['jb_160'].'</a></td>';
                } else {
                    echo '<td>'.$locale['jb_189'].'</td>';
                }
                echo '<td>'.$data['job_title'].'</td>';
                echo '<td>';
                    switch ($data['job_applicant_status']) {
                        case 0:

                            echo '<span class="label label-warning">'.$locale['jb_161'].'</span>';
                            break;
                        case 1:
                            echo '<span class="label label-info">'.$locale['jb_162'].'</span>';
                            break;
                    }
                echo '</td>';
                echo '<td>';
                    echo '<a href="'.$link.'&action=view&applicant_id='.$data['job_applicant_id'].'">'.$locale['view'].'</a>';
                    echo ' | <a class="text-danger" href="'.$link.'&action=delete&applicant_id='.$data['job_applicant_id'].'">'.$locale['delete'].'</a>';
                echo '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table></div>';

        if ($total_rows > $rows) {
            echo makepagenav($rowstart, $limit, $total_rows, $limit, clean_request('', ['aid', 'section']).'&');
        }
    } else {
        echo '<div class="well text-center">'.$locale['jb_163'].'</div>';
    }
}
