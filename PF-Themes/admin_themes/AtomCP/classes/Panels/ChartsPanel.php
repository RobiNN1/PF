<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: ChartsPanel.php
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

class ChartsPanel {
    public $key;
    public $title;
    public $check;

    public function __construct() {
        $locale = fusion_get_locale();
        $theme_settings = atomcp_settings();
        $this->key = 'charts';
        $this->title = $locale['cp_206'];
        $this->check = !empty($theme_settings['charts']);

        add_to_footer('<script src="'.ATOMCP.'assets/chart.min.js"></script>');
        add_to_jquery("var chart_colors = new Array('#FF6384', '#36A2EB', '#FFCD56', '#58595b', '#acc236', '#FF9F40', '#9966FF', '#C9CBCF', '#f67019', '#f53794', '#4BC0C0', '#537bc4', '#166a8f', '#4dc9f6', '#00a950', '#8549ba');");
    }

    public function install() {
        dbquery("INSERT INTO ".DB_SETTINGS_THEME." (settings_name, settings_value, settings_theme) VALUES ('charts', '1', 'AtomCP')");
    }

    public function uninstall() {
        dbquery("DELETE FROM ".DB_SETTINGS_THEME." WHERE settings_theme='AtomCP' AND settings_name='charts'");
    }

    public function mainPanel() {
        $locale = fusion_get_locale();

        if (iSUPERADMIN && $this->check) {
            add_to_jquery('$("#toggle_chart").on("change", function () {
                $("option:selected", this).tab("show");
            });');

            $toggler = '<div class="charts-select display-inline-block m-l-15" style="max-width: 200px;"><select class="form-control input-sm" id="toggle_chart">';
            $toggler .= '<option data-target="#monthly" selected>'.$locale['cp_108'].'</option>';
            $toggler .= '<option data-target="#yearly">'.$locale['cp_109'].'</option>';
            $toggler .= '</select></div>';

            openside($locale['cp_107'].$toggler, '', ['id' => 100, 'collapse' => TRUE, 'side_class' => FALSE, 'body' => FALSE]);
                echo '<div class="charts-tabs">';
                    $this->registrations();

                    echo '<div class="tab-content">';
                        echo '<div class="tab-pane active" id="monthly">';
                            echo '<canvas id="monthly_chart"></canvas>';
                        echo '</div>';
                        echo '<div class="tab-pane" id="yearly">';
                            echo '<canvas id="yearly_chart"></canvas>';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            closeside('', TRUE, FALSE);
        }
    }

    private function registrations() {
        $locale = fusion_get_locale();

        $month_array = [];

        $result = cdquery('acp_chart_reg', "
            SELECT YEAR(FROM_UNIXTIME(root.user_joined)) AS root_year, MONTH(FROM_UNIXTIME(root.user_joined)) AS root_month, COUNT(root.user_id) AS user_count
            FROM ".DB_USERS." root
            GROUP BY root_year, root_month ORDER BY root_year ASC, root_month ASC
        ");

        if (cdrows($result)) {
            while ($data = cdarray($result)) {
                $month_array[$data['root_year']][$data['root_month']] = $data['user_count'];
            }
        }

        $months_array = [];

        foreach ($month_array as $year => $months) {
            $array = [];

            for ($i = 1; $i <= 12; $i++) {
                $array[$i] = isset($months[$i]) ? $months[$i] : 0;
            }

            $months_array[$year] = $array;
        }

        $years = '';
        $counts = '';
        $jan = '';
        $feb = '';
        $mar = '';
        $apr = '';
        $may = '';
        $jun = '';
        $jul = '';
        $aug = '';
        $sep = '';
        $oct = '';
        $nov = '';
        $dec = '';

        foreach ($months_array as $year => $months) {
            $years .= $year.',';
            $count = 0;

            foreach ($months as $value) {
                $count += $value;
            }

            $counts .= $count.',';

            $jan .= $months[1].',';
            $feb .= $months[2].',';
            $mar .= $months[3].',';
            $apr .= $months[4].',';
            $may .= $months[5].',';
            $jun .= $months[6].',';
            $jul .= $months[7].',';
            $aug .= $months[8].',';
            $sep .= $months[9].',';
            $oct .= $months[10].',';
            $nov .= $months[11].',';
            $dec .= $months[12].',';
        }

        $months_label = explode('|', $locale['shortmonths']);

        add_to_jquery("
            new Chart(document.getElementById('yearly_chart'), {
                type: 'line',
                data: {
                    labels: [".$years."],
                    datasets: [{
                        label: 'Count',
                        data: [".$counts."],
                        backgroundColor: chart_colors[1],
                        borderColor: chart_colors[1],
                        borderWidth: 3,
                        fill: false
                    }]
                }
            });

            new Chart(document.getElementById('monthly_chart'), {
                type: 'bar',
                data: {
                    labels: [".$years."],
                    datasets: [{
                        label: '".$months_label[1]."',
                        data: [".$jan."],
                        backgroundColor: chart_colors[0],
                    },{
                        label: '".$months_label[2]."',
                        data: [".$feb."],
                        backgroundColor: chart_colors[1],
                    },{
                        label: '".$months_label[3]."',
                        data: [".$mar."],
                        backgroundColor: chart_colors[2],
                    },{
                        label: '".$months_label[4]."',
                        data: [".$apr."],
                        backgroundColor: chart_colors[12],
                    },{
                        label: '".$months_label[5]."',
                        data: [".$may."],
                        backgroundColor: chart_colors[4],
                    },{
                        label: '".$months_label[6]."',
                        data: [".$jun."],
                        backgroundColor: chart_colors[5],
                    },{
                        label: '".$months_label[7]."',
                        data: [".$jul."],
                        backgroundColor: chart_colors[8],
                    },{
                        label: '".$months_label[8]."',
                        data: [".$aug."],
                        backgroundColor: chart_colors[6],
                    },{
                        label: '".$months_label[9]."',
                        data: [".$sep."],
                        backgroundColor: chart_colors[7],
                    },{
                        label: '".$months_label[10]."',
                        data: [".$oct."],
                        backgroundColor: chart_colors[14],
                    },{
                        label: '".$months_label[11]."',
                        data: [".$nov."],
                        backgroundColor: chart_colors[9],
                    },{
                        label: '".$months_label[12]."',
                        data: [".$dec."],
                        backgroundColor: chart_colors[10],
                    }]
                },
                options: {
                    scales: {
                        xAxes: [{stacked: true}],
                        yAxes: [{stacked: true}]
                    }
                }
            });
        ");
    }

    public function sidePanel() {
        $locale = fusion_get_locale();

        if ($this->check) {
            $members = cdarray(cdquery('acp_mstats', "SELECT
                (SELECT COUNT(user_id) FROM ".DB_USERS." WHERE user_status=8) AS inactive,
                (SELECT COUNT(user_id) FROM ".DB_USERS." WHERE user_status<=1 OR user_status=3 OR user_status=5) AS registered,
                (SELECT COUNT(user_id) FROM ".DB_USERS." WHERE user_status=2) AS unactivated,
                (SELECT COUNT(user_id) FROM ".DB_USERS." WHERE user_status=4) AS security_ban,
                (SELECT COUNT(user_id) FROM ".DB_USERS." WHERE user_status=5) AS canceled,
                (SELECT COUNT(user_id) FROM ".DB_USERS.") AS total
            "));

            add_to_jquery("
                var chart = new Chart(document.getElementById('members_stats'), {
                    type: 'pie',
                    data: {
                        labels: [
                            '".$locale['262']." (".$members['registered'].")',
                            '".$locale['252']." (".$members['unactivated'].")',
                            '".$locale['253']." (".$members['security_ban'].")',
                            '".$locale['263']." (".$members['canceled'].")',
                            '".$locale['264']." (".$members['inactive'].")'
                        ],
                        datasets: [{
                            data: [
                                ".$members['registered'].",
                                ".$members['unactivated'].",
                                ".$members['security_ban'].",
                                ".$members['canceled'].",
                                ".$members['inactive']."
                            ],
                            backgroundColor: [chart_colors[10], chart_colors[1], chart_colors[0], chart_colors[2], chart_colors[5]],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        title: {
                            display: true,
                            text: '".$locale['260'].": ".$members['total']."'
                        }
                    }
                });
            ");

            echo '<div class="openside panel panel-default info-panel"><div class="panel-body">';
                echo '<canvas id="members_stats" height="300"></canvas>';
            echo '</div></div>';
        }
    }
}
