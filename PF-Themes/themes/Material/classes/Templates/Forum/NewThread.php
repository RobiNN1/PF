<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: NewThread.php
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
namespace MaterialTheme\Templates\Forum;

class NewThread {
    public static function displayForumPostForm($info) {
        $locale = fusion_get_locale();

        Main::header();

        echo '<div class="row">';
            echo '<div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">';
                echo '<h3 class="m-t-0">'.$info['title'].'</h3>';
                opentable('');
                    echo $info['description'] ? '<h4>'.$info['description'].'</h4>' : '';

                    echo $info['openform'];
                    echo $info['forum_field'];
                    echo $info['subject_field'];
                    echo !empty($info['tags_field']) ? $info['tags_field'] : '';
                    echo $info['message_field'];
                    echo $info['edit_reason_field'];
                    echo $info['forum_id_field'];
                    echo $info['thread_id_field'];
                    echo $info['poll_form'];

                    $tab_title['title'][0] = $locale['forum_0602'];
                    $tab_title['id'][0] = 'postopts';
                    $tab_title['icon'][0] = '';
                    $tab_active = tab_active($tab_title, 0);
                    $tab_content = opentabbody($tab_title['title'][0], 'postopts', $tab_active);
                    $tab_content .= '<div class="well m-t-20">';
                    $tab_content .= $info['delete_field'];
                    $tab_content .= $info['sticky_field'];
                    $tab_content .= $info['notify_field'];
                    $tab_content .= $info['lock_field'];
                    $tab_content .= $info['hide_edit_field'];
                    $tab_content .= $info['smileys_field'];
                    $tab_content .= $info['signature_field'];
                    $tab_content .= '</div>';
                    $tab_content .= closetabbody();

                    if (!empty($info['attachment_field'])) {
                        $tab_title['title'][1] = $locale['forum_0557'];
                        $tab_title['id'][1] = 'attach_tab';
                        $tab_title['icon'][1] = '';
                        $tab_content .= opentabbody($tab_title['title'][1], 'attach_tab', $tab_active);
                        $tab_content .= '<div class="well m-t-20">'.$info['attachment_field'].'</div>';
                        $tab_content .= closetabbody();
                    }

                    echo opentab($tab_title, $tab_active, 'newthreadopts');
                    echo $tab_content;
                    echo closetab();
                    echo $info['post_buttons'];
                    echo $info['closeform'];
                closetable();

                echo !empty($info['last_posts_reply']) ? '<div class="card">'.$info['last_posts_reply'].'</div>' : '';
            echo '</div>';

            echo '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">';
                Main::tags();
            echo '</div>';
        echo '</div>';
    }
}
