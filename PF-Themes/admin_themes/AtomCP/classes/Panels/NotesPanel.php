<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: NotesPanel.php
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
namespace AtomCP\Panels;

class NotesPanel extends \SqlHandler {
    public $key;
    public $title;
    public $check;

    public function __construct() {
        $locale = fusion_get_locale();
        $this->key = 'usernotes';
        $this->title = $locale['cp_105'];
        $this->check = column_exists('users', 'user_notes');
    }

    public function install() {
        self::add_column(DB_PREFIX.'users', 'user_notes', "TEXT NOT NULL");
    }

    public function uninstall() {
        self::drop_column(DB_PREFIX.'users', 'user_notes');
    }

    public function sidePanel() {
        if ($this->check) {
            $locale = fusion_get_locale();
            $userdata = fusion_get_userdata();

            if (isset($_POST['update_usernotes'])) {
                if (\defender::safe()) {
                    $db = [
                        'user_id'    => $userdata['user_id'],
                        'user_notes' => form_sanitizer($_POST['user_notes'], '', 'user_notes')
                    ];

                    dbquery_insert(DB_USERS, $db, 'update');
                    addnotice('success', $locale['cp_104']);
                    redirect(FUSION_REQUEST);
                }
            }

            echo openform('usernotes', 'post', FUSION_REQUEST);
            echo '<div class="openside panel panel-default">';
                echo '<div class="panel-heading"><i class="far fa-sticky-note"></i> '.$locale['cp_105'].'</div>';
                echo form_textarea('user_notes', '', $userdata['user_notes'], ['class' => 'm-0', 'inner_class' => 'bbr-0', 'placeholder' => $locale['cp_106']]);
                echo '<div class="panel-footer p-5">';
                    echo form_button('update_usernotes', $locale['save'], $locale['save'], ['class' => 'btn-success btn-block', 'icon' => 'fa fa-hdd-o']);
                echo '</div>';
            echo '</div>';
            echo closeform();
        }
    }
}
