<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Debugger.php
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
namespace AtomCP;

use PHPFusion\Database\DatabaseFactory;

class Debugger {
    public function show($query_log = FALSE) {
        $locale = fusion_get_locale();
        $settings = fusion_get_settings();
        $memoryusage = $this->getMemoryUsage();
        $queries = $this->getQueries();
        $errors = dbcount("(error_id)", DB_ERRORS);

        $render_time = substr((microtime(TRUE) - START_TIME), 0, 7);
        $_SESSION['performance'][] = $render_time;

        if (count($_SESSION['performance']) > 5) {
            array_shift($_SESSION['performance']);
        }

        $average_speed = $render_time;

        $diff = 0;
        if (isset($_SESSION['performance'])) {
            $average_speed = substr(array_sum($_SESSION['performance']) / count($_SESSION['performance']), 0, 7);
            $previous_render = array_values(array_slice($_SESSION['performance'], -2, 1, TRUE));
            $diff = (float)$render_time - (!empty($previous_render) ? (float)$previous_render[0] : 0);
        }

        echo '<div class="debugger hidden-xs">';
            echo '<div class="debugger-navbar clearfix">';
                echo '<ul class="debugger-navbar-nav">';
                    echo '<li><a title="'.$locale['cp_300'].'" '.(checkrights('PI') ? 'data-toggle="tab" data-target="#systeminfo" aria-controls="systeminfo"' : 'style="cursor: default;"').'><i class="phpfusion-icon"></i></a></li>';
                    echo '<li>';
                        echo '<a title="'.$locale['cp_301'].'" style="cursor: default;"><i class="fa fa-clock-o"></i> '.$render_time.'s</a>';
                        echo '<div class="debug-dropup">';
                            echo '<div><small><label>'.$locale['cp_302'].'</label>: '.$average_speed.'</small></div>';
                            echo '<div><small><label>'.$locale['cp_303'].'</label>: '.$diff.'</small></div>';
                        echo '</div>';
                    echo '</li>';
                    echo '<li><a title="'.$locale['cp_304'].'" style="cursor: default;"><i class="fa fa-memory"></i> '.$memoryusage.'</a></li>';
                    echo '<li>';
                        echo '<a title="'.$locale['cp_305'].'" '.($query_log == TRUE ? 'data-toggle="tab" data-target="#queries" aria-controls="queries"' : 'style="cursor: default;"').'><i class="fa fa-database"></i> '.$queries.'</a>';
                        echo '<div class="debug-dropup">';
                            echo '<div><small><label>'.$locale['cp_306'].'</label>: '.str_replace('\\PHPFusion\\Database\Driver\\', '', \PHPFusion\Database\DatabaseFactory::getDriverClass()).'</small></div>';
                            echo '<div><small><label>'.$locale['cp_307'].'</label>: '.dbconnection()->getServerVersion().'</small></div>';
                            echo '<div><small><label>'.$locale['cp_308'].'</label>: '.DB_PREFIX.'</small></div>';
                        echo '</div>';
                    echo '</li>';
                    echo '<li><a id="showerrorlog" href="'.ADMIN.'errors.php'.fusion_get_aidlink().'" title="'.fusion_get_locale('ERROR_400', LOCALE.LOCALESET.'admin/errors.php').'" '.($errors === 0 ? ' style="cursor: default;"' : '').'><i class="fa fa-bug"></i> '.$errors.'</a></li>';
                echo '</ul>';

                echo '<div class="close" id="closedebugger" title="'.$locale['close'].'" style="display: none;"><i class="fa fa-times"></i></div>';
            echo '</div>';

            echo '<div class="debugger-content" style="display: none;">';
                echo '<div class="tab-content">';
                    if (checkrights('PI')) {
                        echo '<div class="tab-pane" id="systeminfo">';
                            echo '<h3 class="m-0 m-b-15">'.$locale['cp_300'].'</h3>';

                            echo '<h4>'.$locale['cp_309'].'</h4>';

                            echo '<div class="row">';
                                echo '<div class="col-xs-12 col-sm-3"><label>PHPFusion</label></div>';
                                echo '<div class="col-xs-12 col-sm-9">'.$settings['version'].'</div>';
                            echo '</div>';
                            echo '<div class="row">';
                                echo '<div class="col-xs-12 col-sm-3"><label>'.$locale['cp_310'].'</label></div>';
                                echo '<div class="col-xs-12 col-sm-9">'.php_uname().'</div>';
                            echo '</div>';
                            echo '<div class="row">';
                                echo '<div class="col-xs-12 col-sm-3"><label>'.$locale['cp_311'].'</label></div>';
                                echo '<div class="col-xs-12 col-sm-9">'.$_SERVER['SERVER_SOFTWARE'].'</div>';
                            echo '</div>';

                            echo '<div class="row">';
                                echo '<div class="col-xs-12 col-sm-3"><label>'.$locale['cp_312'].'</label></div>';
                                echo '<div class="col-xs-12 col-sm-9">'.$_SERVER['DOCUMENT_ROOT'].'</div>';
                            echo '</div>';

                            echo '<h4>PHP</h4>';

                            echo '<div class="row">';
                                echo '<div class="col-xs-12 col-sm-3"><label>'.$locale['cp_313'].'</label></div>';
                                echo '<div class="col-xs-12 col-sm-9">'.phpversion().'</div>';
                            echo '</div>';
                            echo '<div class="row">';
                                echo '<div class="col-xs-12 col-sm-3"><label>PHP SAPI</label></div>';
                                echo '<div class="col-xs-12 col-sm-9">'.php_sapi_name().'</div>';
                            echo '</div>';
                            echo '<div class="row">';
                                echo '<div class="col-xs-12 col-sm-3"><label>'.$locale['cp_314'].'</label></div>';
                                echo '<div class="col-xs-12 col-sm-9">';
                                    foreach (get_loaded_extensions() as $ext) {
                                        echo $ext.', ';
                                    }
                                echo '</div>';
                            echo '</div>';
                        echo '</div>'; // #systeminfo
                    }

                    if ($query_log == TRUE) {
                        echo '<div class="tab-pane" id="queries">';
                            echo '<h3 class="m-0 m-b-15">'.$locale['cp_305'].'</h3>';

                            add_to_head('<link rel="stylesheet" href="'.INCLUDES.'bbcodes/code/prism.css">');
                            add_to_footer('<script src="'.INCLUDES.'bbcodes/code/prism.js"></script>');

                            $queries = DatabaseFactory::getConnection('default')->getQueryLog();
                            $i = 1;
                            foreach ($queries as $query) {
                                $highlighted = $query[0] > '0.01';

                                echo '<div class="well">';
                                    echo '<div class="text-bold">Query #'.$i.': <span class="text-'.($highlighted ? 'danger' : 'success').'">'.$query[0].'</span> seconds</div>';
                                    echo '<pre><code class="language-sql">'.$query[1].'</code></pre>';
                                    $query_data_end = end($query[3]);
                                    echo '<code>'.$query_data_end['file'].':'.$query_data_end['line'].'</code> <span class="badge">'.$query_data_end['function'].'</span>';

                                    if (is_array($query[3])) {
                                        echo ' - <a data-toggle="collapse" data-target="#trace'.$i.'" aria-expanded="false" aria-controls="trace'.$i.'" class="pointer">Stack Trace</a>';
                                        echo '<div id="trace'.$i.'" class="collapse"><div class="alert alert-info m-t-15">';

                                        foreach ($query[3] as $id => $backtrace) {
                                            echo '<div class="m-b-5"><kbd>Stack Trace #'.$id.': '.(!empty($backtrace['file']) ? $backtrace['file'] : '').':'.(!empty($backtrace['line']) ? $backtrace['line'] : '').'</kbd> <span class="badge">'.$backtrace['function'].'</span></div>';

                                            if (!empty($backtrace['args'][0])) {
                                                $statements = $backtrace['args'][0];
                                                if (is_array($backtrace['args'][0])) {
                                                    $statements = '';
                                                    foreach ($backtrace['args'][0] as $line) {
                                                        if (!is_array($line)) {
                                                            $statements .= '<br/>'.$line;
                                                        }
                                                    }
                                                }

                                                $parameters = '';
                                                if (!empty($backtrace['args'][1])) {
                                                    if (is_array($backtrace['args'][1])) {
                                                        $parameters .= 'array('.PHP_EOL;
                                                        foreach ($backtrace['args'][1] as $key => $value) {
                                                            $parameters .= '&nbsp;&nbsp;&nbsp;&nbsp;[\''.$key.'\'] => \''.$value.'\','.PHP_EOL;
                                                        }
                                                        $parameters .= ');';
                                                    }
                                                }

                                                echo !empty($statements) ? '<div class="m-b-5">Statement: <code>'.$statements.'</code></div>' : '';
                                                echo !empty($parameters) ? '<div class="m-b-5">Parameters: <pre><code class="language-php">'.$parameters.'</code></pre></div>' : '';
                                            }
                                        }

                                        echo '</div></div>';
                                    }
                                echo '</div>';
                                $i++;
                            }
                        echo '</div>'; // #queries
                    }

                echo '</div>';

                //echo '<div id="footer_errors">'.showFooterErrors().'</div>';
            echo '</div>';
        echo '</div>';
    }

    private function getMemoryUsage() {
        return parsebytesize(memory_get_peak_usage(FALSE));
    }

    private function getQueries() {
        $db_connection = DatabaseFactory::getConnection('default');
        return $db_connection::getGlobalQueryCount();
    }
}
