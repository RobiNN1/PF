<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: HomePage.php
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
namespace MaterialTheme\Templates;

use MaterialTheme\Core;

class HomePage extends Core {
    public static function displayHome($info) {
        $locale = fusion_get_locale();
        self::setTplCss('homepage');

        echo '<div id="home-page">';
            foreach ($info as $content) {
                echo '<div class="module">';
                    echo '<div class="title"><h1>'.$content['blockTitle'].'</h1></div>';

                    if (!empty($content['data'])) {
                        echo '<div class="row">';
                            foreach ($content['data'] as $data) {
                                echo '<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">';
                                opentable('', 'home-item clearfix');
                                if (!empty($data['image'])) {
                                    echo '<div class="img-box">';
                                        echo '<a href="'.$data['url'].'">';
                                            echo '<img src="'.$data['image'].'" alt="'.$data['title'].'">';
                                        echo '</a>';
                                    echo '</div>';
                                }

                                echo '<div class="home-panel-content">';
                                    echo '<div class="home-panel-title"><a class="text-dark" href="'.$data['url'].'">'.trimlink($data['title'], 25).'</a></div>';
                                    echo '<div class="overflow-hide">'.$data['meta'].'</div>';
                                    echo '<br/><a class="show-more hidden-xs" href="'.$data['url'].'">'.$locale['global_700'].'</a>';
                                echo '</div>';

                                closetable();
                                echo '</div>';
                            }
                        echo '</div>';
                    } else {
                        echo '<div class="well text-center">'.$content['norecord'].'</div>';
                    }
                echo '</div>';
            }
        echo '</div>';
    }
}
