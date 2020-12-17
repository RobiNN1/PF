<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: upload.php
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

if (authorize_aid()) {
    if ($_FILES) {
        $upload_dir = NEWSLETTER.'email_templates/uploads';
        $result_array = [];

        foreach ($_FILES as $file) {
            $file_name = $file['name'];
            $tmp_name = $file['tmp_name'];
            $target_file_path = $upload_dir.'/'.$file_name;

            if (move_uploaded_file($tmp_name, $target_file_path)) {
                if ($file['error'] != UPLOAD_ERR_OK) {
                    echo json_encode(NULL);
                }

                $size = getimagesize($target_file_path);

                $result = [
                    'name'   => $file_name,
                    'type'   => 'image',
                    'src'    => str_replace(
                        NEWSLETTER,
                        fusion_get_settings('siteurl').'infusions/newsletter_panel/',
                        $target_file_path
                    ),
                    'height' => $size[0],
                    'width'  => $size[1]
                ];

                array_push($result_array, $result);
            }
        }

        echo json_encode(['data' => $result_array]);
    }
}

function authorize_aid() {
    $aid = (string)filter_input(INPUT_GET, 'aid');

    if (defined('iAUTH') && isset($aid) && iAUTH == $aid) {
        return TRUE;
    }

    return FALSE;
}
