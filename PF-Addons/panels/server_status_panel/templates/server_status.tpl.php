<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: server_status.tpl.php
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

if (!function_exists('render_server_status')) {
    function render_server_status($servers) {
        $locale = fusion_get_locale();

        echo '<div class="table-responsive"><table class="table table-striped table-bordered">';
            echo '<thead><tr>';
                echo '<td>'.$locale['ss_006'].'</td>';
                echo '<td>IP:Port</td>';
                echo '<td>'.$locale['ss_007'].'</td>';
                echo '<td>'.$locale['ss_008'].'</td>';
            echo '</tr></thead>';
            echo '<tbody>';
                if (!empty($servers)) {
                    foreach ($servers as $item) {
                        $data = $item['data'];
                        $server = $item['server'];
                        $host = $data['server_ip'].':'.$data['server_port'];

                        echo '<tr>';
                            echo '<td class="no-break text-overflow-hide">';
                                if (file_exists(S_STATUS.'icons/'.$data['server_type'].'.png')) {
                                    echo '<img alt="'.$server['gq_name'].'" title="'.$server['gq_name'].'" class="img-responsive display-inline m-r-10" style="width: 32px;" src="'.S_STATUS.'icons/'.$data['server_type'].'.png">';
                                }
                                echo '<i class="fa fa-circle text-'.($server['gq_online'] ? 'success' : 'danger').'"></i> '.$server['gq_hostname'];
                            echo '</td>';
                            echo '<td>'.$host.(!empty($server['gq_joinlink']) ? ' <a href="'.$server['gq_joinlink'].'"><i class="fa fa-external-link-alt"></i></a>' : '').'</td>';
                            echo '<td>'.(!empty($server['gq_numplayers']) && !empty($server['gq_maxplayers']) ? $server['gq_numplayers'].'/'.$server['gq_maxplayers'] : 'N/A').'</td>';
                            echo '<td>'.(!empty($server['gq_mapname']) ? $server['gq_mapname'] : 'N/A').'</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="4" class="text-center">'.$locale['ss_notice_05'].'</td></tr>';
                }
            echo '</tbody>';
        echo '</table></div>';
    }
}
