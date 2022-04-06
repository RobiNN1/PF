<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Tags.php
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

class Tags {
    public static function displayForumTags($info) {
        $locale = fusion_get_locale();

        Main::header();

        echo '<div class="row">';
            echo '<div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">';
                echo '<div class="forum-title">'.$locale['forum_tag_0100'].'</div>';

                if (isset($_GET['tag_id'])) {
                    echo forum_filter($info);

                    if (!empty($info['threads']['pagenav'])) {
                        echo '<div class="text-right">'.$info['threads']['pagenav'].'</div>';
                    }

                    echo '<div class="panel panel-primary forum-panel">';
                        if (!empty($info['threads'])) {
                            echo '<div class="list-group">';
                                if (!empty($info['threads']['sticky'])) {
                                    foreach ($info['threads']['sticky'] as $cdata) {
                                        echo '<div class="list-group-item list-group-item-hover">';
                                            render_thread_item($cdata);
                                        echo '</div>';
                                    }
                                }

                                if (!empty($info['threads']['item'])) {
                                    foreach ($info['threads']['item'] as $cdata) {
                                        echo '<div class="list-group-item list-group-item-hover">';
                                            render_thread_item($cdata);
                                        echo '</div>';
                                    }
                                }
                            echo '</div>';
                        } else {
                            echo '<div class="text-center p-20">'.$locale['forum_0269'].'</div>';
                        }
                    echo '</div>';

                    if (!empty($info['threads']['pagenav'])) {
                        echo '<div class="text-right hidden-xs m-t-15">'.$info['threads']['pagenav'].'</div>';
                    }

                    if (!empty($info['threads']['pagenav2'])) {
                        echo '<div class="hidden-sm hidden-md hidden-lg m-t-15">'.$info['threads']['pagenav2'].'</div>';
                    }
                } else {
                    echo '<div class="row">';
                        if (!empty($info['tags'])) {
                            unset($info['tags'][0]);

                            foreach ($info['tags'] as $tag_data) {
                                echo '<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">';
                                    $color = $tag_data['tag_color'];
                                    echo '<div class="panel-body" style="height: 200px; background: '.$color.';">';
                                        echo '<a href="'.$tag_data['tag_link'].'">';
                                            echo '<h4 class="text-white">'.$tag_data['tag_title'].'</h4>';
                                        echo '</a>';
                                        echo '<p class="text-white">'.$tag_data['tag_description'].'</p>';

                                        if (!empty($tag_data['threads'])) {
                                            echo '<hr/>';
                                            echo '<span class="tag_result text-white">';
                                                $link = FORUM.'viewthread.php?thread_id='.$tag_data['threads']['thread_id'];
                                                echo '<a class="text-white" href="'.$link.'">';
                                                    echo trim_text($tag_data['threads']['thread_subject'], 10);
                                                echo '</a> - '.timer($tag_data['threads']['thread_lastpost']);
                                            echo '</span>';
                                        }
                                    echo '</div>';
                                echo '</div>';
                            }
                        }
                    echo '</div>';
                }
            echo '</div>';

            echo '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">';
                echo '<a href="'.FORUM.'newthread.php" class="btn btn-success btn-block m-b-20">'.$locale['forum_0057'].'</a>';

                Main::tags();
            echo '</div>';
        echo '</div>';
    }
}
