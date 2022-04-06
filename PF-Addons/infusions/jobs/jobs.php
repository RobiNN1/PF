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
require_once __DIR__.'/../../maincore.php';

if (!defined('JOBS_EXISTS')) {
    redirect(BASEDIR.'error.php?code=404');
}

require_once THEMES.'templates/header.php';
require_once INCLUDES.'infusions_include.php';
require_once JOBS.'templates/jobs.tpl.php';

$locale = fusion_get_locale('', JB_LOCALE);
$settings = fusion_get_settings();
$jobs_settings = get_settings('jobs');

set_title($locale['jb_title']);

$main_title = !empty(\PHPFusion\SiteLinks::get_current_SiteLinks('infusions/jobs/jobs.php', 'link_name')) ? \PHPFusion\SiteLinks::get_current_SiteLinks('infusions/jobs/jobs.php', 'link_name') : $locale['jb_title'];

add_breadcrumb(['link' => INFUSIONS.'jobs/jobs.php', 'title' => $main_title]);

$info = [];

if (isset($_GET['job_id'])) {
    if (validate_job($_GET['job_id'])) {
        $result = dbquery("SELECT j.*, c.*, o.*
            FROM ".DB_JOBS." AS j
            LEFT JOIN ".DB_JOB_CATS." AS c ON c.job_cat_id = j.job_cat
            LEFT JOIN ".DB_JOB_LOCATIONS." AS o ON o.job_location_id = j.job_location
            WHERE ".(multilang_table('JB') ? in_group('c.job_cat_language', LANGUAGE) : '')."
            AND job_id=:job_id
        ", [':job_id' => (int)$_GET['job_id']]);

        if (dbrows($result) > 0) {

            $info['job'] = dbarray($result);

            $result_f = dbquery("SELECT *
            FROM ".DB_JOB_FAQ."
            WHERE job_faq_job = :job_id
            ORDER BY job_faq_order
        ", [':job_id' => $_GET['job_id']]);

            if (dbrows($result_f) > 0) {
                while ($data_c = dbarray($result_f)) {
                    $info['faq'][] = $data_c;
                }
            }

            add_breadcrumb(['link' => INFUSIONS.'jobs/jobs.php?job_id='.$_GET['job_id'], 'title' => $info['job']['job_title']]);

            set_title($main_title.$locale['global_201']);
            add_to_title($info['job']['job_title']);
        } else {
            redirect(JOBS.'jobs.php');
        }
    } else {
        redirect(JOBS.'jobs.php');
    }
} else if (isset($_GET['apply'])) {
    if (validate_job($_GET['apply'])) {
        $data = [
            'job_applicant_datestamp'   => time(),
            'job_applicant_firstname'   => '',
            'job_applicant_lastname'    => '',
            'job_applicant_email'       => '',
            'job_applicant_phone'       => '',
            'job_applicant_cv'          => '',
            'job_applicant_message'     => '',
            'job_applicant_hearaboutus' => '',
            'job_applicant_internship'  => 0,
            'job_applicant_job'         => (int)$_GET['apply'],
        ];

        if (isset($_POST['submit_request'])) {
            $data = [
                'job_applicant_datestamp'   => time(),
                'job_applicant_firstname'   => form_sanitizer($_POST['firstname'], '', 'firstname'),
                'job_applicant_lastname'    => form_sanitizer($_POST['lastname'], '', 'lastname'),
                'job_applicant_email'       => form_sanitizer($_POST['email'], '', 'email'),
                'job_applicant_phone'       => form_sanitizer(str_replace(' ', '', $_POST['phone']), '', 'phone'),
                'job_applicant_cv'          => '',
                'job_applicant_message'     => form_sanitizer($_POST['message'], '', 'message'),
                'job_applicant_hearaboutus' => form_sanitizer($_POST['hearaboutus'], '', 'hearaboutus'),
                'job_applicant_internship'  => isset($_POST['internship']) ? 1 : 0,
                'job_applicant_job'         => $_GET['apply']
            ];

            if ($jobs_settings['captcha'] == 1) {
                $_CAPTCHA_IS_VALID = FALSE;
                include_once INCLUDES.'captchas/'.$settings['captcha'].'/captcha_check.php';

                if (!$_CAPTCHA_IS_VALID) {
                    \defender::stop();
                    addnotice('warning', $locale['jb_169']);
                }
            }

            if (\defender::safe() && !empty($_FILES['cv']['name']) && is_uploaded_file($_FILES['cv']['tmp_name'])) {
                $upload = form_sanitizer($_FILES['cv'], '', 'cv');

                if (empty($upload['error']) && !empty($_FILES['cv']['size'])) {
                    if (!empty($upload['image_name'])) {
                        $data['job_applicant_cv'] = $upload['image_name'];
                    } else if (!empty($upload['target_file'])) {
                        $data['job_applicant_cv'] = $upload['target_file'];
                    } else {
                        \defender::stop();
                        addnotice('warning', $locale['jb_170']);
                    }
                }

                unset($upload);
            } else if ($jobs_settings['required_cv'] == 1) {
                \Defender::stop();
                addnotice('danger', $locale['jb_196']);
            }

            dbquery_insert(DB_JOB_APPLICANTS, $data, 'save');

            if (\defender::safe()) {
                addnotice('success', $locale['jb_171']);
                redirect(FUSION_REQUEST);
            }
        }

        $info['job'] = dbarray(dbquery("SELECT * FROM ".DB_JOBS." WHERE job_id=:job_id", [':job_id' => $_GET['apply']]));

        add_breadcrumb(['link' => INFUSIONS.'jobs/jobs.php?job_id='.$_GET['apply'], 'title' => $info['job']['job_title']]);
        add_breadcrumb(['link' => INFUSIONS.'jobs/jobs.php?apply='.$_GET['apply'], 'title' => $locale['jb_172']]);

        set_title($main_title.$locale['global_201']);
        add_to_title($info['job']['job_title']);

        $info['form'] = [
            'openform'        => openform('inputform', 'post', FUSION_REQUEST, ['enctype' => TRUE]),
            'closeform'       => closeform(),
            'firstname'       => form_text('firstname', $locale['jb_173'], $data['job_applicant_firstname'], [
                'placeholder' => $locale['jb_174'],
                'required'    => TRUE
            ]),
            'lastname'        => form_text('lastname', $locale['jb_175'], $data['job_applicant_lastname'], [
                'placeholder' => $locale['jb_176'],
                'required'    => TRUE
            ]),
            'email'           => form_text('email', $locale['jb_145'], $data['job_applicant_email'], [
                'placeholder' => 'name@example.com',
                'type'        => 'email',
                'required'    => TRUE
            ]),
            'phone'           => form_text('phone', $locale['jb_146'], $data['job_applicant_phone'], [
                'type'     => 'text',
                'required' => TRUE
            ]),
            'cv'              => form_fileinput('cv', $locale['jb_177'], $data['job_applicant_cv'], [
                'inline'          => FALSE,
                'krajee_disabled' => TRUE,
                'type'            => 'object',
                'valid_ext'       => $jobs_settings['cv_types'],
                'max_byte'        => $jobs_settings['cv_max_b'],
                'upload_path'     => JOBS.'cv_files/',
                'required'        => $jobs_settings['required_cv']
            ]),
            'message'         => form_textarea('message', $locale['jb_149'], $data['job_applicant_message'], [
                'placeholder' => $locale['jb_178'],
                'height'      => '272px',
                'required'    => TRUE
            ]),
            'hearaboutus'     => form_select('hearaboutus', $locale['jb_179'], $data['job_applicant_hearaboutus'], [
                'options'          => [
                    0          => $locale['jb_129'],
                    'friend'   => $locale['jb_180'],
                    'internet' => $locale['jb_181'],
                    'ad'       => $locale['jb_182'],
                ],
                'select2_disabled' => TRUE,
                'inner_width'      => '100%'
            ]),
            'show_internship' => $jobs_settings['internship'],
            'internship'      => form_checkbox('internship', $locale['jb_184'], $data['job_applicant_internship'], [
                'class' => 'display-inline-block m-t-20 m-b-5'
            ]),
            'submit'          => form_button('submit_request', $locale['jb_185'], 'submit', [
                'class' => 'btn-info'
            ]),
            'captcha_code'    => ''
        ];

        if ($jobs_settings['captcha'] == 1) {
            include INCLUDES.'captchas/'.$settings['captcha'].'/captcha_display.php';
            $captcha_settings = [
                'captcha_id' => 'captcha_jobs',
                'input_id'   => 'captcha_code_jobs',
                'image_id'   => 'captcha_image_jobs'
            ];

            $info['form']['captcha'] = display_captcha($captcha_settings);
            if (!isset($_CAPTCHA_HIDE_INPUT) || !$_CAPTCHA_HIDE_INPUT) {
                $info['form']['captcha_code'] = form_text('captcha_code', $locale['jb_186'], '', [
                    'required'         => TRUE,
                    'autocomplete_off' => TRUE,
                    'input_id'         => 'captcha_code_jobs'
                ]);
            }
        }
    } else {
        redirect(JOBS.'jobs.php');
    }
} else {
    $result_c = dbquery("SELECT *
        FROM ".DB_JOB_CATS."
        ".(multilang_table('JB') ? "WHERE ".in_group('job_cat_language', LANGUAGE) : '')."
    ");

    if (dbrows($result_c) > 0) {
        while ($data_c = dbarray($result_c)) {
            $info['categories'][] = $data_c;
        }
    }

    $result_j = dbquery("SELECT j.*, l.*
        FROM ".DB_JOBS." AS j
        LEFT JOIN ".DB_JOB_LOCATIONS." AS l ON l.job_location_id = j.job_location
    ");

    if (dbrows($result_j) > 0) {
        while ($data_j = dbarray($result_j)) {
            $info['jobs'][] = $data_j;
        }
    }
}

render_jobs($info);

function validate_job($id) {
    if (isnum($id)) {
        return dbcount("('job_id')", DB_JOBS, "job_id='".intval($id)."'");
    }

    return NULL;
}

require_once THEMES.'templates/footer.php';
