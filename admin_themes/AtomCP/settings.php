<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: settings.php
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
require_once __DIR__.'/../../../maincore.php';
require_once THEMES.'templates/admin_header.php';

if (!iSUPERADMIN) {
    redirect(BASEDIR.'index.php');
}

$locale = fusion_get_locale('', ATOMCP_LOCALE);
$theme_settings = get_theme_settings('AtomCP');

\PHPFusion\BreadCrumbs::getInstance()->addBreadCrumb(['link' => FUSION_REQUEST, 'title' => $locale['cp_200']]);

opentable($locale['cp_200']);

echo '<div class="row">';
$file_list = makefilelist(ATOMCP.'classes/Panels/', '.|..|index.php');
foreach ($file_list as $name) {
    $name = str_replace('.php', '', $name);
    $panel = new \ReflectionClass('AtomCP\\Panels\\'.$name);
    $panel = $panel->newInstance();

    echo '<div class="col-xs-6 col-sm-3 col-md-2">';
    openside($panel->title);
        echo openform($panel->key.'form', 'post', FUSION_REQUEST);
        if (isset($_POST['install_'.$panel->key])) {
            if (\defender::safe()) {
                $panel->install();
            }

            addnotice('success', $locale['cp_203']);
            redirect(FUSION_REQUEST);
        }

        if (isset($_POST['uninstall_'.$panel->key])) {
            if (\defender::safe()) {
                $panel->uninstall();
            }

            addnotice('success', $locale['cp_204']);
            redirect(FUSION_REQUEST);
        }

        if (!$panel->check) {
            echo form_button('install_'.$panel->key, $locale['cp_201'], 'install', [
                'class' => 'btn-success',
                'icon'  => 'fa fa-magnet'
            ]);
        } else {
            echo form_button('uninstall_'.$panel->key, $locale['cp_202'], 'uninstall', [
                'class' => 'btn-danger',
                'icon'  => 'fa fa-trash'
            ]);
        }
        echo closeform();
    closeside();
    echo '</div>';
}
echo '</div>';

closetable();

require_once THEMES.'templates/footer.php';
