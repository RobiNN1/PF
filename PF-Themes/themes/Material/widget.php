<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: widget.php
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
namespace MaterialTheme;

require_once 'theme_autoloader.php';

class MWidget {
    private static $settings;
    private $locale;
    private $options;
    private $link;
    private $sn_data = [
        'network_id'      => 0,
        'network_title'   => '',
        'network_icon'    => '',
        'network_link'    => '',
        'network_visible' => 1,
        'network_order'   => 0
    ];

    public function __construct() {
        self::$settings = get_theme_settings('Material');
        $this->locale = fusion_get_locale();
        $this->locale += $this->setLocale();

        $this->options = [
            0 => $this->locale['no'],
            1 => $this->locale['yes']
        ];
        $this->link = FUSION_SELF.fusion_get_aidlink().'&action=manage&theme=Material&section=widgets&mt_widget=';

        if (isset($_POST['cancel']) || (isset($_GET['mt_widget']) && $_GET['mt_widget'] == 'back')) {
            redirect(clean_request('', ['mt_widget', 'mt_action', 'network_id'], FALSE));
        }
    }

    private function setLocale($key = NULL) {
        return Core::setLocale($key);
    }

    public function displayAdmin() {
        echo '<h3>'.$this->setLocale('widget_title').'</h3>';

        $allowed_pages = ['list', 'form_social_network', 'settings'];
        $_GET['mt_widget'] = isset($_GET['mt_widget']) && in_array($_GET['mt_widget'], $allowed_pages) ? $_GET['mt_widget'] : 'list';
        $edit = isset($_GET['mt_action']) && $_GET['mt_action'] === 'edit_network' && isset($_GET['network_id']) && isnum($_GET['network_id']);

        if (!isset($_GET['mt_widget']) || $_GET['mt_widget'] !== 'list') {
            $tab['title'][] = $this->locale['back'];
            $tab['id'][] = 'back';
            $tab['icon'][] = 'fa fa-fw fa-arrow-left';
        }

        $tab['title'][] = $this->locale['home'];
        $tab['id'][] = 'list';
        $tab['icon'][] = 'fa fa-th-large';

        $tab['title'][] = $this->setLocale('widget_'.($edit ? 'tab_01' : 'tab_02'));
        $tab['id'][] = 'form_social_network';
        $tab['icon'][] = 'fa fa-'.($edit ? 'pencil' : 'plus');

        $tab['title'][] = $this->setLocale('widget_tab_03');
        $tab['id'][] = 'settings';
        $tab['icon'][] = 'fa fa-cogs';


        echo opentab($tab, $_GET['mt_widget'], 'material_admin', TRUE, FALSE, 'mt_widget');
        echo '<div class="m-t-15">';

        switch ($_GET['mt_widget']) {
            case 'form_social_network':
                $this->formSocialNetwork();
                break;
            case 'settings':
                add_breadcrumb(['link' => $this->link.'settings', 'title' => $this->setLocale('widget_tab_03')]);
                $this->settings();
                break;
            default:
                $this->listing();
        }

        echo '</div>';
        echo closetab();

        $this->deleteSocialNetwork();
    }

    private function formSocialNetwork() {
        if (isset($_POST['save'])) {
            $this->sn_data = [
                'network_id'      => form_sanitizer($_POST['network_id'], 0, 'network_id'),
                'network_title'   => form_sanitizer($_POST['network_title'], '', 'network_title'),
                'network_icon'    => form_sanitizer($_POST['network_icon'], '', 'network_icon'),
                'network_link'    => form_sanitizer($_POST['network_link'], '', 'network_link'),
                'network_visible' => form_sanitizer($_POST['network_visible'], 0, 'network_visible'),
                'network_order'   => form_sanitizer($_POST['network_order'], 0, 'network_order')
            ];

            if (empty($this->sn_data['network_order'])) {
                $this->sn_data['network_order'] = $this->sn_data['network_order'] + 1;
            }

            $result = dbquery("SELECT network_order FROM ".DB_MT_NETWORKS." ORDER BY network_order DESC LIMIT 1");

            if (dbrows($result) != 0) {
                $data = dbarray($result);
                $this->sn_data['network_order'] = $data['network_order'] + 1;
            } else {
                $this->sn_data['network_order'] = 1;
            }

            if (\defender::safe()) {
                if (isset($_GET['mt_action']) && $_GET['mt_action'] == 'edit_network' && isset($_GET['network_id']) && isnum($_GET['network_id'])) {
                    dbquery_insert(DB_MT_NETWORKS, $this->sn_data, 'update');
                    addnotice('success', $this->setLocale('widget_notice_02'));
                } else {
                    dbquery_insert(DB_MT_NETWORKS, $this->sn_data, 'save');
                    addnotice('success', $this->setLocale('widget_notice_01'));
                }

                redirect($this->link.'list');
            }
        }

        if (isset($_GET['move']) && isset($_GET['mt_action']) && $_GET['mt_action'] == 'edit_network' && isset($_GET['network_id']) && isnum($_GET['network_id'])) {
            $data = dbarray(dbquery("SELECT network_id, network_order FROM ".DB_MT_NETWORKS." where network_id = '".intval($_GET['network_id'])."'"));

            if ($_GET['move'] == 'md') {
                dbquery("UPDATE ".DB_MT_NETWORKS." SET network_order = network_order - 1 WHERE network_order = '".($data['network_order'] + 1)."'");
                dbquery("UPDATE ".DB_MT_NETWORKS." SET network_order = network_order + 1 WHERE network_id = '".$data['network_id']."'");
            }

            if ($_GET['move'] == 'mup') {
                dbquery("UPDATE ".DB_MT_NETWORKS." SET network_order = network_order + 1 WHERE network_order = '".($data['network_order'] - 1)."'");
                dbquery("UPDATE ".DB_MT_NETWORKS." SET network_order = network_order - 1 WHERE network_id = '".$data['network_id']."'");
            }

            addnotice('success', $this->setLocale('widget_notice_08'));
            redirect($this->link.'list');
        }

        if (isset($_GET['mt_action']) && $_GET['mt_action'] == 'edit_network' && isset($_GET['network_id']) && isnum($_GET['network_id'])) {
            $result = dbquery("SELECT * FROM ".DB_MT_NETWORKS." WHERE network_id='".intval($_GET['network_id'])."'");
            if (dbrows($result)) {
                $this->sn_data = dbarray($result);
            } else {
                redirect($this->link.'list');
            }
        }

        openside('');
        echo openform('form_network', 'post', FUSION_REQUEST);
        echo form_hidden('network_id', '', $this->sn_data['network_id']);
        echo form_hidden('network_order', '', $this->sn_data['network_order']);
        echo form_text('network_title', $this->setLocale('widget_01'), $this->sn_data['network_title'], [
            'inline'      => TRUE,
            'required'    => TRUE,
            'placeholder' => 'Facebook'
        ]);
        echo form_text('network_icon', $this->setLocale('widget_02'), $this->sn_data['network_icon'], [
            'inline'      => TRUE,
            'required'    => TRUE,
            'placeholder' => 'fa fa-facebook-official'
        ]);
        echo form_text('network_link', $this->setLocale('widget_03'), $this->sn_data['network_link'], [
            'type'        => 'url',
            'inline'      => TRUE,
            'required'    => TRUE,
            'placeholder' => 'https://www.facebook.com/GenuineFusion'
        ]);
        echo form_select('network_visible', $this->setLocale('widget_04'), $this->sn_data['network_visible'], ['inline' => TRUE, 'options' => $this->options]);
        echo form_button('save', $this->locale['save'], 'save', ['class' => 'btn-success', 'icon' => 'fa fa-plus']);
        echo form_button('cancel', $this->locale['cancel'], 'cancel', ['icon' => 'fa fa-fw fa-times']);
        echo closeform();
        closeside();
    }

    private function deleteSocialNetwork() {
        if ((isset($_GET['mt_action']) && $_GET['mt_action'] == 'delete_network') && (isset($_GET['network_id']) && isnum($_GET['network_id']))) {
            dbquery("DELETE FROM ".DB_MT_NETWORKS." WHERE network_id='".intval($_GET['network_id'])."'");
            addnotice('success', $this->setLocale('widget_notice_03'));
            redirect($this->link.'list');
        }
    }

    private function settings() {
        if (isset($_POST['save_settings'])) {
            $settings = [
                'social_links'  => form_sanitizer($_POST['social_links'], 0, 'social_links'),
                'logo'          => form_sanitizer($_POST['logo'], 0, 'logo'),
                'footer_exlude' => form_sanitizer($_POST['footer_exlude'], '', 'footer_exlude'),
                'footer_col_1'  => form_sanitizer($_POST['footer_col_1'], '', 'footer_col_1'),
                'footer_col_2'  => form_sanitizer($_POST['footer_col_2'], '', 'footer_col_2'),
                'footer_col_3'  => form_sanitizer($_POST['footer_col_3'], '', 'footer_col_3'),
                'footer_col_4'  => form_sanitizer($_POST['footer_col_4'], '', 'footer_col_4')
            ];

            if (\defender::safe()) {
                foreach ($settings as $settings_name => $settings_value) {
                    $db = [
                        'settings_name'  => $settings_name,
                        'settings_value' => $settings_value,
                        'settings_theme' => 'Material'
                    ];

                    dbquery_insert(DB_SETTINGS_THEME, $db, 'update');
                }

                addnotice('success', $this->setLocale('widget_notice_04'));
                redirect(FUSION_REQUEST);
            }
        }

        echo openform('main_settings', 'post', FUSION_REQUEST, ['class' => 'm-b-20']);
        echo '<div class="panel panel-default">';
        echo '<div class="panel-heading">'.$this->setLocale('widget_title_01').'</div>';
        echo '<div class="panel-body">';
        openside();
        echo form_select('social_links', $this->setLocale('widget_05'), self::$settings['social_links'], [
            'inline'  => TRUE,
            'options' => $this->options
        ]);
        echo form_select('logo', $this->setLocale('widget_06'), self::$settings['logo'], [
            'inline'  => TRUE,
            'options' => [
                0 => $this->setLocale('widget_07'),
                1 => $this->setLocale('widget_08'),
                2 => $this->setLocale('widget_09')
            ]
        ]);
        closeside();

        openside();
        $panels = [];
        $file_list = makefilelist(THEME.'classes/Footer/', '.|..|.htaccess|.DS_Store|index.php');
        foreach ($file_list as $files) {
            $files = str_replace('.php', '', $files);

            $panels[$files] = strtr($files, [
                'AboutUs'        => $this->setLocale('au_01'),
                'ContactUs'      => $this->setLocale('cu_01'),
                'LatestArticles' => $this->setLocale('la_01'),
                'LatestBlogs'    => $this->setLocale('lb_01'),
                'LatestComments' => $this->locale['global_025'],
                'LatestNews'     => $this->setLocale('ln_01'),
                'UsersOnline'    => $this->setLocale('uo_01')
            ]);
        }

        $text = '<br/><small>'.fusion_get_locale('424', LOCALE.LOCALESET.'admin/settings.php').'</small>';

        echo form_textarea('footer_exlude', $this->setLocale('widget_23').$text, self::$settings['footer_exlude'], ['inline' => TRUE, 'autosize' => TRUE]);
        echo form_select('footer_col_1', $this->setLocale('widget_24'), self::$settings['footer_col_1'], ['options' => $panels, 'inline' => TRUE]);
        echo form_select('footer_col_2', $this->setLocale('widget_25'), self::$settings['footer_col_2'], ['options' => $panels, 'inline' => TRUE]);
        echo form_select('footer_col_3', $this->setLocale('widget_26'), self::$settings['footer_col_3'], ['options' => $panels, 'inline' => TRUE]);
        echo form_select('footer_col_4', $this->setLocale('widget_27'), self::$settings['footer_col_4'], ['options' => $panels, 'inline' => TRUE]);
        closeside();

        echo form_button('save_settings', $this->locale['save_changes'], $this->locale['save_changes'], ['class' => 'btn-success', 'icon' => 'fa fa-hdd-o']);
        echo form_button('cancel', $this->locale['cancel'], 'cancel', ['icon' => 'fa fa-fw fa-times']);
        echo '</div>';
        echo '</div>';
        echo closeform();
    }

    private function listing() {
        $result = dbquery("SELECT * FROM ".DB_MT_NETWORKS." ORDER BY network_order ASC");

        if (dbrows($result)) {
            echo '<table class="table table-responsive table-striped">';
                echo '<thead><tr>';
                    echo '<th>'.$this->setLocale('widget_01').'</th>';
                    echo '<th>'.$this->setLocale('widget_02').'</th>';
                    echo '<th>'.$this->setLocale('widget_03').'</th>';
                    echo '<th>'.$this->setLocale('widget_04').'</th>';
                    echo '<th>'.$this->setLocale('widget_10').'</th>';
                    echo '<th>'.$this->setLocale('widget_11').'</th>';
                    echo '<th>'.$this->locale['order'].'</th>';
                echo '</tr></thead>';
                echo '<tbody>';
                while ($data = dbarray($result)) {
                    $data['network_visible'] = $data['network_visible'] ? $this->locale['yes'] : $this->locale['no'];
                    echo '<tr>';
                        echo '<td>'.$data['network_title'].'</td>';
                        echo '<td>'.$data['network_icon'].'</td>';
                        echo '<td>'.$data['network_link'].'</td>';
                        echo '<td><span class="badge">'.$data['network_visible'].'</span></td>';
                        echo '<td><a href="'.$data['network_link'].'" title="'.$data['network_title'].'"><i class="'.$data['network_icon'].' fa-lg"></i></a></td>';
                        echo '<td>';
                            echo '<a href="'.$this->link.'form_social_network&mt_action=edit_network&network_id='.$data['network_id'].'" title="'.$this->locale['edit'].'"><i class="fa fa-pencil"></i></a>';
                            echo ' | ';
                            echo '<a class="text-danger" href="'.$this->link.'form_social_network&mt_action=delete_network&network_id='.$data['network_id'].'" title="'.$this->locale['delete'].'"><i class="fa fa-trash"></i></a>';
                        echo '</td>';
                        echo '<td>';
                    if ($data['network_order'] == 1) {
                        echo '<a href="'.$this->link.'form_social_network&mt_action=edit_network&move=md&network_id='.$data['network_id'].'"><i class="fa fa-lg fa-angle-down"></i></a>';
                    } else if ($data['network_order'] == dbrows($result)) {
                        echo '<a href="'.$this->link.'form_social_network&mt_action=edit_network&move=mup&network_id='.$data['network_id'].'"><i class="fa fa-lg fa-angle-up"></i></a>';
                    } else {
                        echo '<a href="'.$this->link.'form_social_network&mt_action=edit_network&move=mup&network_id='.$data['network_id'].'"><i class="fa fa-lg fa-angle-up m-r-10"></i></a>';
                        echo '<a href="'.$this->link.'form_social_network&mt_action=edit_network&move=md&network_id='.$data['network_id'].'"><i class="fa fa-lg fa-angle-down"></i></a>';
                    }
                        echo '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
            echo '</table>';
        } else {
            echo '<div class="well text-center">';
            echo str_replace(['[link]', '[/link]'], ['<a href="'.$this->link.'form_social_network">', '</a>'], $this->setLocale('widget_12'));
            echo '</div>';
        }
    }
}

$widget = new MWidget();
$widget->displayAdmin();
