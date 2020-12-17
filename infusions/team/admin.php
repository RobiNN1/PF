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

pageAccess('TM');

$locale = fusion_get_locale('', TM_LOCALE);
$aidlink = fusion_get_aidlink();

add_to_title($locale['tm_title_admin']);

add_breadcrumb(['link' => INFUSIONS.'team/admin.php'.fusion_get_aidlink(), 'title' => $locale['tm_title_admin']]);

opentable($locale['tm_title_admin']);

$data = [
    'team_id'    => 0,
    'userid'     => 0,
    'name'       => '',
    'position'   => '',
    'profession' => '',
    'info'       => '',
    'image'      => '',
    'item_order' => 0,
    'language'   => LANGUAGE
];

$edit = (isset($_GET['action']) && $_GET['action'] == 'edit') && isset($_GET['team_id']);
$allowed_section = ['list', 'form'];
$_GET['section'] = isset($_GET['section']) && in_array($_GET['section'], $allowed_section) ? $_GET['section'] : 'list';

if (isset($_GET['section']) && $_GET['section'] == 'form') {
    $tab['title'][] = $locale['back'];
    $tab['id'][]    = 'back';
    $tab['icon'][]  = 'fa fa-fw fa-arrow-left';
}

$tab['title'][] = $locale['tm_title'];
$tab['id'][]    = 'list';
$tab['icon'][]  = 'fa fa-fw fa-users';

$tab['title'][] = $edit ? $locale['edit'] : $locale['add'];
$tab['id'][]    = 'form';
$tab['icon'][]  = 'fa fa-'.($edit ? 'pencil' : 'plus');

$result = dbquery("SELECT * FROM ".DB_TEAM." WHERE team_id='".(isset($_GET['team_id']) ? $_GET['team_id'] : '')."'");

if (isset($_GET['section']) && $_GET['section'] == 'back') redirect(FUSION_SELF.fusion_get_aidlink());

if ((isset($_GET['action']) && $_GET['action'] == 'delete') && (isset($_GET['team_id']) && isnum($_GET['team_id']))) {
    if (dbrows($result)) {

        if (!empty($data['image']) && file_exists(TEAM.'images/'.$data['image'])) {
            @unlink(TEAM.'images/'.$data['image']);
        }

        dbquery("DELETE FROM ".DB_TEAM." WHERE team_id='".intval($_GET['team_id'])."'");
    }

    addNotice('success', $locale['tm_011']);
    redirect(FUSION_SELF.$aidlink);
}

echo opentab($tab, $_GET['section'], 'teamadmin', TRUE, 'nav-tabs m-b-20');
switch ($_GET['section']) {
    case 'form':
        if ($edit) {
            add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $locale['edit']]);
        } else {
            add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $locale['add']]);
        }

        if (isset($_POST['save'])) {
            $data = [
                'team_id'    => form_sanitizer($_POST['team_id'], 0, 'team_id'),
                'userid'     => form_sanitizer($_POST['userid'], '', 'userid'),
                'name'       => form_sanitizer($_POST['name'], '', 'name'),
                'position'   => form_sanitizer($_POST['position'], '', 'position'),
                'profession' => form_sanitizer($_POST['profession'], '', 'profession'),
                'info'       => form_sanitizer($_POST['info'], '', 'info'),
                'image'      => isset($_POST['image']) ? form_sanitizer($_POST['image'], '', 'image') : '',
                'item_order' => form_sanitizer($_POST['item_order'], 0, 'item_order'),
                'language'   => form_sanitizer($_POST['language'], '', 'language')
            ];

            if (\defender::safe() && isset($_POST['delete_image']) && isset($_GET['team_id']) && isnum($_GET['team_id'])) {
                $result = dbquery("SELECT image FROM ".DB_TEAM." WHERE team_id='".$_GET['team_id']."'");
                if (dbrows($result)) {
                    $data += dbarray($result);
                    if (!empty($data['image']) && file_exists(TEAM.'images/'.$data['image'])) {
                        @unlink(TEAM.'images/'.$data['image']);
                    }
                }

                $data['image'] = '';
            } else if (\defender::safe() && !empty($_FILES['image']['name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
                $upload = form_sanitizer($_FILES['image'], '', 'image');
                if (empty($upload['error'])) {
                    $data['image'] = $upload['image_name'];
                }
            }

            if (empty($data['item_order'])) {
                $data['item_order'] = $data['item_order'] + 1;
                $result3 = dbquery("SELECT item_order FROM ".DB_TEAM." ORDER BY item_order DESC LIMIT 1");

                if (dbrows($result3) != 0) {
                    $data3 = dbarray($result3);
                    $data['item_order'] = $data3['item_order'] + 1;
                } else {
                    $data['item_order'] = 1;
                }
            }

            if (dbcount('(team_id)', DB_TEAM, "team_id='".$data['team_id']."'")) {
                dbquery_insert(DB_TEAM, $data, 'update');
                if (\defender::safe()) {
                    addNotice('success', $locale['tm_010']);
                    redirect(FUSION_SELF.$aidlink);
                }
            } else {
                dbquery_insert(DB_TEAM, $data, 'save');
                if (\defender::safe()) {
                    addNotice('success', $locale['tm_009']);
                    redirect(FUSION_SELF.$aidlink);
                }
            }
        }

        if (isset($_GET['move']) && isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['team_id']) && isnum($_GET['team_id'])) {
            $data2 = dbarray(dbquery("SELECT team_id, item_order FROM ".DB_TEAM." WHERE team_id = '".intval($_GET['team_id'])."'"));

            if ($_GET['move'] == 'md') {
                dbquery("UPDATE ".DB_TEAM." SET item_order = item_order - 1 WHERE item_order = '".($data2['item_order'] + 1)."'");
                dbquery("UPDATE ".DB_TEAM." SET item_order = item_order + 1 WHERE team_id = '".$data2['team_id']."'");
            }

            if ($_GET['move'] == 'mup') {
                dbquery("UPDATE ".DB_TEAM." SET item_order = item_order + 1 WHERE item_order = '".($data2['item_order'] - 1)."'");
                dbquery("UPDATE ".DB_TEAM." SET item_order = item_order - 1 WHERE team_id = '".$data2['team_id']."'");
            }

            redirect(FUSION_SELF.$aidlink);
        }

        if ((isset($_GET['action']) && $_GET['action'] == 'edit') && (isset($_GET['team_id']) && isnum($_GET['team_id']))) {
            if (dbrows($result)) {
                $data = dbarray($result);
            } else {
                redirect(FUSION_SELF.$aidlink);
            }
        }

        echo openform('teamform', 'post', FUSION_REQUEST, ['enctype' => TRUE]);
        echo form_hidden('team_id', '', $data['team_id']);
        echo form_hidden('item_order', '', $data['item_order']);
        echo form_text('name', $locale['tm_001'], $data['name'], ['inline' => TRUE]);
        echo form_user_select('userid', $locale['tm_008'], $data['userid'], ['inline' => TRUE, 'allow_self' => TRUE]);
        echo form_text('position', $locale['tm_002'], $data['position'], ['inline' => TRUE]);
        echo form_text('profession', $locale['tm_003'], $data['profession'], ['inline' => TRUE]);

        echo form_textarea('info', $locale['tm_004'], $data['info'], ['inline' => TRUE]);

        if (!empty($data['image'])) {
            echo '<div class="clearfix list-group-item m-b-20">';
            echo '<div class="pull-left m-r-10">';
            echo thumbnail(TEAM.'images/'.$data['image'], '80px');
            echo '</div>';
            echo '<div class="overflow-hide">';
            echo '<span class="text-dark strong">'.$locale['tm_005'].'</span>';
            echo form_checkbox('delete_image', $locale['delete'], '');
            echo form_hidden('image', '', $data['image']);
            echo '</div>';
            echo '</div>';
        } else {
            echo form_fileinput('image', $locale['tm_005'], '', [
                'upload_path'     => TEAM.'images/',
                'max_width'       => 400,
                'max_height'      => 400,
                'max_byte'        => 1048576,
                'type'            => 'image',
                'delete_original' => FALSE,
                'width'           => '100%',
                'inline'          => TRUE,
                'template'        => 'thumbnail',
                'ext_tip'         => 'Max: 400x400px, 1MB'
            ]);
        }

        if (multilang_table('TM')) {
            echo form_select('language[]', $locale['global_ML100'], $data['language'], [
                'options'     => fusion_get_enabled_languages(),
                'placeholder' => $locale['choose'],
                'width'       => '100%',
                'inline'      => TRUE,
                'multiple'    => TRUE
            ]);
        } else {
            echo form_hidden('language', '', $data['language']);
        }

        echo form_button('save', $locale['save'], 'save', ['class' => 'btn-success']);
        echo closeform();
        break;
    default:
        $result = dbquery("SELECT * FROM ".DB_TEAM." ".(multilang_table('TM') ? " WHERE ".in_group('language', LANGUAGE) : '')." ORDER BY item_order ASC");

        echo '<div class="table-responsive"><table class="table table-striped table-bordered">';
            echo '<thead><tr>';
                echo '<th>'.$locale['tm_001'].'</th>';
                echo '<th>'.$locale['tm_002'].'</th>';
                echo '<th>'.$locale['tm_003'].'</th>';
                echo '<th>'.$locale['order'].'</th>';
                echo '<th>'.$locale['actions'].'</th>';
            echo '</tr></thead>';

            if (dbrows($result)) {
                while ($data = dbarray($result)) {
                    echo '<tr>';
                        echo '<td>'.$data['name'].'</td>';
                        echo '<td>'.$data['position'].'</td>';
                        echo '<td>'.$data['profession'].'</td>';
                        echo '<td>';
                            if ($data['item_order'] == 1) {
                                echo '<a href="'.FUSION_SELF.$aidlink.'&section=form&action=edit&move=md&team_id='.$data['team_id'].'"><i class="fa fa-lg fa-angle-down"></i></a>';
                            } else if ($data['item_order'] == dbrows($result)) {
                                echo '<a href="'.FUSION_SELF.$aidlink.'&section=form&action=edit&move=mup&team_id='.$data['team_id'].'"><i class="fa fa-lg fa-angle-up"></i></a>';
                            } else {
                                echo '<a href="'.FUSION_SELF.$aidlink.'&section=form&action=edit&move=mup&team_id='.$data['team_id'].'"><i class="fa fa-lg fa-angle-up m-r-10"></i></a>';
                                echo '<a href="'.FUSION_SELF.$aidlink.'&section=form&action=edit&move=md&team_id='.$data['team_id'].'"><i class="fa fa-lg fa-angle-down"></i></a>';
                            }
                        echo '</td>';
                        echo '<td>';
                            echo '<a href="'.FUSION_SELF.fusion_get_aidlink().'&section=form&action=edit&team_id='.$data['team_id'].'">'.$locale['edit'].'</a> | ';
                            echo '<a href="'.FUSION_SELF.fusion_get_aidlink().'&action=delete&team_id='.$data['team_id'].'">'.$locale['delete'].'</a>';
                        echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="5" class="text-center">'.$locale['tm_007'].'</td></tr>';
            }
        echo '</table></div>';
        break;
}
echo closetab();

closetable();

require_once THEMES.'templates/footer.php';
