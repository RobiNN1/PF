<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: AboutUs.php
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
namespace MaterialTheme\Footer;

use MaterialTheme\Core;

class AboutUs extends Core {
    public static function panel() {
        $settings = fusion_get_settings();

        ob_start();

        echo '<h3 class="title">'.self::setLocale('au_01').'</h3>';
        echo '<img src="'.BASEDIR.$settings['sitebanner'].'" alt="'.$settings['sitename'].'" class="m-t-5 m-b-5 img-responsive">';
        echo $settings['description'];
        echo '<br/>';

        echo nl2br(parse_textarea($settings['footer'], FALSE));

        if ($settings['visitorcounter_enabled']) {
            echo '<br/>';
            echo showcounter();
        }

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}
