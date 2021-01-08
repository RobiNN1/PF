<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: team.tpl.php
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

if (!function_exists('render_team')) {
    function render_team($info) {
        $locale = fusion_get_locale('', TM_LOCALE);

        opentable($locale['tm_title']);

        echo '<div class="table-responsive"><table class="table table-striped table-bordered">';
        echo '<thead><tr>';
            echo '<th></th>';
            echo '<th>'.$locale['tm_001'].'</th>';
            echo '<th>'.$locale['tm_002'].'</th>';
            echo '<th>'.$locale['tm_003'].'</th>';
            echo '<th>'.$locale['tm_004'].'</th>';
        echo '</tr></thead>';

        if (!empty($info)) {
            foreach ($info as $data) {
                echo '<tr>';
                    echo '<td><img style="width:40px;height:40px;" src="'.$data['photo'].'" alt="'.$data['name'].'"></td>';
                    echo '<td>'.$data['name'].'</td>';
                    echo '<td>'.$data['position'].'</td>';
                    echo '<td>'.$data['profession'].'</td>';
                    echo '<td>';
                        echo $data['info'];
                        if (!empty($data['user_data'])) {
                            echo '<a class="display-block" href="'.BASEDIR.'profile.php?lookup='.$data['user_data']['user_id'].'">'.$locale['tm_006'].'</a>';
                        }
                    echo '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="5" class="text-center">'.$locale['tm_007'].'</td></tr>';
        }
        echo '</table></div>';

        closetable();
    }
}
