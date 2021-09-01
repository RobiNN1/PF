<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: jobs_rewrite_include.php
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

$regex = [
    '%job_id%'    => '([0-9]+)',
    '%job_title%' => '([0-9a-zA-Z._\W]+)',
];

$pattern = [
    'jobs/apply/%job_id%'           => 'infusions/jobs/jobs.php?apply=%job_id%',
    'jobs/job-%job_id%/%job_title%' => 'infusions/jobs/jobs.php?job_id=%job_id%',
    'jobs'                          => 'infusions/jobs/jobs.php'
];

$pattern_tables['%job_id%'] = [
    'table'       => DB_JOBS,
    'primary_key' => 'job_id',
    'id'          => ['%job_id%' => 'job_id'],
    'columns'     => [
        '%job_title%' => 'job_title'
    ]
];
