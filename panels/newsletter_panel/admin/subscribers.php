<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: subscribers.php
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
$link = NEWSLETTER.'admin.php'.fusion_get_aidlink().'&section=subscribers';
$action = get('action');

$data = [
    'sub_id'        => 0,
    'sub_email'     => '',
    'sub_token'     => random_token(),
    'sub_active'    => 1,
    'sub_datestamp' => time()
];

if ((isset($_GET['action']) && $_GET['action'] == 'delete') && (isset($_GET['sub_id']) && isnum($_GET['sub_id']))) {
    dbquery("DELETE FROM ".DB_NEWSLETTER_SUBS." WHERE sub_id='".$_GET['sub_id']."'");

    addNotice('success', $locale['nsl_notice_08']);
    redirect($link);
}

if (isset($_POST['save_sub'])) {
    $data = [
        'sub_id'        => form_sanitizer($_POST['sub_id'], '0', 'sub_id'),
        'sub_email'     => form_sanitizer($_POST['sub_email'], '', 'sub_email'),
        'sub_token'     => form_sanitizer($_POST['sub_token'], '', 'sub_token'),
        'sub_active'    => form_sanitizer($_POST['sub_active'], '', 'sub_active'),
        'sub_datestamp' => form_sanitizer($_POST['sub_datestamp'], '', 'sub_datestamp')
    ];

    if (dbcount("(sub_id)", DB_NEWSLETTER_SUBS, "sub_id='".$data['sub_id']."'")) {
        dbquery_insert(DB_NEWSLETTER_SUBS, $data, 'update');
        if (\defender::safe()) {
            addNotice('success', $locale['nsl_notice_09']);
            redirect($link);
        }
    } else {
        dbquery_insert(DB_NEWSLETTER_SUBS, $data, 'save');
        if (\defender::safe()) {
            addNotice('success', $locale['nsl_notice_10']);
            redirect($link);
        }
    }
}

if ((isset($_GET['action']) && $_GET['action'] == 'edit') && (isset($_GET['sub_id']) && isnum($_GET['sub_id']))) {
    $result = dbquery("SELECT * FROM ".DB_NEWSLETTER_SUBS." WHERE sub_id='".intval($_GET['sub_id'])."'");
    if (dbrows($result)) {
        $data = dbarray($result);
    } else {
        redirect($link);
    }
}

if (get('ref') == 'form') {
    echo openform('subform', 'post', FUSION_REQUEST);
    echo form_hidden('sub_id', '', $data['sub_id']);
    echo form_hidden('sub_active', '', $data['sub_active']);
    echo form_hidden('sub_datestamp', '', $data['sub_datestamp']);
    echo form_text('sub_token', 'Token', $data['sub_token'], ['deactivate' => TRUE]);
    echo form_text('sub_email', $locale['nsl_032'], $data['sub_email'], ['type' => 'email', 'required' => TRUE]);
    echo form_button('save_sub', $locale['save'], 'save_sub', ['class' => 'btn-success']);
    echo closeform();
} else {
    $allowed_section = ['guests', 'members'];
    $_GET['subs'] = isset($_GET['subs']) && in_array($_GET['subs'], $allowed_section) ? $_GET['subs'] : 'guests';

    $tab_settings['title'][] = $locale['nsl_033'];
    $tab_settings['id'][]    = 'guests';
    $tab_settings['title'][] = $locale['nsl_034'];
    $tab_settings['id'][]    = 'members';

    echo opentab($tab_settings, $_GET['subs'], 'subslist', TRUE, 'nav-tabs', 'subs');
    switch ($_GET['subs']) {
        case 'members':
            if (column_exists('users', 'user_newsletter')) {
                subs_members();
            } else {
                echo $locale['nsl_063'];
            }
            break;
        default:
            subs_guests($link);
            break;
    }
    echo closetab();
}

function subs_members() {
    $locale = fusion_get_locale();
    $limit = 15;
    $total_rows = dbcount("(user_id)", DB_USERS, "user_newsletter=1");
    $rowstart = isset($_GET['rowstart']) && ($_GET['rowstart'] <= $total_rows) ? $_GET['rowstart'] : 0;

    $result = dbquery("SELECT user_id, user_name, user_email, user_status, user_newsletter FROM ".DB_USERS." WHERE user_newsletter=1 LIMIT $rowstart, $limit");
    $rows = dbrows($result);

    echo '<div class="table-responsive"><table id="subs-table" class="table table-striped table-bordered">';
        echo '<thead><tr>';
            echo '<th>'.$locale['nsl_035'].'</th>';
            echo '<th>'.$locale['nsl_032'].'</th>';
        echo '</tr></thead>';
        echo '<tbody>';
            if (dbrows($result) > 0) {
                while ($data = dbarray($result)) {
                    echo '<tr>';
                        echo '<td>'.profile_link($data['user_id'], $data['user_name'], $data['user_status']).'</td>';
                        echo '<td>'.$data['user_email'].'</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="2" class="text-center">'.$locale['nsl_036'].'</td></tr>';
            }
        echo '</tbody>';
    echo '</div></table>';

    if ($total_rows > $rows) {
        echo makepagenav($rowstart, $limit, $total_rows, $limit, clean_request('', ['aid', 'section'], TRUE).'&subs=members&');
    }
}

function subs_guests($link) {
    $locale = fusion_get_locale();
    $allowed_actions = array_flip(['activate', 'deactivate', 'delete']);

    if (isset($_POST['table_action']) && isset($allowed_actions[$_POST['table_action']])) {
        $input = (isset($_POST['sub_id'])) ? explode(',', form_sanitizer($_POST['sub_id'], '', 'sub_id')) : '';

        if (!empty($input)) {
            foreach ($input as $sub_id) {
                if (dbcount("('sub_id')", DB_NEWSLETTER_SUBS, "sub_id = :sub_id", [':sub_id' => (int)$sub_id]) && \defender::safe()) {
                    switch ($_POST['table_action']) {
                        case 'activate':
                            dbquery("UPDATE ".DB_NEWSLETTER_SUBS." SET sub_active = 1 WHERE sub_id = :sub_id", [':sub_id' => (int)$sub_id]);
                            addNotice('success', $locale['nsl_notice_11']);
                            break;
                        case 'deactivate':
                            dbquery("UPDATE ".DB_NEWSLETTER_SUBS." SET sub_active = 0 WHERE sub_id = :sub_id", [':sub_id' => (int)$sub_id]);
                            addNotice('success', $locale['nsl_notice_12']);
                            break;
                        case 'delete':
                            dbquery("DELETE FROM ".DB_NEWSLETTER_SUBS." WHERE sub_id = :sub_id", [':sub_id' => (int)$sub_id]);
                            addNotice('success', $locale['nsl_notice_13']);
                            break;
                        default:
                            redirect(FUSION_REQUEST);
                    }
                }
            }
            redirect(FUSION_REQUEST);
        }

        addNotice('warning', $locale['nsl_notice_14']);
        redirect(FUSION_REQUEST);
    }

    echo '<div class="clearfix m-b-20">';
        echo '<div class="pull-right">';
            echo '<a class="btn btn-primary btn-sm m-l-5" href="'.$link.'&ref=form"><i class="fa fa-plus"></i> '.$locale['add'].'</a>';
            echo '<button type="button" class="btn btn-default btn-sm m-l-5" onclick="run_admin(\'activate\', \'#table_action\', \'#subs_table\');">'.$locale['nsl_037'].'</button>';
            echo '<button type="button" class="btn btn-default btn-sm m-l-5" onclick="run_admin(\'deactivate\', \'#table_action\', \'#subs_table\');">'.$locale['nsl_038'].'</button>';
            echo '<button type="button" class="btn btn-danger btn-sm m-l-5" onclick="run_admin(\'delete\', \'#table_action\', \'#subs_table\');">'.$locale['delete'].'</button>';
        echo '</div>';
    echo '</div>';

    $limit = 15;
    $total_rows = dbcount("(sub_id)", DB_NEWSLETTER_SUBS);
    $rowstart = isset($_GET['rowstart']) && ($_GET['rowstart'] <= $total_rows) ? $_GET['rowstart'] : 0;
    $result = dbquery("SELECT * FROM ".DB_NEWSLETTER_SUBS." LIMIT $rowstart, $limit");
    $rows = dbrows($result);

    echo openform('subs_table', 'post', FUSION_REQUEST);
    echo form_hidden('table_action', '', '');

    echo '<div class="table-responsive"><table id="subs-table" class="table table-striped table-bordered">';
        echo '<thead><tr>';
            echo '<th>'.form_checkbox('check_all', '', '', ['class' => 'm-b-0']).'</th>';
            echo '<th>'.$locale['nsl_032'].'</th>';
            echo '<th>'.$locale['nsl_039'].'</th>';
            echo '<th>'.$locale['nsl_040'].'</th>';
            echo '<th>'.$locale['actions'].'</th>';
        echo '</tr></thead>';
        echo '<tbody>';
            if (dbrows($result) > 0) {
                while ($data = dbarray($result)) {
                    echo '<tr>';
                        echo '<td>'.form_checkbox('sub_id[]', '', '', ['value' => $data['sub_id'], 'class' => 'm-0', 'input_id' => 'sub-id-'.$data['sub_id']]).'</td>';
                        echo '<td>'.$data['sub_email'].'</td>';
                        echo '<td>'.($data['sub_active'] ? $locale['yes'] : $locale['no']).'</td>';
                        echo '<td>'.showdate('shortdate', $data['sub_datestamp']).'</td>';
                        echo '<td><a href="'.$link.'&ref=form&action=edit&sub_id='.$data['sub_id'].'">'.$locale['edit'].'</a> | <a href="'.$link.'&action=delete&sub_id='.$data['sub_id'].'" class="text-danger">'.$locale['delete'].'</a></td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="5" class="text-center">'.$locale['nsl_036'].'</td></tr>';
            }
        echo '</tbody>';
    echo '</div></table>';

    closeform();

    if ($total_rows > $rows) {
        echo makepagenav($rowstart, $limit, $total_rows, $limit, clean_request('', ['aid', 'section'], TRUE).'&');
    }
}
