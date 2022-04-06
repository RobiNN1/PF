<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: add_settimgs_v8.php
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
require_once __DIR__.'/maincore.php';
require_once THEMES.'templates/header.php';

include "locale/English/setup.php";

function strleft($s1, $s2) {
    return substr($s1, 0, strpos($s1, $s2));
}

function getCurrentURL() {
    $s = (empty($_SERVER["HTTPS"]) ? "" : ($_SERVER["HTTPS"] == "on")) ? "s" : "";
    $protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
    $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
    return $protocol."://".$_SERVER['SERVER_NAME'].$port.(str_replace(basename(cleanurl($_SERVER['PHP_SELF'])), "", $_SERVER['REQUEST_URI']));
}

$siteurl = getCurrentURL();
$url = parse_url($siteurl);

$insert_settings_tbl = [
    'sitename'                    => 'PHPFusion Powered Website',
    'siteurl'                     => $siteurl,
    'site_protocol'               => $url['scheme'],
    'site_host'                   => $url['host'],
    'site_port'                   => (isset($url['port']) ? $url['port'] : ""),
    'site_path'                   => (isset($url['path']) ? $url['path'] : ""),
    'site_seo'                    => '0',
    'normalize_seo'               => '0',
    'debug_seo'                   => '0',
    'gateway'                     => '1',
    'gateway_method'              => '1',
    'sitebanner'                  => 'images/php-fusion-logo.png',
    'sitebanner1'                 => '',
    'sitebanner2'                 => '',
    'siteemail'                   => 'email@email.com',
    'siteusername'                => 'Username',
    'siteintro'                   => "<div style=\'text-align:center\'>".$locale['230']."</div>",
    'description'                 => '',
    'keywords'                    => '',
    'footer'                      => "<div style=\'text-align:center\'>Copyright &copy; ".@date("Y")."</div>",
    'opening_page'                => 'news.php',
    'locale'                      => 'English',
    'bootstrap'                   => '0',
    'entypo'                      => '0',
    'fontawesome'                 => '0',
    'theme'                       => 'Atom-X8',
    'admin_theme'                 => 'Ares',
    'default_search'              => 'all',
    'exclude_left'                => '',
    'exclude_upper'               => '',
    'exclude_lower'               => '',
    'exclude_aupper'              => '',
    'exclude_blower'              => '',
    'exclude_right'               => '',
    'shortdate'                   => $locale['shortdate'],
    'longdate'                    => $locale['longdate'],
    'forumdate'                   => $locale['forumdate'],
    'newsdate'                    => $locale['newsdate'],
    'subheaderdate'               => $locale['subheaderdate'],
    'timeoffset'                  => '0.0',
    'serveroffset'                => '0.0',
    'numofthreads'                => '15',
    'forum_ips'                   => '0',
    'attachmax'                   => '15000000',
    'attachmax_count'             => '5',
    'attachtypes'                 => '.gif,.jpg,.png,.zip,.rar,.tar,.7z',
    'thread_notify'               => '1',
    'forum_ranks'                 => '1',
    'forum_edit_lock'             => '0',
    'forum_edit_timelimit'        => '0',
    'forum_editpost_to_lastpost'  => '1',
    'forum_last_posts_reply'      => '10',
    'forum_last_post_avatar'      => '1',
    'enable_registration'         => '1',
    'email_verification'          => '1',
    'admin_activation'            => '0',
    'display_validation'          => '1',
    'enable_deactivation'         => '0',
    'deactivation_period'         => '365',
    'deactivation_response'       => '14',
    'enable_terms'                => '0',
    'license_agreement'           => '',
    'license_lastupdate'          => '0',
    'thumb_w'                     => '150',
    'thumb_h'                     => '150',
    'photo_w'                     => '600',
    'photo_h'                     => '400',
    'photo_max_w'                 => '2800',
    'photo_max_h'                 => '2600',
    'photo_max_b'                 => '15000000',
    'thumb_compression'           => 'gd2',
    'thumbs_per_row'              => '4',
    'thumbs_per_page'             => '12',
    'photo_watermark'             => '1',
    'photo_watermark_image'       => 'images/watermark.png',
    'photo_watermark_text'        => '0',
    'photo_watermark_text_color1' => 'FF6600',
    'photo_watermark_text_color2' => 'FFFF00',
    'photo_watermark_text_color3' => 'FFFFFF',
    'photo_watermark_save'        => '0',
    'tinymce_enabled'             => '0',
    'smtp_host'                   => '',
    'smtp_port'                   => '25',
    'smtp_username'               => '',
    'smtp_password'               => '',
    'bad_words_enabled'           => '1',
    'bad_words'                   => '',
    'bad_word_replace'            => '****',
    'login_method'                => '0',
    'guestposts'                  => '0',
    'comments_enabled'            => '1',
    'ratings_enabled'             => '1',
    'hide_userprofiles'           => '0',
    'userthemes'                  => '1',
    'newsperpage'                 => '11',
    'flood_interval'              => '15',
    'counter'                     => '0',
    'version'                     => '8.00.80',
    'maintenance'                 => '0',
    'maintenance_message'         => '',
    'download_max_b'              => '15000000',
    'download_types'              => '.pdf,.gif,.jpg,.png,.zip,.rar,.tar,.bz2,.7z',
    'articles_per_page'           => '15',
    'downloads_per_page'          => '15',
    'links_per_page'              => '15',
    'comments_per_page'           => '10',
    'posts_per_page'              => '20',
    'threads_per_page'            => '20',
    'comments_sorting'            => 'ASC',
    'comments_avatar'             => '1',
    'avatar_width'                => '250',
    'avatar_height'               => '250',
    'avatar_filesize'             => '1550000',
    'avatar_ratio'                => '0',
    'cronjob_day'                 => time(),
    'cronjob_hour'                => time(),
    'flood_autoban'               => '1',
    'visitorcounter_enabled'      => '1',
    'rendertime_enabled'          => '0',
    'popular_threads_timeframe'   => '',
    'maintenance_level'           => '102',
    'news_photo_w'                => '400',
    'news_photo_h'                => '300',
    'news_image_frontpage'        => '0',
    'news_image_readmore'         => '0',
    'news_thumb_ratio'            => '0',
    'news_image_link'             => '1',
    'news_thumb_w'                => '100',
    'news_thumb_h'                => '100',
    'news_photo_max_w'            => '4800',
    'news_photo_max_h'            => '4600',
    'news_photo_max_b'            => '15000000',
    'blog_image_readmore'         => '0',
    'blog_image_frontpage'        => '0',
    'blog_thumb_ratio'            => '0',
    'blog_image_link'             => '1',
    'blog_photo_w'                => '400',
    'blog_photo_h'                => '300',
    'blog_thumb_w'                => '100',
    'blog_thumb_h'                => '100',
    'blog_photo_max_w'            => '4800',
    'blog_photo_max_h'            => '4600',
    'blog_photo_max_b'            => '15000000',
    'blogperpage'                 => '12',
    'deactivation_action'         => '0',
    'captcha'                     => 'securimage3',
    'password_algorithm'          => 'sha256',
    'default_timezone'            => 'Europe/London',
    'userNameChange'              => '1',
    'download_screen_max_b'       => '9990000',
    'download_screen_max_w'       => '4800',
    'download_screen_max_h'       => '4600',
    'recaptcha_public'            => '',
    'recaptcha_private'           => '',
    'recaptcha_theme'             => 'light',
    'download_screenshot'         => '1',
    'download_thumb_max_w'        => '200',
    'download_thumb_max_h'        => '200',
    'multiple_logins'             => '0',
    'smtp_auth'                   => '0',
    'mime_check'                  => '0',
    'enabled_languages'           => 'English',
    'number_delimiter'            => '.',
    'thousands_separator'         => ',',
    'allow_php_exe'               => '0',
    'update_checker'              => '1',
];

$added = '';
foreach ($insert_settings_tbl as $key => $value) {
    if (!isset($settings[$key])) {
        $result = dbquery("INSERT INTO ".DB_PREFIX."settings (settings_name, settings_value) VALUES ('$key', '$value')");
        $added .= 'Added '.$key.'<br>';
    }
}

echo $added;
require_once THEMES.'templates/footer.php';
