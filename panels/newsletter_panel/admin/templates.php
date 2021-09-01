<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: templates.php
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
$link = NEWSLETTER.'admin.php'.fusion_get_aidlink().'&section=templates';
$nsl_settings = get_settings('newsletter_panel');

$data = [
    'tpl_id'           => 0,
    'tpl_name'         => '',
    'tpl_body'         => '',
    'tpl_style'        => '',
    'tpl_datestamp'    => time(),
    'tpl_priority'     => 3,
    'tpl_file'         => ''
];

if ((isset($_GET['action']) && $_GET['action'] == 'delete') && (isset($_GET['tpl_id']) && isnum($_GET['tpl_id']))) {
    dbquery("DELETE FROM ".DB_NEWSLETTER_TEMPLATES." WHERE tpl_id='".$_GET['tpl_id']."'");

    addNotice('success', $locale['nsl_notice_15']);
    redirect($link);
}

if (isset($_POST['save_tpl']) || isset($_POST['test_tpl'])) {
    $data = [
        'tpl_id'           => form_sanitizer($_POST['tpl_id'], '0', 'tpl_id'),
        'tpl_name'         => form_sanitizer($_POST['tpl_name'], '', 'tpl_name'),
        'tpl_datestamp'    => form_sanitizer($_POST['tpl_datestamp'], '', 'tpl_datestamp'),
        'tpl_priority'     => form_sanitizer($_POST['tpl_priority'], '', 'tpl_priority'),
        'tpl_body'         => '',
        'tpl_style'        => form_sanitizer($_POST['tpl_style'], '', 'tpl_style'),
        'tpl_file'         => ''
    ];

    if ($_POST['tpl_file'] !== 'none') {
        $data['tpl_file'] = form_sanitizer($_POST['tpl_file'], '', 'tpl_file');
        $data['tpl_body'] = '';
        $data['tpl_style'] = '';
    } else {
        $data['tpl_body'] = form_sanitizer($_POST['tpl_body'], '', 'tpl_body');
        $data['tpl_file'] = '';
    }

    if (isset($_POST['test_tpl'])) {
        if ($_POST['tpl_file'] !== 'none') {
            $body = file_get_contents(NEWSLETTER.'email_templates/'.$data['tpl_file']);
        } else {
            $body = $data['tpl_body'];
        }

        $sender = !empty($nsl_settings['test_email']) ? $nsl_settings['test_email'] : $nsl_settings['sender_email'];
        $mail = send_newsletter($data['tpl_name'], $body, $sender, $data['tpl_priority'], random_token(), $data['tpl_style']);
        if ($mail['result'] == TRUE) {
            addNotice('success', $locale['nsl_notice_16'].' '.$nsl_settings['sender_email']);
        } else {
            addNotice('warning', $locale['nsl_notice_17'].' '.$mail['error']);
        }
    }

    if (dbcount("(tpl_id)", DB_NEWSLETTER_TEMPLATES, "tpl_id='".$data['tpl_id']."'")) {
        dbquery_insert(DB_NEWSLETTER_TEMPLATES, $data, 'update');
        if (\defender::safe() && !isset($_POST['test_tpl'])) {
            addNotice('success', $locale['nsl_notice_18']);
        }
    } else {
        dbquery_insert(DB_NEWSLETTER_TEMPLATES, $data, 'save');
        if (\defender::safe() && !isset($_POST['test_tpl'])) {
            addNotice('success', $locale['nsl_notice_19']);
        }
    }

    redirect(FUSION_REQUEST);
}

if ((isset($_GET['action']) && $_GET['action'] == 'edit') && (isset($_GET['tpl_id']) && isnum($_GET['tpl_id']))) {
    $result = dbquery("SELECT * FROM ".DB_NEWSLETTER_TEMPLATES." WHERE tpl_id='".intval($_GET['tpl_id'])."'");
    if (dbrows($result)) {
        $data = dbarray($result);
    } else {
        redirect($link);
    }
}

if (get('ref') == 'form') {
    echo openform('tplform', 'post', FUSION_REQUEST);
    echo form_hidden('tpl_id', '', $data['tpl_id']);
    echo form_hidden('tpl_datestamp', '', $data['tpl_datestamp']);
    echo form_text('tpl_name', $locale['nsl_019'], $data['tpl_name'], ['required' => TRUE]);

    $email_templates = ['none' => $locale['nsl_041']];
    $templates = makefilelist(NEWSLETTER.'email_templates/', '.|..|index.php|newsletter_confirm.html');
    foreach ($templates as $name) {
        $email_templates[$name] = $name;
    }

    echo form_select('tpl_file', $locale['nsl_042'], $data['tpl_file'], ['options' => $email_templates]);

    if (!empty($_GET['tpl'])) {
        if (file_exists(NEWSLETTER.'admin/tpls/'.$_GET['tpl'].'.html')) {
            $data['tpl_body'] = file_get_contents(NEWSLETTER.'admin/tpls/'.$_GET['tpl'].'.html');
            $data['tpl_style'] = (file_get_contents(NEWSLETTER.'admin/tpls/default.css'));
        }
    }

    add_to_head('<link rel="stylesheet" href="'.NEWSLETTER.'includes/grapesjs/css/grapes.min.css">');
    add_to_head('<link rel="stylesheet" href="'.NEWSLETTER.'includes/grapesjs/css/grapesjs-preset-newsletter.css">');
    add_to_footer('<script src="'.NEWSLETTER.'includes/grapesjs/js/grapes.min.js"></script>');
    add_to_footer('<script src="'.NEWSLETTER.'includes/grapesjs/js/grapesjs-preset-newsletter.min.js"></script>');
    add_to_footer("<script>
        let SITE_URL = '".fusion_get_settings('siteurl')."';
        let TPL_STYLE = `".htmlspecialchars_decode($data['tpl_style'])."`;
        let AID = '".fusion_get_aidlink()."';
    </script>");
    add_to_footer('<script src="'.NEWSLETTER.'includes/scripts.min.js?v='.filemtime(NEWSLETTER.'includes/scripts.min.js').'"></script>');
    // GrapesJS UI fix
    add_to_css('
        html {font-size:initial;}
        .gjs-block:before{font-size: 3em;}
        .gjs-block.fa:before{font-size: 1em;font-weight: 900;}
        .gjs-one-bg{background-color: #373d49;}
        select.gjs-field{background:#ddd;color:#000;}
        .gjs-rte-action.gjs-rte-inactive{font-size:.75rem!important;}
    ');

    echo '<div id="tpl-text"'.(!empty($data['tpl_file']) ? ' style="display: none;"' : '').'>';

    echo '<div id="editor-container"><div id="gjs">'.htmlspecialchars_decode($data['tpl_body']).'</div></div>';

    echo form_textarea('tpl_body', '', $data['tpl_body'], ['class' => 'display-none']);
    echo form_textarea('tpl_style', '', $data['tpl_style'], ['class' => 'display-none']);

    echo '</div>';

    echo form_checkbox('tpl_priority', $locale['nsl_043'], $data['tpl_priority'], [
        'type'    => 'radio',
        'inline_options' => 1,
        'options' => [
            3  => $locale['nsl_044'],
            2  => $locale['nsl_045'],
            1  => $locale['nsl_046']
        ]
    ]);

    echo form_button('save_tpl', $locale['save'], 'save_tpl', ['class' => 'btn-success']);
    echo form_button('test_tpl', $locale['nsl_047'], 'test_tpl', ['class' => 'btn-primary']);
    echo closeform();
} else {
    $allowed_actions = array_flip(['delete']);

    if (isset($_POST['table_action']) && isset($allowed_actions[$_POST['table_action']])) {
        $input = (isset($_POST['tpl_id'])) ? explode(',', form_sanitizer($_POST['tpl_id'], '', 'tpl_id')) : '';

        if (!empty($input)) {
            foreach ($input as $tpl_id) {
                if (dbcount("('tpl_id')", DB_NEWSLETTER_TEMPLATES, "tpl_id = :tpl_id", [':tpl_id' => (int)$tpl_id]) && \defender::safe()) {
                    switch ($_POST['table_action']) {
                        case 'delete':
                            dbquery("DELETE FROM ".DB_NEWSLETTER_TEMPLATES." WHERE tpl_id = :tpl_id", [':tpl_id' => (int)$tpl_id]);
                            addNotice('success', $locale['nsl_notice_20']);
                            break;
                        default:
                            redirect(FUSION_REQUEST);
                    }
                }
            }
            redirect(FUSION_REQUEST);
        }

        addNotice('warning', $locale['nsl_notice_21']);
        redirect(FUSION_REQUEST);
    }

    $limit = 15;
    $total_rows = dbcount("(tpl_id)", DB_NEWSLETTER_TEMPLATES);
    $rowstart = isset($_GET['rowstart']) && ($_GET['rowstart'] <= $total_rows) ? $_GET['rowstart'] : 0;

    $result = dbquery("SELECT * FROM ".DB_NEWSLETTER_TEMPLATES." LIMIT $rowstart, $limit");
    $rows = dbrows($result);

    if ($total_rows > $rows) {
        echo makepagenav($rowstart, $limit, $total_rows, $limit, clean_request('', ['aid', 'section']).'&');
    }

    echo '<div class="clearfix m-b-20">';
        echo '<div class="pull-right">';
            echo '<a data-toggle="modal" data-target="#selecttemplate" id="select_template" class="btn btn-primary btn-sm m-l-5" href="'.$link.'&ref=form"><i class="fa fa-plus"></i> '.$locale['add'].'</a>';
            echo '<button type="button" class="btn btn-danger btn-sm m-l-5" onclick="run_admin(\'delete\', \'#table_action\', \'#subs_table\');">'.$locale['delete'].'</button>';
        echo '</div>';
    echo '</div>';

    add_to_css('
        .select-tpl {border: 1px solid #ddd;border-radius: 4px;padding: 10px;margin: 4px;display: inline-block;text-align: center;width: calc(33.33333% - 8px);}
        .select-tpl > i {font-size: 50px;margin-bottom: 5px;}
        .select-tpl > span {display: block;}
    ');

    echo openmodal('selecttemplate', $locale['nsl_004'], ['class' => 'modal-sm', 'button_id' => 'select_template']);
    echo '<a class="select-tpl" href="'.$link.'&ref=form"><i class="fa fa-align-justify"></i> <span>'.$locale['nsl_048'].'</span></a>';
    echo '<a class="select-tpl" href="'.$link.'&ref=form&tpl=1_column"><i class="far fa-square"></i> <span>'.$locale['nsl_049'].'</span></a>';
    echo '<a class="select-tpl" href="'.$link.'&ref=form&tpl=2_columns"><i class="fa fa-columns"></i> <span>'.$locale['nsl_050'].'</span></a>';
    echo closemodal();

    echo openform('subs_table', 'post', FUSION_REQUEST);
    echo form_hidden('table_action');

    echo '<div class="table-responsive"><table id="subs-table" class="table table-striped table-bordered">';
        echo '<thead><tr>';
            echo '<th>'.form_checkbox('check_all', '', '', ['class' => 'm-b-0']).'</th>';
            echo '<th>'.$locale['nsl_019'].'</th>';
            echo '<th>'.$locale['nsl_043'].'</th>';
            echo '<th>'.$locale['nsl_051'].'</th>';
            echo '<th>'.$locale['actions'].'</th>';
        echo '</tr></thead>';
        echo '<tbody>';
            if (dbrows($result) > 0) {
                while ($data = dbarray($result)) {
                    echo '<tr>';
                        echo '<td>'.form_checkbox('tpl_id[]', '', '', ['value' => $data['tpl_id'], 'class' => 'm-0', 'input_id' => 'sub-id-'.$data['tpl_id']]).'</td>';
                        echo '<td>'.$data['tpl_name'].'</td>';
                        echo '<td>'.($data['tpl_priority'] == 3 ? $locale['nsl_044'] : ($data['tpl_priority'] == 2 ? $locale['nsl_045'] : $locale['nsl_046'])).'</td>';
                        echo '<td>'.showdate('shortdate', $data['tpl_datestamp']).'</td>';
                        echo '<td><a href="'.$link.'&ref=form&action=edit&tpl_id='.$data['tpl_id'].'">'.$locale['edit'].'</a> | <a href="'.$link.'&action=delete&tpl_id='.$data['tpl_id'].'" class="text-danger">'.$locale['delete'].'</a></td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="5" class="text-center">'.$locale['nsl_052'].'</td></tr>';
            }
        echo '</tbody>';
    echo '</div></table>';

    echo closeform();

    if ($total_rows > $rows) {
        echo makepagenav($rowstart, $limit, $total_rows, $limit, clean_request('', ['aid', 'section']).'&');
    }
}
