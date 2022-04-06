<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: jobs.tpl.php
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

if (!function_exists('render_jobs')) {
    function render_jobs($info) {
        opentable(fusion_get_locale('jb_title'));

        echo render_breadcrumbs();

        if (isset($_GET['job_id'])) {
            display_job($info);
        } else if (isset($_GET['apply'])) {
            display_form($info);
        } else {
            display_jobs_index($info);
        }

        closetable();
    }
}

if (!function_exists('display_jobs_index')) {
    function display_jobs_index($info) {
        $locale = fusion_get_locale();

        if (!empty($info['categories'])) {
            echo '<div class="panel-group" id="jobs_categories">';

            add_to_jquery('
                $("#jobs_categories .collapse").on("show.bs.collapse", function() {
                    $(this).parent().find(".icon").addClass("rotate");
                }).on("hide.bs.collapse", function() {
                    $(this).parent().find(".icon").removeClass("rotate");
                });
            ');

            add_to_css('.rotate{-webkit-transform:rotate(180deg);transform:rotate(180deg);}');

            foreach ($info['categories'] as $key => $data) {
                $jobs_count = dbcount('(job_id)', DB_JOBS, 'job_cat='.$data['job_cat_id']);

                echo '<div class="panel panel-default" id="jobs_categories">';
                    echo '<div class="panel-heading">';
                        echo '<div class="row">';
                            echo '<div class="col-xs-6"><a role="button" data-toggle="collapse" data-parent="#jobs_categories" href="#jobs_c'.$data['job_cat_id'].'" aria-expanded="false" aria-controls="jobs_c'.$data['job_cat_id'].'">';
                                echo $data['job_cat_name'];
                            echo '</a></div>';

                            echo '<div class="col-xs-6">';
                                echo format_word($jobs_count, $locale['jb_187']);
                                echo '<i class="icon fa fa-chevron-down pull-right'.($key == 0 ? ' rotate' : '').'"></i>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';

                    echo '<div id="jobs_c'.$data['job_cat_id'].'" class="panel-collapse collapse'.($key == 0 ? ' in' : '').'">';
                            echo '<div class="list-group">';

                            if (!empty($info['jobs'])) {
                                foreach ($info['jobs'] as $job) {
                                    if ($job['job_cat'] == $data['job_cat_id']) {
                                        echo '<div class="list-group-item">';
                                            echo '<div class="row">';
                                                echo '<div class="col-xs-12 col-sm-6">'.$job['job_title'].'</div>';
                                                echo '<div class="col-xs-12 col-sm-6">';
                                                    echo '<a class="pull-right" href="'.JOBS.'jobs.php?job_id='.$job['job_id'].'">'.$locale['jb_188'].'</a>';
                                                    echo !empty($job['job_location_name']) ? $job['job_location_name'] : $locale['jb_189'];
                                                echo '</div>';
                                            echo '</div>';
                                        echo '</div>';

                                    }
                                }
                            }

                            if (dbcount('(job_id)', DB_JOBS, 'job_cat='.$data['job_cat_id']) == 0) {
                                echo '<div class="panel-body"><div class="text-center">'.$locale['jb_190'].'</div></div>';
                            }

                            echo '</div>';
                    echo '</div>';
                echo '</div>';
            }

            echo '</div>';
        } else {
            echo '<div class="well text-center">'.$locale['jb_191'].'</div>';
        }
    }
}

if (!function_exists('display_job')) {
    function display_job($info) {
        $locale = fusion_get_locale();

        $data = $info['job'];

        echo '<div class="text-center">';
            echo '<h1>'.$data['job_title'].'</h1>';
            echo !empty($data['job_location_name']) ? '<h2><i class="fa fa-map-marker-alt"></i> '.$data['job_location_name'].'</h2>' : '';

            echo '<hr/>';

            echo '<h3>'.$locale['jb_192'].'</h3>';

            echo '<p>'.$data['job_description'].'</p>';
        echo '</div>';

        if (!empty($info['faq'])) {
            echo '<div class="panel-group m-t-30" id="job_faq">';

            add_to_jquery('
                $("#job_faq .collapse").on("show.bs.collapse", function() {
                    $(this).parent().find(".icon").addClass("rotate");
                }).on("hide.bs.collapse", function() {
                    $(this).parent().find(".icon").removeClass("rotate");
                });
            ');

            add_to_css('.rotate{-webkit-transform:rotate(180deg);transform:rotate(180deg);}');

            foreach ($info['faq'] as $faq) {
                echo '<div class="panel panel-default">';
                    echo '<div class="panel-heading">';
                        echo '<i class="icon fa fa-chevron-down m-r-20"></i>';
                        echo '<a class="panel-title pointer" role="button" data-toggle="collapse" data-parent="#job_faq" data-target="#job_f'.$faq['job_faq_id'].'" aria-expanded="false" aria-controls="job_f'.$faq['job_faq_id'].'">'.$faq['job_faq_question'].'</a>';
                    echo '</div>';

                    echo '<div id="job_f'.$faq['job_faq_id'].'" class="panel-collapse collapse">';
                        echo '<div class="panel-body">'.parse_textarea($faq['job_faq_answer'], FALSE, FALSE, TRUE, IMAGES, TRUE).'</div>';
                    echo '</div>';
                echo '</div>';
            }

            echo '</div>';
        }

        echo '<div class="text-center"><a class="btn btn-default m-t-20 m-b-50" href="'.JOBS.'jobs.php?apply='.$data['job_id'].'">'.$locale['jb_193'].'</a></div>';
        echo '<div class="text-center"><a href="'.JOBS.'jobs.php">'.$locale['jb_194'].'</a></div>';
    }
}

if (!function_exists('display_form')) {
    function display_form($info) {
        $locale = fusion_get_locale();
        $form = $info['form'];

        echo '<h1 class="text-center">'.$locale['jb_195'].'</h1>';
        echo '<h3 class="text-center">'.$info['job']['job_title'].'</h3>';

        echo $form['openform'];

        echo '<div class="row">';
            echo '<div class="col-xs-12 col-sm-6">';
                echo $form['firstname'];
                echo $form['lastname'];
                echo $form['email'];
                echo $form['phone'];
                echo $form['cv'];
            echo '</div>';

            echo '<div class="col-xs-12 col-sm-6">';
                echo $form['message'];
                echo $form['hearaboutus'];
            echo '</div>';
        echo '</div>';

        add_to_css('.display-inline-block {display: inline-block !important;}');

        if ($form['show_internship']) {
            echo '<div class="text-center">'.$form['internship'].'</div>';
        }

        if (!empty($form['captcha'])) {
            echo '<div class="row">';
                echo '<div class="col-xs-12 col-sm-4">'.$form['captcha'].'</div>';
                echo '<div class="col-xs-12 col-sm-4">'.$form['captcha_code'].'</div>';
            echo '</div>';
        }

        echo '<div class="text-center">'.$form['submit'].'</div>';

        echo $form['closeform'];
    }
}
