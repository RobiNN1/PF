<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: Main.php
| Author: YOUR_NAMELICENSE_TEXT
+--------------------------------------------------------*/
namespace ADDON_NAME;

class Main {
    public function __construct() {
        $settings = fusion_get_settings();

        echo '<div class="container-fluid">';
            echo '<header>';
                $menu_config = [
                    'container_fluid' => TRUE,
                    'show_header'     => TRUE
                ];
                echo \PHPFusion\SiteLinks::setSubLinks($menu_config)->showSubLinks();
            echo '</header>';

            echo '<div class="notices">';
                echo renderNotices(getnotices(['all', FUSION_SELF]));
            echo '</div>';

            echo '<section class="main-content">';
                echo defined('AU_CENTER') && AU_CENTER ? AU_CENTER : '';
                echo showbanners(1);

                echo '<div class="row">';
                $content = ['sm' => 12, 'md' => 12, 'lg' => 12];
                $left    = ['sm' => 3,  'md' => 2,  'lg' => 2];
                $right   = ['sm' => 3,  'md' => 2,  'lg' => 2];

                if (defined('LEFT') && LEFT) {
                    $content['sm'] = $content['sm'] - $left['sm'];
                    $content['md'] = $content['md'] - $left['md'];
                    $content['lg'] = $content['lg'] - $left['lg'];
                }

                if (defined('RIGHT') && RIGHT) {
                    $content['sm'] = $content['sm'] - $right['sm'];
                    $content['md'] = $content['md'] - $right['md'];
                    $content['lg'] = $content['lg'] - $right['lg'];
                }

                if (defined('LEFT') && LEFT) {
                    echo '<div class="col-xs-12 col-sm-'.$left['sm'].' col-md-'.$left['md'].' col-lg-'.$left['lg'].'">';
                        echo LEFT;
                    echo '</div>';
                }

                echo '<div class="col-xs-12 col-sm-'.$content['sm'].' col-md-'.$content['md'].' col-lg-'.$content['lg'].'">';
                    echo defined('U_CENTER') && U_CENTER ? U_CENTER : '';
                    echo CONTENT;
                    echo defined('L_CENTER') && L_CENTER ? L_CENTER : '';
                    echo showbanners(2);
                echo '</div>';

                if (defined('RIGHT') && RIGHT) {
                    echo '<div class="col-xs-12 col-sm-'.$right['sm'].' col-md-'.$right['md'].' col-lg-'.$right['lg'].'">';
                        echo RIGHT;
                    echo '</div>';
                }

                echo '</div>';

                echo defined('BL_CENTER') && BL_CENTER ? BL_CENTER : '';
            echo '</section>';

            echo '<footer>';
                $theme_settings = get_theme_settings('folder_name');

                if (!empty($theme_settings['facebook_url'])) {
                    echo '<a href="'.$theme_settings['facebook_url'].'" target="_blank"><i class="fa fa-facebook"></i></a>';
                }

                echo '<div class="row m-t-10">';
                    echo defined('USER1') && USER1 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER1.'</div>' : '';
                    echo defined('USER2') && USER2 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER2.'</div>' : '';
                    echo defined('USER3') && USER3 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER3.'</div>' : '';
                    echo defined('USER4') && USER4 ? '<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">'.USER4.'</div>' : '';
                echo '</div>';

                echo showFooterErrors();
                echo showcopyright().showprivacypolicy();

                if ($settings['rendertime_enabled'] == 1 || $settings['rendertime_enabled'] == 2) {
                    echo '<br/><small>'.showrendertime().showMemoryUsage().'</small>';
                }

                echo '<br/>'.showcounter();
                echo parse_text($settings['footer'], ['parse_smileys' => FALSE, 'add_line_breaks' => TRUE]);
            echo '</footer>';
        echo '</div>';
    }
}
