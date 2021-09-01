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

pageAccess('JB');

class JobsAdmin {
    private $locale;

    public function __construct() {
        $this->locale = fusion_get_locale('', JB_LOCALE);
    }

    private function listing() {
        $limit = 15;
        $total_rows = dbcount("(job_id)", DB_JOBS);
        $rowstart = isset($_GET['rowstart']) && ($_GET['rowstart'] <= $total_rows) ? $_GET['rowstart'] : 0;

        $result = dbquery("SELECT j.*, c.*, l.*, (SELECT COUNT(f.job_faq_id) FROM ".DB_JOB_FAQ." AS f WHERE j.job_id = f.job_faq_job) AS job_faq_count
            FROM ".DB_JOBS." AS j
            LEFT JOIN ".DB_JOB_CATS." AS c ON c.job_cat_id = j.job_cat
            LEFT JOIN ".DB_JOB_LOCATIONS." AS l ON l.job_location_id = j.job_location
            WHERE ".(multilang_table('JB') ? in_group('c.job_cat_language', LANGUAGE) : '')."
            LIMIT $rowstart, $limit
        ");

        $rows = dbrows($result);

        if ($rows > 0) {
            echo '<div class="table-responsive"><table class="table table-bordered table-striped">';
            echo '<thead><tr>';
                echo '<th>'.$this->locale['jb_100'].'</th>';
                echo '<th>'.$this->locale['jb_101'].'</th>';
                echo '<th>'.$this->locale['jb_102'].'</th>';
                echo '<th>'.$this->locale['jb_103'].'</th>';
                echo '<th>'.$this->locale['jb_104'].'</th>';
            echo '</tr></thead>';
            echo '<tbody>';

            while ($data = dbarray($result)) {
                echo '<tr>';
                    echo '<td>'.$data['job_title'].'</td>';
                    echo '<td>'.$data['job_cat_name'].'</td>';
                    echo '<td>'.$data['job_location_name'].'</td>';

                    echo '<td>'.format_word($data['job_faq_count'], $this->locale['fmt_item']).' - <a href="'.JOBS.'admin.php'.fusion_get_aidlink().'&section=form&action=faq&job_id='.$data['job_id'].'">'.$this->locale['add'].'/'.$this->locale['edit'].'</a></td>';

                    echo '<td>';
                        echo '<a href="'.JOBS.'admin.php'.fusion_get_aidlink().'&section=form&action=edit&job_id='.$data['job_id'].'">'.$this->locale['edit'].'</a>';
                        echo ' | <a class="text-danger" href="'.JOBS.'admin.php'.fusion_get_aidlink().'&section=form&action=delete&job_id='.$data['job_id'].'">'.$this->locale['delete'].'</a>';
                    echo '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table></div>';

            if ($total_rows > $rows) {
                echo makepagenav($rowstart, $limit, $total_rows, $limit, clean_request('', ['aid', 'section']).'&');
            }
        } else {
            echo '<div class="well text-center">'.$this->locale['jb_105'].'</div>';
        }
    }

    public function displayAdmin() {
        add_to_title($this->locale['jb_title']);

        add_breadcrumb(['link' => INFUSIONS.'jobs/admin.php'.fusion_get_aidlink(), 'title' => $this->locale['jb_title']]);

        if (!empty($_GET['section'])) {
            switch ($_GET['section']) {
                case 'form':
                    add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $this->locale['jb_106']]);
                    break;
                case 'categories':
                    add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $this->locale['jb_107']]);
                    break;
                case 'locations':
                    add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $this->locale['jb_108']]);
                    break;
                case 'applicants':
                    add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $this->locale['jb_109']]);
                    break;
                case 'settings':
                    add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $this->locale['jb_110']]);
                    break;
            }
        }

        opentable($this->locale['jb_title']);

        $edit = (isset($_GET['action']) && $_GET['action'] == 'edit') && isset($_GET['job_id']);
        $allowed_section = ['list', 'form', 'jobs', 'categories', 'locations', 'applicants', 'settings'];
        $_GET['section'] = isset($_GET['section']) && in_array($_GET['section'], $allowed_section) ? $_GET['section'] : 'list';

        $tab['title'][] = $this->locale['jb_title'];
        $tab['id'][]    = 'list';
        $tab['icon'][]  = 'fa fa-fw fa-briefcase';

        $tab['title'][] = $this->locale['jb_106'];
        $tab['id'][]    = 'form';
        $tab['icon'][]  = 'fa fa-'.($edit ? 'pencil' : 'plus');

        $tab['title'][] = $this->locale['jb_107'];
        $tab['id'][]    = 'categories';
        $tab['icon'][]  = 'fa fa-folder';

        $tab['title'][] = $this->locale['jb_108'];
        $tab['id'][]    = 'locations';
        $tab['icon'][]  = 'fa fa-map-marker-alt';

        $tab['title'][] = $this->locale['jb_109'].' <span class="badge">'.dbcount('(job_applicant_id)', DB_JOB_APPLICANTS).'</span>';
        $tab['id'][]    = 'applicants';
        $tab['icon'][]  = 'fa fa-user-plus';

        $tab['title'][] = $this->locale['jb_110'];
        $tab['id'][]    = 'settings';
        $tab['icon'][]  = 'fa fa-cog';

        echo opentab($tab, $_GET['section'], 'jobsadmin', TRUE, 'nav-tabs');
        switch ($_GET['section']) {
            case 'form':
                if (dbcount('(job_cat_id)', DB_JOB_CATS)) {
                    require_once 'admin/jobs.php';
                } else {
                    echo '<div class="well text-center">'.$this->locale['jb_111'].'</div>';
                }
                break;
            case 'categories':
                require_once 'admin/categories.php';
                break;
            case 'locations':
                require_once 'admin/locations.php';
                break;
            case 'applicants':
                require_once 'admin/applicants.php';
                break;
            case 'settings':
                require_once 'admin/settings.php';
                break;
            default:
                $this->listing();
                break;
        }
        echo closetab();

        closetable();
    }
}

$jb = new JobsAdmin();
$jb->displayAdmin();

require_once THEMES.'templates/footer.php';
