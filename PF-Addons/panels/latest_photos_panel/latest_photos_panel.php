<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: latest_photos_panel.php
| Author: RobiNN
| Version: 1.0.1
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

if (defined('GALLERY_EXIST')) {
    include_once INFUSIONS.'gallery/functions.php';

    $result = dbquery("
        SELECT ph.photo_id, ph.photo_title, ph.photo_datestamp, ph.photo_filename, ph.photo_thumb1, ph.photo_thumb2, pa.album_id, pa.album_access FROM ".DB_PHOTOS." ph
        LEFT JOIN ".DB_PHOTO_ALBUMS." AS pa USING (album_id)
        WHERE ".groupaccess('pa.album_access')." ORDER BY ph.photo_datestamp DESC LIMIT 0,5
    ");

    if (dbrows($result) > 0) {
        if (file_exists(INFUSIONS.'latest_photos_panel/locale/'.LANGUAGE.'.php')) {
            $locale = fusion_get_locale('', INFUSIONS.'latest_photos_panel/locale/'.LANGUAGE.'.php');
        } else {
            $locale = fusion_get_locale('', INFUSIONS.'latest_photos_panel/locale/English.php');
        }

        openside($locale['LP01']);
            echo '<div class="text-center">';
            while($data = dbarray($result)) {
                $img_path = return_photo_paths($data);
                if (!empty($img_path['photo_thumb2'])) {
                    $img_path = $img_path['photo_thumb2'];
                } else if (!empty($img_path['photo_thumb1'])) {
                    $img_path = $img_path['photo_thumb1'];
                } else {
                    $img_path = $img_path['photo_filename'];
                }
                echo '<a class="display-inline-block m-r-10" href="'.INFUSIONS.'gallery/gallery.php?photo_id='.$data['photo_id'].'">';
                    echo '<img class="img-responsive" style="max-width: 150px;" src="'.$img_path.'" alt="'.$data['photo_title'].'" title="'.$data['photo_title'].'">';
                    echo '<span>'.trimlink($data['photo_title'], 23).'</span>';
                echo '</a>';
            }
            echo '</div>';
        closeside();
    }
}
