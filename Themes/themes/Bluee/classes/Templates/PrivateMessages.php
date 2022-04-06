<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: PrivateMessages.php
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
namespace Bluee\Templates;

use Bluee\Main;

class PrivateMessages {
    public static function displayInbox($info) {
        Main::hideAll(0);

        $locale = fusion_get_locale();

        if (get('folder') !== 'options') {
            $info['actions_form']['lockbtn'] = form_button('archive_pm', '<i class="fas fa-lock"></i>', 'archive_pm', [
                'alt'   => $locale['412'],
                'class' => 'btn-outline-secondary'
            ]);
            $info['actions_form']['unlockbtn'] = form_button('unarchive_pm', '<i class="fas fa-unlock"></i>', 'unarchive_pm', [
                'alt'   => $locale['413'],
                'class' => 'btn-outline-secondary'
            ]);
            $info['actions_form']['deletebtn'] = form_button('delete_pm', '<i class="fas fa-trash"></i>', 'delete_pm', [
                'alt'   => $locale['416'],
                'class' => 'btn-outline-danger'
            ]);
        }

        switch (get('folder')) {
            case 'options':
                $content = $info['options_form'];
                break;
            case 'inbox':
                $content = self::inbox($info);
                break;
            default:
                $content = self::inbox($info);
        }

        $context = [
            'locale'  => $locale,
            'info'    => $info,
            'get'     => ['msg_read' => get('msg_read'), 'folder' => $_GET['folder']],
            'if_form' => !isset($_GET['msg_send']) && (!empty($info['actions_form']) || isset($_GET['msg_read'])),
            'if_read' => isset($_GET['msg_read']) && isset($info['items'][$_GET['msg_read']]),
            'content' => $content
        ];

        return fusion_render(THEME.'twig/pm', 'index.twig', $context);
    }

    private static function inbox($info) {
        if (isset($_GET['msg_read']) && isset($info['items'][$_GET['msg_read']])) {
            $info['item'] = $info['items'][$_GET['msg_read']];

            $info['item']['avatar'] = display_avatar($info['item'], '40px', '', FALSE, 'rounded pull-left m-t-5 m-r-10');
            $info['item']['profile'] = profile_link($info['item']['user_id'], $info['item']['user_name'], $info['item']['user_status'], 'display-block');
            $info['item']['date'] = '<span>'.timer($info['item']['message_datestamp']).'</span>';
        }

        if (!empty($info['items'])) {
            foreach ($info['items'] as $message_id => $data) {
                $info['items'][$message_id]['checkbox'] = form_checkbox('pmID', '', '', [
                    'input_id' => 'pmID-'.$message_id,
                    'value'    => $message_id,
                    'class'    => 'select-msg m-b-0'
                ]);
                $info['items'][$message_id]['text'] = trim_text($data['message_message'], 80);
                $info['items'][$message_id]['date'] = timer($data['message_datestamp']);
            }
        }

        $context = [
            'locale' => fusion_get_locale(),
            'info'   => $info,
            'get'    => ['msg_send' => get('msg_send')],
            'if_msg' => isset($_GET['msg_read']) && isset($info['items'][$_GET['msg_read']]),
        ];

        return fusion_render(THEME.'twig/pm', 'inbox.twig', $context);
    }
}
