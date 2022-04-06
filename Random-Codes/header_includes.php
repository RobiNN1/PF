<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: header_includes.php
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

// Theme switcher

$settings = fusion_get_settings();

if (!defined('ADMIN_PANEL')) {
    $themes_path = THEMES;

    $themes = makefilelist($themes_path, '.|..|templates|admin_themes', TRUE, 'folders');

    if (session_get(COOKIE_PREFIX.'theme') && theme_exists(session_get(COOKIE_PREFIX.'theme'))) {
        $theme = session_get(COOKIE_PREFIX.'theme');
    } else if (!empty(fusion_get_userdata('user_theme'))) {
        $theme = fusion_get_userdata('user_theme');
    } else {
        $theme = $settings['theme'];
    }

    if ($theme == 'Default') {
        $theme = $settings['theme'];
    }

    if (isset($_POST['change'])) {
        $theme = form_sanitizer($_POST['theme'], $theme, 'theme');

        if (\defender::safe()) {
            session_add(COOKIE_PREFIX.'theme', $theme);
            addnotice('success', 'Theme has been changed');
            redirect(FUSION_REQUEST);
        }
    }

    add_to_jquery('
        $("#theme").bind("change", function () {
            $("#theme_preview").error(function() {
                $("#theme_preview").attr("src", "'.get_image('imagenotfound').'");
            });

            $("#theme_preview").attr("src", "'.$themes_path.'" + $(this).val() + "/screenshot.png");
        });
    ');


    add_to_css('
    html {
        height: auto;
    }
    body {
        margin-bottom: 70px;
    }
    #theme-switcher {
        position: fixed;
        width: 100%;
        z-index: 1000;
        bottom: 0;
        left: 0;
    }
    #theme-switcher > .alert {
        padding: 5px;
        border-radius: 0;
        border: 0;
        margin: 0;
    }
    #theme-switcher > .alert > div {
        width: calc(100% / 4);
        margin: 0 auto;
    }
    #theme_preview {
        width: 80px;
        max-height: 80px;
        margin-right: 20px;
    }
    #theme-switcher .select2-container {
        width: 125px !important;
    }
    @media (max-width: 767px) {
        #theme-switcher > .alert > div {
            width: 100%;
        }
    }
    ');

    ob_start();
    echo '<div id="theme-switcher"><div class="alert alert-info clearfix"><div>';
    echo '<div class="pull-left">';
    if (file_exists($themes_path.$theme.'/screenshot.png')) {
        echo '<img id="theme_preview" class="img-responsive" src="'.$themes_path.$theme.'/screenshot.png" alt="'.$theme.'">';
    } else {
        echo '<img id="theme_preview" class="img-responsive" src="'.get_image('imagenotfound').'" alt="'.$theme.'">';
    }
    echo '</div>';
    echo '<div class="pull-left" style="margin-top:14px;">';
    echo openform('themeswitcher', 'post', FUSION_REQUEST, ['class' => 'form-inline tswform']);
    $opts = [];
    foreach ($themes as $file) {
        $opts[$file] = $file;
    }

    echo form_select('theme', '', $theme, [
        'options'        => $opts,
        'callback_check' => !defined('ADMIN_PANEL') ? 'theme_exists' : '',
        'width'          => '100%',
        'inline'         => TRUE,
        'class'          => 'pull-left'
    ]);
    echo form_button('change', 'Change', 'change', ['class' => 'btn-primary m-l-20']);
    echo closeform();
    echo '</div>';
    echo '</div></div></div>';
    $html = ob_get_contents();
    add_to_footer($html);
    ob_end_clean();
}
