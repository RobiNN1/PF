<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Error.php
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
namespace CzechiaTheme\Templates;

use CzechiaTheme\Core;
use CzechiaTheme\Main;

class Error extends Core {
    public static function displayErrorPage($data) {
        $locale = fusion_get_locale();
        $settings = fusion_get_settings();

        add_to_css('body {background: #0077C0;color: #fff;}.wrapper {margin: 50px auto;max-width: 600px;}.logo {max-height: 120px;}.status {font-size: 5em;}@media (min-width: 400px) {.status {font-size: 7em;}}@media (min-width: 768px) {.status {font-size: 10em;}}');

        Main::hideAll();

        $locale['error'] = str_replace('!', '', $locale['error']);
        set_title($locale['error'].' '.$data['status']);

        echo '<div class="wrapper">';
            echo '<div class="logo">';
                echo '<a href="'.BASEDIR.$settings['opening_page'].'" title="'.$settings['sitename'].'">';
                    echo '<img src="'.$settings['siteurl'].'/'.$settings['sitebanner'].'" class="logo center-x" alt="Logo"/>';
                echo '</a>';
            echo '</div>';

            echo '<div class="text-center">';
                echo '<h1 class="status">'.$data['status'].'</h1>';
                echo '<h2 class="title">'.$data['title'].'</h2>';
            echo '</div>';
        echo '</div>';
    }
}
