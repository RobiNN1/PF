<?php
/**
 * @author RobiNN
 */
require_once __DIR__.'/../maincore.php';
require_once THEMES.'templates/header.php';
require_once __DIR__.'/classes/Filesystem.php';
require_once __DIR__.'/classes/AddonGenerator.php';

add_to_title('Addon Generator');

$gen = new AddonGenerator();

if (post('create')) {
    $name = normalize(post('name'));
    $folder_name = '';

    if (post('type')) {
        switch (post('type')) {
            case 'panel':
                $gen->setRootPath(__DIR__.'/boilerplates/panel/');
                $folder_name = str_replace(' ', '_', strtolower($name));

                if (!empty($folder_name) && stringEndsWith($folder_name, '_panel') == 0) {
                    $folder_name = $folder_name.'_panel';
                }
                break;
            case 'infusion':
                $gen->setRootPath(__DIR__.'/boilerplates/infusion/');
                $folder_name = str_replace(' ', '_', strtolower($name));
                break;
            case 'theme':
                $gen->setRootPath(__DIR__.'/boilerplates/theme/');
                $folder_name = str_replace(' ', '_', $name);
                break;
            case 'admin_theme':
                $gen->setRootPath(__DIR__.'/boilerplates/admin_theme/');

                $folder_name = str_replace(' ', '_', $name);
                break;
        }
    }

    $rights = post('rights');
    $rights = strtoupper(strlen($rights) <= 4 ? $rights : substr($rights, 0, 4));
    $length = [2, 3, 4];
    $rights = !empty($rights) ? $rights : substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length[array_rand($length)]);

    $locale_prefix = strtolower(strlen($rights) <= 4 ? $rights : substr($rights, 0, 4));

    $gen->setReplace([
        'folder_name'   => form_sanitizer($folder_name, '', 'name'),
        'INF_EXIST'     => form_sanitizer(strtoupper($folder_name), '', 'name'),
        'ADDON_NAME'    => form_sanitizer($folder_name, '', 'name'),
        'YOUR_NAME'     => sanitizer('author', '', 'author'),
        'YOUR_EMAIL'    => sanitizer('email', '', 'email'),
        'YOUR_WEBSITE'  => sanitizer('website', '', 'website'),
        'ADMIN_RIGHTS'  => form_sanitizer($rights, '', 'rights'),
        'LOCALE_PREFIX' => form_sanitizer($locale_prefix, '', 'rights'),
    ]);

    if (Defender::safe()) {
        $gen->createZipPack();
        redirect(FUSION_SELF.'?file='.$gen->folder_name);
    }
}

if (get('file')) {
    $gen->downloadZip(get('file'));
}

function stringEndsWith($whole, $end) {
    return (strpos($whole, $end, strlen($whole) - strlen($end)) !== FALSE);
}

echo '<h1 class="main-title">Addon Generator for v9</h1>';
echo '<div class="mb-5">';

echo openform('generator', 'post', FUSION_SELF);
echo '<div class="row">';
echo '<div class="col-12 col-sm-6">';
echo form_select('type', 'Type', 'infusion', [
    'options'          => [
        'panel'       => 'Panel',
        'infusion'    => 'Infusion',
        'theme'       => 'Theme',
        'admin_theme' => 'Admin Theme'
    ],
    'select2_disabled' => TRUE,
    'inner_width'      => '100%'
]);

echo form_text('name', 'Addon Name', '', ['required' => TRUE, 'error_text' => 'Please enter a addon name']);
echo form_text('author', 'Author Name', (iMEMBER ? fusion_get_userdata('user_name') : ''), [
    'required'   => TRUE,
    'error_text' => 'Please enter a author name'
]);
echo '</div>';
echo '<div class="col-12 col-sm-6">';
echo form_text('email', 'Author Email (Optional)');
echo form_text('website', 'Website (Optional)');
echo form_text('rights', 'Admin Rights <span class="required">*</span>', '', [
    'placeholder' => 'Max. 4 characters, e.g. XX'
]);
echo '</div>';
echo '</div>';

echo form_button('create', 'Download', 'create', ['class' => 'btn-primary', 'icon' => 'fal fa-download']);
echo closeform();

echo '</div>';

// Panel && Admin Theme
add_to_jquery("
    $('#type').bind('change', function() {
        var val = $(this).val();
        if (val == 'panel' || val == 'admin_theme') {
            $('#email-field').hide();
            $('#website-field').hide();
            $('#rights-field').hide();
        } else {
            $('#email-field').show();
            $('#website-field').show();
            $('#rights-field').show();
        }
    });
");

// Theme
add_to_jquery("
    $('#type').bind('change', function() {
        var val = $(this).val();
        if (val == 'theme') {
            $('#email-field').hide();
            $('#rights-field').hide();
        }
    });
");

// Delete cache files older than two weeks
$files = glob(dirname(__FILE__).'/tmp/*.zip');
$now = time();

if ($files) {
    foreach ($files as $file) {
        if (is_file($file)) {
            if ($now - filemtime($file) >= 1209600) {
                unlink($file);
            }
        }
    }
}

require_once THEMES.'templates/footer.php';
