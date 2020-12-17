<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
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

pageAccess('SS');

$locale = fusion_get_locale('', SS_LOCALE);
$link = FUSION_SELF.fusion_get_aidlink();

require_once S_STATUS.'includes/functions.php';

add_to_title($locale['ss_title']);
add_breadcrumb(['link' => INFUSIONS.'server_status_panel/admin.php'.fusion_get_aidlink(), 'title' => $locale['ss_title']]);

opentable($locale['ss_title']);

$edit = (isset($_GET['action']) && $_GET['action'] == 'edit') && isset($_GET['server_id']);
$allowed_section = ['list', 'form'];
$_GET['section'] = isset($_GET['section']) && in_array($_GET['section'], $allowed_section) ? $_GET['section'] : 'list';

if (isset($_GET['section']) && $_GET['section'] == 'form') {
    $tab['title'][] = $locale['back'];
    $tab['id'][] = 'back';
    $tab['icon'][] = 'fa fa-fw fa-arrow-left';
}

$tab['title'][] = $locale['ss_title'];
$tab['id'][] = 'list';
$tab['icon'][] = 'fa fa-fw fa-server';

$tab['title'][] = $edit ? $locale['edit'] : $locale['add'];
$tab['id'][] = 'form';
$tab['icon'][] = 'fa fa-'.($edit ? 'pencil' : 'plus');

$result = dbquery("SELECT * FROM ".DB_SERVER_STATUS." WHERE server_id='".(isset($_GET['server_id']) ? $_GET['server_id'] : '')."'");

if (isset($_GET['section']) && $_GET['section'] == 'back') {
    redirect($link);
}

if ((isset($_GET['action']) && $_GET['action'] == 'delete') && (isset($_GET['server_id']) && isnum($_GET['server_id']))) {
    if (dbrows($result)) {
        dbquery("DELETE FROM ".DB_SERVER_STATUS." WHERE server_id='".intval($_GET['server_id'])."'");
    }

    addNotice('success', $locale['ss_notice_01']);
    redirect($link);
}

echo opentab($tab, $_GET['section'], 'ssdmin', TRUE, 'nav-tabs m-b-20');
switch ($_GET['section']) {
    case 'form':
        if ($edit) {
            add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $locale['edit']]);
        } else {
            add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $locale['add']]);
        }

        server_form();
        break;
    default:
        server_listing();
        break;
}
echo closetab();

closetable();

function server_form() {
    $locale = fusion_get_locale();
    $link = FUSION_SELF.fusion_get_aidlink();

    $data = [
        'server_id'    => 0,
        'server_ip'    => '',
        'server_port'  => 0,
        'server_qport' => 0,
        'server_type'  => '',
        'server_order' => 0
    ];

    if (isset($_POST['save'])) {
        $data = [
            'server_id'    => form_sanitizer($_POST['server_id'], 0, 'server_id'),
            'server_ip'    => form_sanitizer($_POST['server_ip'], '', 'server_ip'),
            'server_port'  => form_sanitizer($_POST['server_port'], 0, 'server_port'),
            'server_qport' => form_sanitizer($_POST['server_qport'], 0, 'server_qport'),
            'server_type'  => form_sanitizer($_POST['server_type'], '', 'server_type'),
            'server_order' => form_sanitizer($_POST['server_order'], '', 'server_order')
        ];

        if (empty($data['server_order'])) {
            $data['server_order'] = (int) $data['server_order'] + 1;
            $result3 = dbquery("SELECT server_order FROM ".DB_SERVER_STATUS." ORDER BY server_order DESC LIMIT 1");

            if (dbrows($result3) != 0) {
                $data3 = dbarray($result3);
                $data['server_order'] = $data3['server_order'] + 1;
            } else {
                $data['server_order'] = 1;
            }
        }

        if (dbcount('(server_id)', DB_SERVER_STATUS, "server_id='".$data['server_id']."'")) {
            dbquery_insert(DB_SERVER_STATUS, $data, 'update');
            if (\defender::safe()) {
                addNotice('success', $locale['ss_notice_02']);
                redirect($link);
            }
        } else {
            dbquery_insert(DB_SERVER_STATUS, $data, 'save');
            if (\defender::safe()) {
                addNotice('success', $locale['ss_notice_03']);
                redirect($link);
            }
        }
    }

    if (isset($_GET['move']) && (isset($_GET['action']) && $_GET['action'] == 'edit') && (isset($_GET['server_id']) && isnum($_GET['server_id']))) {
        $data = dbarray(dbquery("SELECT server_id, server_order FROM ".DB_SERVER_STATUS." where server_id = '".intval($_GET['server_id'])."'"));

        if ($_GET['move'] == 'md') {
            dbquery("UPDATE ".DB_SERVER_STATUS." SET server_order = server_order - 1 WHERE server_order = '".($data['server_order'] + 1)."'");
            dbquery("UPDATE ".DB_SERVER_STATUS." SET server_order = server_order + 1 WHERE server_id = '".$data['server_id']."'");
        }

        if ($_GET['move'] == 'mup') {
            dbquery("UPDATE ".DB_SERVER_STATUS." SET server_order = server_order + 1 WHERE server_order = '".($data['server_order'] - 1)."'");
            dbquery("UPDATE ".DB_SERVER_STATUS." SET server_order = server_order - 1 WHERE server_id = '".$data['server_id']."'");
        }

        addNotice('success', $locale['ss_notice_04']);
        redirect($link);
    }

    if ((isset($_GET['action']) && $_GET['action'] == 'edit') && (isset($_GET['server_id']) && isnum($_GET['server_id']))) {
        $result = dbquery("SELECT * FROM ".DB_SERVER_STATUS." WHERE server_id='".(isset($_GET['server_id']) ? $_GET['server_id'] : '')."'");

        if (dbrows($result)) {
            $data = dbarray($result);
        } else {
            redirect($link);
        }
    }

    echo '<div id="connection_result" class="alert alert-info" style="display:none;"></div>';

    echo openform('ssform', 'post', FUSION_REQUEST);
    echo form_hidden('server_id', '', $data['server_id']);
    echo form_hidden('server_order', '', $data['server_order']);

    echo form_select('server_type', $locale['ss_004'], $data['server_type'], [
        'options'  => protocols_list(),
        'required' => TRUE
    ]);

    echo '<div class="clearfix">';
        echo form_text('server_ip', 'IP', $data['server_ip'], ['required' => TRUE, 'class' => 'pull-left m-r-15']);
        echo form_text('server_port', 'Port', $data['server_port'], ['required' => TRUE, 'type' => 'number', 'class' => 'pull-left']);
    echo '</div>';

    echo form_text('server_qport', 'Query Port', $data['server_qport'], ['required' => TRUE, 'type' => 'number']);

    echo form_button('save', $locale['save'], 'save', ['class' => 'btn-success']);

    add_to_footer("<script>let SITE_URL = '".fusion_get_settings('siteurl')."';let AID = '".fusion_get_aidlink()."';</script>");
    add_to_footer('<script src="'.S_STATUS.'includes/ajax/check_connection.min.js"></script>');
    echo '<button type="button" id="check-connection" class="btn btn-primary"><i class="fa fa-sync fa-spin" style="display:none;"></i> '.$locale['ss_005'].'</button>';
    echo closeform();
}

function server_listing() {
    $locale = fusion_get_locale();
    $link = FUSION_SELF.fusion_get_aidlink();

    $limit = 16;
    $total_rows = dbcount("(server_id)", DB_SERVER_STATUS);
    $rowstart = isset($_GET['rowstart']) && ($_GET['rowstart'] <= $total_rows) ? $_GET['rowstart'] : 0;

    $result = dbquery("SELECT * FROM ".DB_SERVER_STATUS." ORDER BY server_order LIMIT $rowstart, $limit");
    $rows = dbrows($result);

    echo '<div class="table-responsive"><table class="table table-striped table-bordered">';
        echo '<thead><tr>';
            echo '<td>'.$locale['ss_006'].'</td>';
            echo '<td>IP:Port</td>';
            echo '<td>'.$locale['order'].'</td>';
            echo '<td>'.$locale['actions'].'</td>';
        echo '</tr></thead>';

        $GameQ = new \GameQ\GameQ();
        $GameQ->setOption('timeout', 3);

        Phpfastcache\CacheManager::setDefaultConfig(new Phpfastcache\Config\ConfigurationOption([
            'path' => __DIR__.'/cache'
        ]));

        $InstanceCache = Phpfastcache\CacheManager::getInstance('files');

        if (dbrows($result)) {
            while ($data = dbarray($result)) {
                $host = $data['server_ip'].':'.$data['server_port'];

                $GameQ->addServer([
                    'type' => $data['server_type'],
                    'host' => $host,
                    'options' => [
                        'query_port' => $data['server_qport']
                    ]
                ]);

                $results = $GameQ->process();
                $cache_key = str_replace('.', '_', $data['server_ip'].'_'.$data['server_port']);

                $CachedString = $InstanceCache->getItem($cache_key);
                if (!$CachedString->isHit()) {
                    $CachedString->set($results[$host])->expiresAfter(60);
                    $InstanceCache->save($CachedString);
                }

                $server = $CachedString->get();

                echo '<tr>';
                    echo '<td>';
                        if (file_exists(S_STATUS.'icons/'.$data['server_type'].'.png')) {
                            echo '<img alt="'.$server['gq_name'].'" title="'.$server['gq_name'].'" class="img-responsive display-inline m-r-10" style="width: 32px;" src="'.S_STATUS.'icons/'.$data['server_type'].'.png">';
                        }
                        echo $server['gq_hostname'];
                    echo '</td>';
                    echo '<td>'.$data['server_ip'].':'.$data['server_port'].'</td>';
                    echo '<td>';
                        if ($data['server_order'] == 1) {
                            echo '<a href="'.$link.'&section=form&action=edit&move=md&server_id='.$data['server_id'].'"><i class="fa fa-lg fa-angle-down"></i></a>';
                        } else if ($data['server_order'] == dbrows($result)) {
                            echo '<a href="'.$link.'&section=form&action=edit&move=mup&server_id='.$data['server_id'].'"><i class="fa fa-lg fa-angle-up"></i></a>';
                        } else {
                            echo '<a href="'.$link.'&section=form&action=edit&move=mup&server_id='.$data['server_id'].'"><i class="fa fa-lg fa-angle-up m-r-10"></i></a>';
                            echo '<a href="'.$link.'&section=form&action=edit&move=md&server_id='.$data['server_id'].'"><i class="fa fa-lg fa-angle-down"></i></a>';
                        }
                    echo '</td>';
                    echo '<td>';
                        echo '<a href="'.$link.'&section=form&action=edit&server_id='.$data['server_id'].'">'.$locale['edit'].'</a> | ';
                        echo '<a href="'.$link.'&action=delete&server_id='.$data['server_id'].'">'.$locale['delete'].'</a>';
                    echo '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="4" class="text-center">'.$locale['ss_notice_05'].'</td></tr>';
        }
    echo '</table></div>';

    if ($total_rows > $rows) {
        echo makepagenav($rowstart, $limit, $total_rows, $limit, clean_request('', ['aid', 'section'], TRUE).'&');
    }
}

require_once THEMES.'templates/footer.php';
