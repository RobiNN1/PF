<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: settings.php
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

$allowed_section = ['sending', 'headers', 'smtp'];
$_GET['settings'] = isset($_GET['settings']) && in_array($_GET['settings'], $allowed_section) ? $_GET['settings'] : 'sending';

$tab_settings['title'][] = $locale['nsl_006'];
$tab_settings['id'][]    = 'sending';
$tab_settings['title'][] = $locale['nsl_007'];
$tab_settings['id'][]    = 'headers';
$tab_settings['title'][] = 'SMTP';
$tab_settings['id'][]    = 'smtp';

echo opentab($tab_settings, $_GET['settings'], 'nslsettings', TRUE, 'nav-tabs', 'settings');
switch ($_GET['settings']) {
    case 'headers':
        additional_headers();
        break;
    case 'smtp':
        smtp_servers();
        break;
    default:
        sending_settings();
        break;
}
echo closetab();

function sending_settings() {
    $locale = fusion_get_locale();
    $nsl_settings = get_settings('newsletter_panel');

    if (isset($_POST['save'])) {
        $settings = [
            'sender_name'     => form_sanitizer($_POST['sender_name'], '', 'sender_name'),
            'sender_email'    => form_sanitizer($_POST['sender_email'], '', 'sender_email'),
            'show_email'      => form_sanitizer(isset($_POST['show_email']) ? 1 : 0, 0, 'show_email'),
            'add_dkim'        => form_sanitizer(isset($_POST['add_dkim']) ? 1 : 0, 0, 'add_dkim'),
            'dkim_domain'     => form_sanitizer($_POST['dkim_domain'], '', 'dkim_domain'),
            'dkim_private'    => form_sanitizer($_POST['dkim_private'], '', 'dkim_private'),
            'dkim_selector'   => form_sanitizer($_POST['dkim_selector'], '', 'dkim_selector'),
            'dkim_passphrase' => form_sanitizer($_POST['dkim_passphrase'], '', 'dkim_passphrase'),
            'dkim_identity'   => form_sanitizer($_POST['dkim_identity'], '', 'dkim_identity'),
            'how_to_send'     => form_sanitizer($_POST['how_to_send'], '', 'how_to_send'),
            'sendmail_path'   => form_sanitizer($_POST['sendmail_path'], '', 'sendmail_path'),
            'charset'         => form_sanitizer($_POST['charset'], '', 'charset'),
            'content_type'    => form_sanitizer($_POST['content_type'], '', 'content_type'),
            'test_email'      => form_sanitizer($_POST['test_email'], '', 'test_email'),
            'visibility'      => form_sanitizer($_POST['visibility'], 0, 'visibility')
        ];

        if (\defender::safe()) {
            foreach ($settings as $key => $value) {
                if (\defender::safe()) {
                    $data = [
                        'settings_name'  => $key,
                        'settings_value' => $value,
                        'settings_inf'   => 'newsletter_panel'
                    ];
                    dbquery_insert(DB_SETTINGS_INF, $data, 'update', ['primary_key' => 'settings_name']);
                }
            }

            addnotice('success', $locale['nsl_notice_01']);
        }

        redirect(FUSION_SELF.fusion_get_aidlink().'&section=settings');
    }


    echo '<div class="row">';
        echo '<div class="col-xs-12 col-sm-6">';
            openside();
            echo openform('savesettings', 'post', FUSION_REQUEST);
            echo form_text('sender_name', $locale['nsl_008'], $nsl_settings['sender_name']);
            echo form_text('sender_email', $locale['nsl_009'], $nsl_settings['sender_email']);
            echo form_text('test_email', $locale['nsl_014'], $nsl_settings['test_email']);
            echo form_checkbox('show_email', $locale['nsl_010'], $nsl_settings['show_email']);
            echo form_select('charset', $locale['nsl_011'], $nsl_settings['charset'], [
                'options' => [
                    'big5'           => 'Chinese (traditional)',
                    'euc-kr'         => 'Korean (EUC)',
                    'gb2312'         => 'Chinese (simplified)',
                    'iso-2022-jp'    => 'Japanese (ISO)',
                    'iso-8859-1'     => 'Western European (ISO)',
                    'iso-8859-10'    => 'Scandinavian (ISO)',
                    'iso-8859-13'    => 'Estonian (ISO)',
                    'iso-8859-14'    => 'Celtic (ISO)',
                    'iso-8859-15'    => 'Latin 9 (ISO)',
                    'iso-8859-16'    => 'Romanian (ISO)',
                    'iso-8859-2'     => 'Central European (ISO)',
                    'iso-8859-3'     => 'South European (ISO)',
                    'iso-8859-4'     => 'Baltic (ISO)',
                    'iso-8859-5'     => 'Cyrillic (ISO)',
                    'iso-8859-6'     => 'Arabic (ISO)',
                    'iso-8859-7'     => 'Greek (ISO)',
                    'iso-8859-8'     => 'Hebrew (ISO)',
                    'iso-8859-9'     => 'Turkish (ISO)',
                    'koi8-r'         => 'Cyrillic (Russian KOI8-R)',
                    'koi8-u'         => 'Cyrillic (Ukrainian KOI8-U)',
                    'ks_c_5601-1987' => 'Korean',
                    'utf-8'          => 'Unicode (UTF-8)',
                    'windows-1250'   => 'Central European (Windows)',
                    'windows-1251'   => 'Cyrillic (Windows)',
                    'windows-1252'   => 'Western European (Windows)',
                    'windows-1253'   => 'Greek (Windows)',
                    'windows-1254'   => 'Turkish (Windows)',
                    'windows-1255'   => 'Hebrew (Windows)',
                    'windows-1256'   => 'Arabic (Windows)',
                    'windows-1257'   => 'Baltic (Windows)',
                    'windows-1258'   => 'Vietnamese (Windows)',
                    'windows-874'    => 'Thai (Windows)'
                ]
            ]);

            echo form_select('content_type', $locale['nsl_012'], $nsl_settings['content_type'], [
                'options' => [
                    'html'  => 'HTML',
                    'plain' => $locale['nsl_013']
                ]
            ]);

            echo form_select('visibility', $locale['nsl_015'], $nsl_settings['visibility'], [
                'options' => fusion_get_groups(),
            ]);
            closeside();

            openside('Sending');
            echo form_checkbox('how_to_send', $locale['nsl_016'], $nsl_settings['how_to_send'], [
                'type'    => 'radio',
                'options' => [
                    'php'      => 'PHP',
                    'smtp'     => 'SMTP',
                    'sendmail' => 'Sendmail'
                ]
            ]);

            echo '<div id="sendmail_opt"'.($nsl_settings['how_to_send'] !== 'sendmail' ? ' style="display:none;"' : '').'>';
            echo form_text('sendmail_path', $locale['nsl_017'], $nsl_settings['sendmail_path']);
            echo '</div>';

            add_to_jquery("
                $('input[name=how_to_send]').change(function() {
                    var val = $('input[name=how_to_send]:checked').val();
                    if (val == 'sendmail') {
                        $('#sendmail_opt').show();
                    } else {
                        $('#sendmail_opt').hide();
                    }
                });
            ");
            closeside();
        echo '</div>';

        echo '<div class="col-xs-12 col-sm-6">';
            openside('DKIM');
            echo form_checkbox('add_dkim', $locale['nsl_018'], $nsl_settings['add_dkim']);
            echo form_text('dkim_domain', 'DKIM Domain', $nsl_settings['dkim_domain']);
            echo form_textarea('dkim_private', 'DKIM Private', $nsl_settings['dkim_private']);
            echo form_text('dkim_selector', 'DKIM Selector', $nsl_settings['dkim_selector']);
            echo form_text('dkim_passphrase', 'DKIM Passphrase', $nsl_settings['dkim_passphrase']);
            echo form_text('dkim_identity', 'DKIM Identity (Email)', $nsl_settings['dkim_identity'], ['type' => 'email']);
            closeside();
        echo '</div>';
    echo '</div>';

    echo form_button('save', $locale['save'], 'save');
    echo closeform();
}

function additional_headers() {
    $locale = fusion_get_locale();
    $link = NEWSLETTER.'admin.php'.fusion_get_aidlink().'&section=settings&settings=headers';

    $data = [
        'header_id'    => 0,
        'header_name'  => '',
        'header_value' => ''
    ];

    if ((isset($_GET['action']) && $_GET['action'] == 'delete') && (isset($_GET['header_id']) && isnum($_GET['header_id']))) {
        dbquery("DELETE FROM ".DB_NEWSLETTER_HEADERS." WHERE header_id='".$_GET['header_id']."'");

        addnotice('success', $locale['nsl_notice_02']);
        redirect($link);
    }

    if (isset($_POST['save_header'])) {
        $data = [
            'header_id'    => form_sanitizer($_POST['header_id'], '0', 'header_id'),
            'header_name'  => form_sanitizer($_POST['header_name'], '', 'header_name'),
            'header_value' => form_sanitizer($_POST['header_value'], '', 'header_value')
        ];

        if (dbcount("(header_id)", DB_NEWSLETTER_HEADERS, "header_id='".$data['header_id']."'")) {
            dbquery_insert(DB_NEWSLETTER_HEADERS, $data, 'update');
            if (\defender::safe()) {
                addnotice('success', $locale['nsl_notice_03']);
                redirect($link);
            }
        } else {
            dbquery_insert(DB_NEWSLETTER_HEADERS, $data, 'save');
            if (\defender::safe()) {
                addnotice('success', $locale['nsl_notice_04']);
                redirect($link);
            }
        }
    }

    if ((isset($_GET['action']) && $_GET['action'] == 'edit') && (isset($_GET['header_id']) && isnum($_GET['header_id']))) {
        $result = dbquery("SELECT * FROM ".DB_NEWSLETTER_HEADERS." WHERE header_id='".intval($_GET['header_id'])."'");
        if (dbrows($result)) {
            $data = dbarray($result);
        } else {
            redirect($link);
        }
    }

    echo '<div class="row">';
        echo '<div class="col-xs-12 col-sm-6">';
            openside();
            echo openform('customheaders', 'post', FUSION_REQUEST);
            echo form_hidden('header_id', '', $data['header_id']);

            echo '<div class="row">';
                echo '<div class="col-xs-12 col-sm-6">';
                    echo form_text('header_name', $locale['nsl_019'], $data['header_name'], ['required' => TRUE]);
                echo '</div>';

                echo '<div class="col-xs-12 col-sm-6">';
                    echo form_text('header_value', $locale['nsl_020'], $data['header_value'], ['required' => TRUE]);
                echo '</div>';
            echo '</div>';

            echo form_button('save_header', $locale['save'], 'save_header');
            echo closeform();
            closeside();
        echo '</div>';

        echo '<div class="col-xs-12 col-sm-6">';
            $result = dbquery("SELECT * FROM ".DB_NEWSLETTER_HEADERS);

            echo '<div class="table-responsive"><table class="table table-striped table-bordered m-b-20">';
                echo '<thead><tr>';
                    echo '<th>'.$locale['nsl_019'].'</th>';
                    echo '<th>'.$locale['nsl_020'].'</th>';
                    echo '<th>'.$locale['actions'].'</th>';
                echo '</tr></thead>';
                echo '<tbody>';
                    if (dbrows($result) > 0) {
                        while ($header = dbarray($result)) {
                            echo '<tr>';
                                echo '<td>'.$header['header_name'].'</td>';
                                echo '<td>'.$header['header_value'].'</td>';
                                echo '<td><a href="'.$link.'&action=edit&header_id='.$header['header_id'].'">'.$locale['edit'].'</a> | <a href="'.$link.'&action=delete&header_id='.$header['header_id'].'" class="text-danger">'.$locale['delete'].'</a></td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="3" class="text-center">'.$locale['nsl_021'].'</td></tr>';
                    }
                echo '</tbody>';
            echo '</table></div>';
        echo '</div>';
    echo '</div>';
}

function smtp_servers() {
    $locale = fusion_get_locale();
    $link = NEWSLETTER.'admin.php'.fusion_get_aidlink().'&section=settings&settings=smtp';

    $data = [
        'smtp_id'      => 0,
        'smtp_host'    => '',
        'smtp_port'    => 25,
        'smtp_name'    => '',
        'smtp_pass'    => '',
        'smtp_secure'  => 'no',
        'smtp_timeout' => 5,
        'smtp_active'  => 1
    ];

    if ((isset($_GET['action']) && $_GET['action'] == 'delete') && (isset($_GET['smtp_id']) && isnum($_GET['smtp_id']))) {
        dbquery("DELETE FROM ".DB_NEWSLETTER_SMTP." WHERE smtp_id='".$_GET['smtp_id']."'");

        addnotice('success', $locale['nsl_notice_05']);
        redirect($link);
    }

    if (isset($_POST['save_smtp'])) {
        $data = [
            'smtp_id'      => form_sanitizer($_POST['smtp_id'], 0, 'smtp_id'),
            'smtp_host'    => form_sanitizer($_POST['smtp_host'], '', 'smtp_host'),
            'smtp_port'    => form_sanitizer($_POST['smtp_port'], '', 'smtp_port'),
            'smtp_name'    => form_sanitizer($_POST['smtp_name'], '', 'smtp_name'),
            'smtp_pass'    => form_sanitizer($_POST['smtp_pass'], '', 'smtp_pass'),
            'smtp_secure'  => form_sanitizer($_POST['smtp_secure'], '', 'smtp_secure'),
            'smtp_timeout' => form_sanitizer($_POST['smtp_timeout'], 5, 'smtp_timeout'),
            'smtp_active'  => form_sanitizer($_POST['smtp_active'], 0, 'smtp_active')
        ];

        if (dbcount("(smtp_id)", DB_NEWSLETTER_SMTP, "smtp_id='".$data['smtp_id']."'")) {
            dbquery_insert(DB_NEWSLETTER_SMTP, $data, 'update');
            if (\defender::safe()) {
                addnotice('success', $locale['nsl_notice_06']);
                redirect($link);
            }
        } else {
            dbquery_insert(DB_NEWSLETTER_SMTP, $data, 'save');
            if (\defender::safe()) {
                addnotice('success', $locale['nsl_notice_07']);
                redirect($link);
            }
        }
    }

    if ((isset($_GET['action']) && $_GET['action'] == 'edit') && (isset($_GET['smtp_id']) && isnum($_GET['smtp_id']))) {
        $result = dbquery("SELECT * FROM ".DB_NEWSLETTER_SMTP." WHERE smtp_id='".intval($_GET['smtp_id'])."'");
        if (dbrows($result)) {
            $data = dbarray($result);
        } else {
            redirect($link);
        }
    }

    echo '<div class="row">';
        echo '<div class="col-xs-12 col-sm-6">';
            openside();
            echo openform('smtpservers', 'post', FUSION_REQUEST);
            echo form_hidden('smtp_id', '', $data['smtp_id']);
            echo form_text('smtp_host', $locale['nsl_022'], $data['smtp_host']);
            echo form_text('smtp_port', $locale['nsl_023'], $data['smtp_port']);
            echo form_text('smtp_name', $locale['nsl_024'], $data['smtp_name']);
            echo form_text('smtp_pass', $locale['nsl_025'], $data['smtp_pass']);
            echo form_text('smtp_timeout', $locale['nsl_026'], $data['smtp_timeout']);

            echo form_checkbox('smtp_active', $locale['nsl_027'], $data['smtp_active'], [
                'type' => 'radio',
                'options' => [
                    1 => $locale['yes'],
                    0 => $locale['no']
                ],
            ]);

            echo form_checkbox('smtp_secure', $locale['nsl_028'], $data['smtp_secure'], [
                'type'    => 'radio',
                'options' => [
                    'no'  => $locale['no'],
                    'ssl' => 'SSL',
                    'tls' => 'TLS'
                ]
            ]);

            echo form_button('save_smtp', $locale['save'], 'save_smtp');
            echo closeform();
            closeside();
        echo '</div>';

        echo '<div class="col-xs-12 col-sm-6">';
            $result = dbquery("SELECT * FROM ".DB_NEWSLETTER_SMTP);

            echo '<div class="table-responsive"><table class="table table-striped table-bordered m-b-20">';
            echo '<thead><tr>';
                echo '<th>'.$locale['nsl_022'].'</th>';
                echo '<th>'.$locale['nsl_023'].'</th>';
                echo '<th>'.$locale['nsl_024'].'</th>';
                echo '<th>'.$locale['nsl_026'].'</th>';
                echo '<th>'.$locale['status'].'</th>';
                echo '<th>'.$locale['actions'].'</th>';
            echo '</tr></thead>';
            echo '<tbody>';
            if (dbrows($result) > 0) {
                while ($smtp = dbarray($result)) {
                    echo '<tr>';
                        echo '<td>'.$smtp['smtp_host'].'</td>';
                        echo '<td>'.$smtp['smtp_port'].'</td>';
                        echo '<td>'.$smtp['smtp_name'].'</td>';
                        echo '<td>'.$smtp['smtp_timeout'].'</td>';
                        echo '<td>'.($smtp['smtp_active'] == 1 ? $locale['nsl_029'] : $locale['nsl_030']).'</td>';
                        echo '<td><a href="'.$link.'&action=edit&smtp_id='.$smtp['smtp_id'].'">'.$locale['edit'].'</a> | <a href="'.$link.'&action=delete&smtp_id='.$smtp['smtp_id'].'" class="text-danger">'.$locale['delete'].'</a></td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="6" class="text-center">'.$locale['nsl_031'].'</td></tr>';
            }
            echo '</tbody>';
            echo '</table></div>';
        echo '</div>';
    echo '</div>';
}
