<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://phpfusion.com/
+--------------------------------------------------------+
| Filename: content_generator.php
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
require_once '../../maincore.php';
require_once THEMES.'templates/admin_header.php';

pageaccess('CG');

class ContentGenerator {
    private $locale;
    private $snippet;
    private $body;
    private $short_text;
    private $shout_text;
    private $message_text;
    private $users;

    public function __construct() {
        $this->locale = fusion_get_locale('', CG_LOCALE);

        $this->snippet = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum aliquam felis nunc, in dignissim metus suscipit eget. Nunc scelerisque laoreet purus, in ullamcorper magna sagittis eget. Aliquam ac rhoncus orci, a lacinia ante. Integer sed erat ligula. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Fusce ullamcorper sapien mauris, et tempus mi tincidunt laoreet. Proin aliquam vulputate felis in viverra.';
        $this->body = $this->snippet."\n<p>Duis sed lorem vitae nibh sagittis tempus sed sed enim. Mauris egestas varius purus, a varius odio vehicula quis. Donec cursus interdum libero, et ornare tellus mattis vitae. Phasellus et ligula velit. Vivamus ac turpis dictum, congue metus facilisis, ultrices lorem. Cras imperdiet lacus in tincidunt pellentesque. Sed consectetur nunc vitae fringilla volutpat. Mauris nibh justo, luctus eu dapibus in, pellentesque non urna. Nulla ullamcorper varius lacus, ut finibus eros interdum id. Proin at pellentesque sapien. Integer imperdiet, sapien nec tristique laoreet, sapien lacus porta nunc, tincidunt cursus risus mauris id quam.</p>";
        $this->short_text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tempor aliquam nulla eu dapibus. Donec pulvinar porttitor urna, in ultrices dolor cursus et. Quisque vitae eros imperdiet, dictum orci lacinia, scelerisque est.';
        $this->shout_text = [
            1 => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. :D',
            2 => 'Aliquam ac rhoncus orci, a lacinia ante.',
            3 => 'Mauris nibh justo, luctus eu dapibus in, pellentesque non urna. Nulla ullamcorper varius lacus, ut finibus eros interdum id. :)',
            4 => 'Quisque vitae eros imperdiet, dictum orci lacinia, scelerisque est.',
            5 => 'Proin aliquam vulputate felis in viverra.'
        ];
        $this->message_text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam a tempus lectus, eu posuere ipsum. Etiam et odio laoreet quam cursus sollicitudin. Donec ac eros non mi lacinia volutpat quis ultrices odio.';

        $this->users = dbcount('(user_id)', DB_USERS, 'user_status = 0');
    }

    private function numField($id, $value = 20) {
        return form_text('num_'.$id, $this->locale['cg_001'], $value, [
            'type'        => 'number',
            'number_min'  => 1,
            'number_max'  => 2000,
            'inline'      => TRUE,
            'class'       => 'm-b-0',
            'inner_class' => 'input-sm'
        ]);
    }

    private function button($id, $delete = FALSE) {
        if ($delete == TRUE) {
            $button = form_button('delete_'.$id, $this->locale['delete'], $this->locale['delete'], ['class' => 'btn-sm btn-danger']);
        } else {
            $button = form_button('create_'.$id, $this->locale['cg_001'], $this->locale['cg_001'], ['class' => 'btn-sm btn-default']);
        }

        return $button;
    }

    private function randomName() {
        $length = 8;
        $name = '';
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max = count($characters) - 1;

        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $name .= $characters[$rand];
        }

        return $name;
    }

    private function randomIp() {
        $num1 = mt_rand(0, 255);
        $num2 = mt_rand(0, 255);
        $num3 = mt_rand(0, 255);
        $num4 = mt_rand(0, 255);

        return $num1.'.'.$num2.'.'.$num3.'.'.$num4;
    }

    private function notice($num, $delete = FALSE) {
        if ($delete == TRUE) {
            addnotice('success', $this->locale['cg_002']);
        } else {
            addnotice('success', $this->locale['cg_003'].' ('.$num.')');
        }

        redirect(FUSION_REQUEST);
    }

    private function query($table, $insert, $values) {
        dbquery("INSERT INTO  ".$table." (".$insert.") VALUES ".$values);
    }

    private function delete($table) {
        dbquery("TRUNCATE TABLE ".$table);
    }

    private function users() {
        $admin = !isset($_POST['create_admins']);
        $mailnames = ['gmail.com', 'hotmail.com', 'yahoo.com', 'outlook.com', 'yandex.com', 'protonmail.com', 'aol.com'];
        $password = '8a724b7684e0254527cf990012e93b6ec988e71a612419da0938a78e096c79be'; // test123456
        $salt = '2038a428a612fef1930f9cbfc34ac617931d9ac5';
        $passworda = '116c3754c28c691f4c7769487fd41a2f9e6b85a41034cc84533c9a2923267fd1'; // test123456789
        $admin_salt = $admin ? '' : '0d406b98c9e42c0223754fce4d8150a5f70f4d17';
        $user_level = $admin ? USER_LEVEL_MEMBER : USER_LEVEL_ADMIN;
        $admin_password = $admin ? '' : $passworda;
        $rights = 'A.BLOG.D.FQ.F.PH.IM.N.PO.W.B.C.M.UG.BB.SM.LANG.S2.S9.S';
        $rights = $admin ? '' : $rights;
        $algo = 'sha256';

        $query = "INSERT INTO ".DB_USERS." (user_name, user_algo, user_salt, user_password, user_admin_algo, user_admin_salt, user_admin_password, user_email, user_hide_email, user_joined, user_lastvisit, user_ip, user_ip_type, user_rights, user_level, user_threads, user_groups) VALUES ";

        if (isset($_POST['create_users']) || isset($_POST['create_admins'])) {
            $num_users = $_POST['num_users'];
            $num_admins = $_POST['num_admins'];
            $num = $admin ? $num_users : $num_admins;

            for ($i = 1; $i <= $num; $i++) {
                $username = $this->randomName();
                $ip = $this->randomIp();
                $mail = strtolower($username.'@'.$mailnames[rand(1, 6)]);
                $joined_rand = rand(0, (time() / 2));
                $joined = time() - $joined_rand;
                $lastvisit = time() - rand(0, $joined_rand);

                $query .= "('".$username."', '".$algo."', '".$salt."', '".$password."', '".$algo."', '".$admin_salt."', '".$admin_password."', '".$mail."', 0, '".$joined."', '".$lastvisit."', '".$ip."', 4, '".$rights."', '".$user_level."', '', '')";
                $query .= $i < $num ? ', ' : ';';
            }

            dbquery($query);

            $this->notice($num);
        }

        if (isset($_POST['delete_users'])) {
            dbquery("DELETE FROM ".DB_USERS." WHERE user_id != 1 AND user_level = ".USER_LEVEL_MEMBER."");
            $this->notice('', TRUE);
        }

        if (isset($_POST['delete_admins'])) {
            dbquery("DELETE FROM ".DB_USERS." WHERE user_id != 1 AND user_level = ".USER_LEVEL_ADMIN."");
            $this->notice('', TRUE);
        }
    }

    private function userGroups() {
        if (isset($_POST['create_user_groups'])) {
            $num = $_POST['num_user_groups'];
            $insert = 'group_name, group_description';
            $values = '';

            for ($i = 1; $i <= $num; $i++) {
                $values .= "('".$this->locale['cg_006']." ".$i."', '".$this->locale['cg_007']."')";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_USER_GROUPS, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['delete_user_groups'])) {
            $this->delete(DB_USER_GROUPS);
            $this->notice('', TRUE);
        }
    }

    private function privateMessages() {
        if (isset($_POST['create_private_messages'])) {
            $num = $_POST['num_private_messages'];
            for ($i = 1; $i <= $num; $i++) {
                send_pm(rand(1, $this->users / 2), rand($this->users / 2, $this->users), $this->locale['cg_041'].' '.$i, $this->message_text);
            }

            $this->notice($num);
        }

        if (isset($_POST['delete_private_messages'])) {
            $this->delete(DB_MESSAGES);
            $this->notice('', TRUE);
        }
    }

    private function articles() {
        if (isset($_POST['create_article_cats'])) {
            $num = $_POST['num_article_cats'];
            $insert = 'article_cat_parent, article_cat_name, article_cat_description, article_cat_visibility, article_cat_status, article_cat_language';
            $values = '';

            for ($i = 1; $i <= $num; $i++) {
                $values .= "(0, '".$this->locale['cg_009']." ".$i."', '".$this->locale['cg_007']."', 0, 1, '".LANGUAGE."')";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_ARTICLE_CATS, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['create_articles'])) {
            $num = $_POST['num_articles'];
            $insert = 'article_subject, article_cat, article_snippet, article_article, article_breaks, article_name, article_datestamp, article_reads, article_allow_comments, article_allow_ratings, article_language';
            $values = '';

            for ($i = 1; $i <= $num; $i++) {
                $article_cats = dbcount('(article_cat_id)', DB_ARTICLE_CATS);
                $article_cats = rand(1, $article_cats);
                $values .= "('".$this->locale['cg_010']." ".$i."', ".$article_cats.", '".$this->snippet."', '".$this->body."', 'y', '".rand(1, $this->users)."', '".(time() - rand(0, time() / 2))."', '".rand(1, 10000)."', 1, 1, '".LANGUAGE."')";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_ARTICLES, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['delete_article_cats'])) {
            $this->delete(DB_ARTICLE_CATS);
            $this->notice('', TRUE);
        }

        if (isset($_POST['delete_articles'])) {
            $this->delete(DB_ARTICLES);
            $this->notice('', TRUE);
        }
    }

    private function blog() {
        if (isset($_POST['create_blog_cats'])) {
            $num = $_POST['num_blog_cats'];
            $insert = 'blog_cat_parent, blog_cat_name, blog_cat_language';
            $values = '';

            for ($i = 1; $i <= $num; $i++) {
                $values .= "(0, '".$this->locale['cg_009']." ".$i."', '".LANGUAGE."')";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_BLOG_CATS, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['create_blogs'])) {
            $num = $_POST['num_blogs'];
            $insert = 'blog_subject, blog_cat, blog_blog, blog_extended, blog_breaks, blog_name, blog_datestamp, blog_reads, blog_allow_comments, blog_allow_ratings, blog_language';
            $values = '';

            for ($i = 1; $i <= $num; $i++) {
                $blog_cats = dbcount('(blog_cat_id)', DB_BLOG_CATS);
                $blog_cats = rand(1, $blog_cats);
                $values .= "('".$this->locale['cg_013']." ".$i."', ".$blog_cats.", '".$this->snippet."', '".$this->body."', 'y', '".rand(1, $this->users)."', '".(time() - rand(0, time() / 2))."', '".rand(1, 10000)."', 1, 1, '".LANGUAGE."')";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_BLOG, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['delete_blog_cats'])) {
            $this->delete(DB_BLOG_CATS);
            $this->notice('', TRUE);
        }

        if (isset($_POST['delete_blogs'])) {
            $this->delete(DB_BLOG);
            $this->notice('', TRUE);
        }
    }

    private function commentsAndRatings() {
        $addons = [];

        if (defined('ARTICLES_EXISTS') && defined('DB_ARTICLES')) {
            $count = dbcount('(article_id)', DB_ARTICLES);
            if ($count > 0) {
                $addons[] = ['type' => 'A', 'max' => $count];
            }
        }

        if (defined('BLOG_EXISTS') && defined('DB_BLOG')) {
            $count = dbcount('(blog_id)', DB_BLOG);
            if ($count > 0) {
                $addons[] = ['type' => 'B', 'max' => $count];
            }
        }

        if (defined('DOWNLOADS_EXISTS') && defined('DB_DOWNLOADS')) {
            $count = dbcount('(download_id)', DB_DOWNLOADS);
            if ($count > 0) {
                $addons[] = ['type' => 'D', 'max' => $count];
            }
        }

        if (defined('GALLERY_EXISTS') && defined('DB_PHOTO_ALBUMS')) {
            $count = dbcount('(album_id)', DB_PHOTO_ALBUMS);
            if ($count > 0) {
                $addons[] = ['type' => 'P', 'max' => $count];
            }
        }

        if (defined('NEWS_EXISTS') && defined('DB_NEWS')) {
            $count = dbcount('(news_id)', DB_NEWS);
            if ($count > 0) {
                $addons[] = ['type' => 'N', 'max' => $count];
            }
        }

        if (defined('VIDEOS_EXISTS') && defined('DB_VIDEOS')) {
            $count = dbcount('(video_id)', DB_VIDEOS);
            if ($count > 0) {
                $addons[] = ['type' => 'VID', 'max' => $count];
            }
        }

        shuffle($addons);

        if (isset($_POST['create_comments'])) {
            $num = $_POST['num_comments'];

            $insert = 'comment_item_id, comment_type, comment_name, comment_subject, comment_message, comment_datestamp, comment_ip, comment_hidden';

            $values = '';
            for ($i = 1; $i <= $num; $i++) {
                $type = $addons[array_rand($addons)];

                $values .= "('".rand(1, $type['max'])."', '".$type['type']."', '".rand(1, $this->users)."', '".$this->locale['cg_048']." ".$i."', '".$this->shout_text[rand(1, 5)]."', '".(time() - rand(0, time() / 2))."', '".$this->randomIp()."', 0)";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_COMMENTS, $insert, $values);
            $this->notice($num);
        }


        if (isset($_POST['delete_comments'])) {
            $this->delete(DB_COMMENTS);
            $this->notice('', TRUE);
        }

        if (isset($_POST['create_ratings'])) {
            $num = $_POST['num_ratings'];

            $insert = 'rating_item_id, rating_type, rating_user, rating_vote, rating_datestamp, rating_ip, rating_ip_type';

            $values = '';
            for ($i = 1; $i <= $num; $i++) {
                $type = $addons[array_rand($addons)];

                $values .= "('".rand(1, $type['max'])."', '".$type['type']."', '".rand(1, $this->users)."', '".rand(1, 5)."', '".(time() - rand(0, time() / 2))."', '".$this->randomIp()."', 4)";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_RATINGS, $insert, $values);
            $this->notice($num);
        }


        if (isset($_POST['delete_ratings'])) {
            $this->delete(DB_RATINGS);
            $this->notice('', TRUE);
        }
    }

    private function customPages() {
        if (isset($_POST['create_custom_pages'])) {
            $num = $_POST['num_custom_pages'];

            $insert = 'page_id, page_title, page_access, page_content, page_status, page_user, page_datestamp, page_language';
            $insert2 = 'page_id, page_grid_id, page_content_id, page_content_type, page_content, page_options, page_widget';
            $insert3 = 'page_id, page_grid_id, page_grid_column_count, page_grid_html_id, page_grid_class';

            $values = '';
            $values2 = '';
            $values3 = '';
            for ($i = 1; $i <= $num; $i++) {
                $values .= "(".$i.", '".$this->locale['cg_016']." ".$i."', 0, '".$this->body."', 1, 1, '".(time() - rand(0, time() / 2))."', '".LANGUAGE."')";
                $values .= $i < $num ? ', ' : ';';

                $values2 .= "(".$i.", ".$i.", ".$i.", 'content', '".$this->body."', '', '')";
                $values2 .= $i < $num ? ', ' : ';';

                $values3 .= "(".$i.", ".$i.", 1, ".$i.", '')";
                $values3 .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_CUSTOM_PAGES, $insert, $values);
            $this->query(DB_CUSTOM_PAGES_CONTENT, $insert2, $values2);
            $this->query(DB_CUSTOM_PAGES_GRID, $insert3, $values3);
            $this->notice($num);
        }


        if (isset($_POST['delete_custom_pages'])) {
            $this->delete(DB_CUSTOM_PAGES);
            $this->delete(DB_CUSTOM_PAGES_CONTENT);
            $this->delete(DB_CUSTOM_PAGES_GRID);
            $this->notice('', TRUE);
        }
    }

    private function downloads() {
        if (isset($_POST['create_download_cats'])) {
            $num = $_POST['num_download_cats'];
            $insert = 'download_cat_parent, download_cat_name, download_cat_description, download_cat_sorting, download_cat_language';
            $values = '';

            for ($i = 1; $i <= $num; $i++) {
                $values .= "(0, '".$this->locale['cg_009']." ".$i."', '".$this->locale['cg_007']."', 'download_id ASC', '".LANGUAGE."')";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_DOWNLOAD_CATS, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['create_downloads'])) {
            $num = $_POST['num_downloads'];
            $insert = 'download_user, download_title, download_description_short, download_description, download_url, download_cat, download_datestamp, download_visibility, download_count, download_allow_comments, download_allow_ratings';
            $values = '';

            for ($i = 1; $i <= $num; $i++) {
                $download_cats = dbcount('(download_cat_id)', DB_DOWNLOAD_CATS);
                $download_cats = rand(1, $download_cats);
                $values .= "('".rand(1, $this->users)."', '".$this->locale['cg_018']." ".$i."', '".$this->short_text."', '".$this->body."', 'https://phpfusion.com/home.php', ".$download_cats.", '".(time() - rand(0, time() / 2))."', 0, ".rand(1, 10000).", 1, 1)";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_DOWNLOADS, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['delete_download_cats'])) {
            $this->delete(DB_DOWNLOAD_CATS);
            $this->notice('', TRUE);
        }

        if (isset($_POST['delete_downloads'])) {
            $this->delete(DB_DOWNLOADS);
            $this->notice('', TRUE);
        }
    }

    private function faq() {
        if (isset($_POST['create_faq_cats'])) {
            $num = $_POST['num_faq_cats'];
            $insert = 'faq_cat_name, faq_cat_description, faq_cat_language';
            $values = '';

            for ($i = 1; $i <= $num; $i++) {
                $values .= "('".$this->locale['cg_009']." ".$i."', '".$this->locale['cg_007']."', '".LANGUAGE."')";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_FAQ_CATS, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['create_faqs'])) {
            $num = $_POST['num_faqs'];
            $insert = 'faq_cat_id, faq_question, faq_answer, faq_breaks, faq_name, faq_datestamp, faq_visibility, faq_status, faq_language';
            $values = '';

            for ($i = 1; $i <= $num; $i++) {
                $faq_cats = dbcount('(faq_cat_id)', DB_FAQ_CATS);
                $faq_cats = rand(1, $faq_cats);
                $values .= "(".$faq_cats.", '".$this->locale['cg_021']." ".$i."', '".$this->short_text."', 'y', '".rand(1, $this->users)."', '".(time() - rand(0, time() / 2))."', 0, 1, '".LANGUAGE."')";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_FAQS, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['delete_faq_cats'])) {
            $this->delete(DB_FAQ_CATS);
            $this->notice('', TRUE);
        }

        if (isset($_POST['delete_faqs'])) {
            $this->delete(DB_FAQS);
            $this->notice('', TRUE);
        }
    }

    private function forum() {
        if (isset($_POST['create_forums'])) {
            $num = $_POST['num_forums'];
            $insert = 'forum_name, forum_type, forum_description, forum_post, forum_reply, forum_language, forum_rules, forum_mods, forum_meta';
            $values = '';

            for ($i = 1; $i <= $num; $i++) {
                $type = rand(1, 4);
                $values .= "('".$this->locale['cg_044']." ".$i."', '".$type."', '".$this->locale['cg_007']."', '".USER_LEVEL_MEMBER."', '".USER_LEVEL_MEMBER."', '".LANGUAGE."', '', '', '')";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_FORUMS, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['delete_forums'])) {
            $this->delete(DB_FORUMS);
            $this->notice('', TRUE);
        }
    }

    private function gallery() {
        if (isset($_POST['create_photo_albums'])) {
            $num = $_POST['num_photo_albums'];
            $insert = 'album_title, album_description, album_user, album_datestamp, album_language';
            $values = '';

            for ($i = 1; $i <= $num; $i++) {
                $values .= "('".$this->locale['cg_046']." ".$i."', '".$this->locale['cg_007']."', '".rand(1, $this->users)."', '".(time() - rand(0, time() / 2))."', '".LANGUAGE."')";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_PHOTO_ALBUMS, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['delete_photo_albums'])) {
            $this->delete(DB_PHOTO_ALBUMS);
            $this->notice('', TRUE);
        }
    }

    private function news() {
        if (isset($_POST['create_news_cats'])) {
            $num = $_POST['num_news_cats'];
            $insert = 'news_cat_parent, news_cat_name, news_cat_visibility, news_cat_language';
            $values = '';

            for ($i = 1; $i <= $num; $i++) {
                $values .= "(0, '".$this->locale['cg_009']." ".$i."', 0, '".LANGUAGE."')";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_NEWS_CATS, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['create_news'])) {
            $num = $_POST['num_news'];
            $insert = 'news_subject, news_cat, news_news, news_extended, news_breaks, news_name, news_datestamp, news_visibility, news_reads, news_allow_comments, news_allow_ratings, news_language';
            $values = '';

            for ($i = 1; $i <= $num; $i++) {
                $news_cats = dbcount('(news_cat_id)', DB_NEWS_CATS);
                $news_cats = rand(1, $news_cats);
                $values .= "('".$this->locale['cg_024']." ".$i."', ".$news_cats.", '".$this->snippet."', '".$this->body."', 'y', '".rand(1, $this->users)."', '".(time() - rand(0, time() / 2))."', 0, ".rand(1, 10000).", 1, 1, '".LANGUAGE."')";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_NEWS, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['delete_news_cats'])) {
            $this->delete(DB_NEWS_CATS);
            $this->notice('', TRUE);
        }

        if (isset($_POST['delete_news'])) {
            $this->delete(DB_NEWS);
            $this->notice('', TRUE);
        }
    }

    private function polls() {
        if (isset($_POST['create_polls'])) {
            $num = $_POST['num_polls'];
            $insert = 'poll_title, poll_opt, poll_started, poll_ended, poll_visibility';
            $values = '';

            for ($i = 1; $i <= $num; $i++) {
                $values .= "('".serialize([LANGUAGE => $this->locale['cg_027'].' '.$i])."', '".serialize([[LANGUAGE => $this->locale['cg_028']], [LANGUAGE => $this->locale['cg_029']]])."', '".(time() - rand(0, time() / 2))."', 0, 0)";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_POLLS, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['delete_polls'])) {
            $this->delete(DB_POLLS);
            $this->notice('', TRUE);
        }
    }

    private function shouts() {
        if (isset($_POST['create_shouts'])) {
            $num = $_POST['num_shouts'];
            $insert = 'shout_name, shout_message, shout_datestamp, shout_ip, shout_ip_type, shout_hidden, shout_language';
            $values = '';

            for ($i = 1; $i <= $num; $i++) {
                $values .= "('".rand(1, $this->users)."', '".$this->shout_text[rand(1, 5)]."', '".(time() - rand(0, time() / 2))."', '".$this->randomIp()."', 4, 0, '".LANGUAGE."')";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_SHOUTBOX, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['delete_shouts'])) {
            $this->delete(DB_SHOUTBOX);
            $this->notice('', TRUE);
        }
    }

    private function videos() {
        if (isset($_POST['create_video_cats']) && defined('DB_VIDEO_CATS')) {
            $num = $_POST['num_video_cats'];
            $insert = 'video_cat_parent, video_cat_name, video_cat_description, video_cat_sorting, video_cat_language';
            $values = '';

            for ($i = 1; $i <= $num; $i++) {
                $values .= "(0, '".$this->locale['cg_009']." ".$i."', '".$this->locale['cg_007']."', 'video_id ASC', '".LANGUAGE."')";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_VIDEO_CATS, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['create_videos']) && defined('DB_VIDEOS')) {
            $num = $_POST['num_videos'];
            $insert = 'video_cat, video_user, video_title, video_description, video_length, video_datestamp, video_visibility, video_type, video_url, video_views, video_allow_comments, video_allow_ratings';
            $values = '';

            $video_urls = [
                1 => 'https://www.youtube.com/watch?v=C0DPdy98e4c',
                2 => 'https://www.youtube.com/watch?v=xcJtL7QggTI',
                3 => 'https://www.youtube.com/watch?v=2MpUj-Aua48',
            ];

            for ($i = 1; $i <= $num; $i++) {
                $video_cats = dbcount('(video_cat_id)', DB_VIDEO_CATS);
                $video_cats = rand(1, $video_cats);
                $values .= "(".$video_cats.", '".rand(1, $this->users)."', '".$this->locale['cg_050']." ".$i."', '".$this->body."', '".rand(0, 60).":".rand(0, 60)."', '".(time() - rand(0, time() / 2))."', 0, 'youtube', '".$video_urls[rand(1, 3)]."', ".rand(1, 10000).", 1, 1)";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_VIDEOS, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['delete_video_cats'])) {
            $this->delete(DB_VIDEO_CATS);
            $this->notice('', TRUE);
        }

        if (isset($_POST['delete_videos'])) {
            $this->delete(DB_VIDEOS);
            $this->notice('', TRUE);
        }
    }

    private function webLinks() {
        if (isset($_POST['create_weblink_cats'])) {
            $num = $_POST['num_weblink_cats'];
            $insert = 'weblink_cat_parent, weblink_cat_name, weblink_cat_description, weblink_cat_status, weblink_cat_visibility, weblink_cat_language';
            $values = '';

            for ($i = 1; $i <= $num; $i++) {
                $values .= "(0, '".$this->locale['cg_009']." ".$i."', '".$this->locale['cg_007']."', 1, 0, '".LANGUAGE."')";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_WEBLINK_CATS, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['create_weblinks'])) {
            $num = $_POST['num_weblinks'];
            $insert = 'weblink_name, weblink_description, weblink_url, weblink_cat, weblink_datestamp, weblink_visibility, weblink_status, weblink_count, weblink_language';
            $values = '';

            for ($i = 1; $i <= $num; $i++) {
                $weblink_cats = dbcount('(weblink_cat_id)', DB_WEBLINK_CATS);
                $weblink_cats = rand(1, $weblink_cats);
                $values .= "('".$this->locale['cg_033']." ".$i."', '".$this->locale['cg_007']."', 'https://".strtolower($this->randomName()).".com', ".$weblink_cats.", '".(time() - rand(0, time() / 2))."', 0, 1, ".rand(1, 10000).", '".LANGUAGE."')";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_WEBLINKS, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['delete_weblink_cats'])) {
            $this->delete(DB_WEBLINK_CATS);
            $this->notice('', TRUE);
        }

        if (isset($_POST['delete_weblinks'])) {
            $this->delete(DB_WEBLINKS);
            $this->notice('', TRUE);
        }
    }

    private function siteLinks() {
        if (isset($_POST['create_sitelinks'])) {
            $num = $_POST['num_sitelinks'];
            $insert = 'link_cat, link_name, link_url, link_icon, link_visibility, link_position, link_status, link_window, link_order, link_language';
            $values = '';

            for ($i = 1; $i <= $num; $i++) {
                $name = strtolower($this->randomName());
                $values .= "(0, '".ucfirst($name)."', 'https://".$name.".com', '', 0, '".rand(1, 3)."', 1, 0, 0, '".LANGUAGE."')";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_SITE_LINKS, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['delete_sitelinks'])) {
            $this->delete(DB_SITE_LINKS);
            $this->notice('', TRUE);
        }
    }

    private function panels() {
        if (isset($_POST['create_panels'])) {
            $num = $_POST['num_panels'];
            $insert = 'panel_name, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_restriction, panel_languages, panel_url_list';
            $values = '';

            for ($i = 1; $i <= $num; $i++) {
                $name = strtolower($this->randomName());
                $values .= "('".ucfirst($name)."', '".$this->short_text."', '".rand(1, 10)."', 1, 'php', 0, 1, 1, '".rand(0, 3)."', '".LANGUAGE."', '')";
                $values .= $i < $num ? ', ' : ';';
            }

            $this->query(DB_PANELS, $insert, $values);
            $this->notice($num);
        }

        if (isset($_POST['delete_panels'])) {
            $this->delete(DB_PANELS);
            $this->notice('', TRUE);
        }
    }

    public function displayAdmin() {
        add_to_title($this->locale['cg_title']);

        add_breadcrumb([
            'link'  => INFUSIONS.'content_generator/content_generator.php'.fusion_get_aidlink(),
            'title' => $this->locale['cg_title']
        ]);

        opentable($this->locale['cg_title']);

        echo '<div class="well">';
        echo '<strong class="text-danger">'.$this->locale['cg_037'].'</strong><br />';
        echo $this->locale['cg_038'].'<br />';
        echo $this->locale['cg_039'].': <strong>test123456</strong><br />';
        echo $this->locale['cg_040'].': <strong>test123456789</strong>';
        echo '</div>';

        echo openform('content', 'post', FUSION_REQUEST);

        echo '<div class="table-responsive"><table class="table table-striped">';
        echo '<tbody>';
        $this->users();
        $total_users = dbcount('(user_id)', DB_USERS, 'user_status=0');
        echo '<tr><td colspan="4" class="info text-center strong">'.$this->locale['cg_042'].': '.$total_users.'</td></tr>';
        echo '<tr>';
        echo '<td>'.$this->numField('users', 50).'</td>';
        echo '<td>'.$this->button('users').'</td>';
        $users = dbcount('(user_id)', DB_USERS, 'user_status=0 AND user_level='.USER_LEVEL_MEMBER.'');
        echo '<td>'.$this->locale['cg_004'].': '.$users.'</td>';
        echo '<td>'.$this->button('users', TRUE).'</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td>'.$this->numField('admins', 5).'</td>';
        echo '<td>'.$this->button('admins').'</td>';
        $admins = dbcount('(user_id)', DB_USERS, 'user_status=0 AND user_level='.USER_LEVEL_ADMIN.' OR user_level='.USER_LEVEL_SUPER_ADMIN.'');
        echo '<td>'.$this->locale['cg_005'].': '.$admins.'</td>';
        echo '<td>'.$this->button('admins', TRUE).'</td>';
        echo '</tr>';

        echo '<tr>';
        $this->userGroups();
        echo '<td>'.$this->numField('user_groups', 5).'</td>';
        echo '<td>'.$this->button('user_groups').'</td>';
        $user_groups = dbcount('(group_id)', DB_USER_GROUPS);
        echo '<td>'.$this->locale['cg_008'].': '.$user_groups.'</td>';
        echo '<td>'.$this->button('user_groups', TRUE).'</td>';
        echo '</tr>';

        echo '<tr>';
        $this->privateMessages();
        echo '<td>'.$this->numField('private_messages', 50).'</td>';
        echo '<td>'.$this->button('private_messages').'</td>';
        $private_messages = dbcount('(message_id)', DB_MESSAGES) / 2;
        echo '<td>'.$this->locale['cg_041'].': '.$private_messages.'</td>';
        echo '<td>'.$this->button('private_messages', TRUE).'</td>';
        echo '</tr>';

        if (defined('ARTICLES_EXISTS')) {
            $this->articles();
            echo '<tr><td colspan="4" class="info text-center strong">'.$this->locale['cg_012'].'</td></tr>';
            echo '<tr>';
            echo '<td>'.$this->numField('article_cats', 5).'</td>';
            echo '<td>'.$this->button('article_cats').'</td>';
            $article_cats = dbcount('(article_cat_id)', DB_ARTICLE_CATS);
            echo '<td>'.$this->locale['cg_011'].': '.$article_cats.'</td>';
            echo '<td>'.$this->button('article_cats', TRUE).'</td>';
            echo '</tr>';
            if (!empty($article_cats)) {
                echo '<tr>';
                echo '<td>'.$this->numField('articles').'</td>';
                echo '<td>'.$this->button('articles').'</td>';
                $articles = dbcount('(article_id)', DB_ARTICLES);
                echo '<td>'.$this->locale['cg_012'].': '.$articles.'</td>';
                echo '<td>'.$this->button('articles', TRUE).'</td>';
                echo '</tr>';
            } else {
                echo '<tr><td colspan="4" class="warning text-center">'.sprintf($this->locale['cg_036'], $this->locale['cg_011']).'</td></tr>';
            }
        }

        if (defined('BLOG_EXISTS')) {
            $this->blog();
            echo '<tr><td colspan="4" class="info text-center strong">'.$this->locale['cg_015'].'</td></tr>';
            echo '<tr>';
            echo '<td>'.$this->numField('blog_cats', 5).'</td>';
            echo '<td>'.$this->button('blog_cats').'</td>';
            $blog_cats = dbcount('(blog_cat_id)', DB_BLOG_CATS);
            echo '<td>'.$this->locale['cg_014'].': '.$blog_cats.'</td>';
            echo '<td>'.$this->button('blog_cats', TRUE).'</td>';
            echo '</tr>';
            if (!empty($blog_cats)) {
                echo '<tr>';
                echo '<td>'.$this->numField('blogs').'</td>';
                echo '<td>'.$this->button('blogs').'</td>';
                $blogs = dbcount('(blog_id)', DB_BLOG);
                echo '<td>'.$this->locale['cg_015'].': '.$blogs.'</td>';
                echo '<td>'.$this->button('blogs', TRUE).'</td>';
                echo '</tr>';
            } else {
                echo '<tr><td colspan="4" class="warning text-center">'.sprintf($this->locale['cg_036'], $this->locale['cg_014']).'</td></tr>';
            }
        }

        echo '<tr><td colspan="4" class="info text-center strong">'.$this->locale['cg_017'].'</td></tr>';
        echo '<tr>';
        $this->customPages();
        echo '<td>'.$this->numField('custom_pages', 5).'</td>';
        echo '<td>'.$this->button('custom_pages').'</td>';
        $custom_pages = dbcount('(page_id)', DB_CUSTOM_PAGES);
        echo '<td>'.$this->locale['cg_017'].': '.$custom_pages.'</td>';
        echo '<td>'.$this->button('custom_pages', TRUE).'</td>';
        echo '</tr>';

        if (defined('DOWNLOADS_EXISTS')) {
            $this->downloads();
            echo '<tr><td colspan="4" class="info text-center strong">'.$this->locale['cg_020'].'</td></tr>';
            echo '<tr>';
            echo '<td>'.$this->numField('download_cats', 5).'</td>';
            echo '<td>'.$this->button('download_cats').'</td>';
            $download_cats = dbcount('(download_cat_id)', DB_DOWNLOAD_CATS);
            echo '<td>'.$this->locale['cg_019'].': '.$download_cats.'</td>';
            echo '<td>'.$this->button('download_cats', TRUE).'</td>';
            echo '</tr>';
            if (!empty($download_cats)) {
                echo '<tr>';
                echo '<td>'.$this->numField('downloads').'</td>';
                echo '<td>'.$this->button('downloads').'</td>';
                $downloads = dbcount('(download_id)', DB_DOWNLOADS);
                echo '<td>'.$this->locale['cg_020'].': '.$downloads.'</td>';
                echo '<td>'.$this->button('downloads', TRUE).'</td>';
                echo '</tr>';
            } else {
                echo '<tr><td colspan="4" class="warning text-center">'.sprintf($this->locale['cg_036'], $this->locale['cg_019']).'</td></tr>';
            }
        }

        if (defined('FAQ_EXISTS')) {
            $this->faq();
            echo '<tr><td colspan="4" class="info text-center strong">'.$this->locale['cg_023'].'</td></tr>';
            echo '<tr>';
            echo '<td>'.$this->numField('faq_cats', 5).'</td>';
            echo '<td>'.$this->button('faq_cats').'</td>';
            $faq_cats = dbcount('(faq_cat_id)', DB_FAQ_CATS);
            echo '<td>'.$this->locale['cg_022'].': '.$faq_cats.'</td>';
            echo '<td>'.$this->button('faq_cats', TRUE).'</td>';
            echo '</tr>';
            if (!empty($faq_cats)) {
                echo '<tr>';
                echo '<td>'.$this->numField('faqs').'</td>';
                echo '<td>'.$this->button('faqs').'</td>';
                $faqs = dbcount('(faq_id)', DB_FAQS);
                echo '<td>'.$this->locale['cg_023'].': '.$faqs.'</td>';
                echo '<td>'.$this->button('faqs', TRUE).'</td>';
                echo '</tr>';
            } else {
                echo '<tr><td colspan="4" class="warning text-center">'.sprintf($this->locale['cg_036'], $this->locale['cg_022']).'</td></tr>';
            }
        }

        if (defined('FORUM_EXISTS')) {
            $this->forum();
            echo '<tr><td colspan="4" class="info text-center strong">'.$this->locale['cg_043'].'</td></tr>';
            echo '<tr>';
            echo '<td>'.$this->numField('forums', 5).'</td>';
            echo '<td>'.$this->button('forums').'</td>';
            $forums = dbcount('(forum_id)', DB_FORUMS);
            echo '<td>'.$this->locale['cg_043'].': '.$forums.'</td>';
            echo '<td>'.$this->button('forums', TRUE).'</td>';
            echo '</tr>';
        }

        if (defined('GALLERY_EXISTS')) {
            $this->gallery();
            echo '<tr><td colspan="4" class="info text-center strong">'.$this->locale['cg_045'].'</td></tr>';
            echo '<tr>';
            echo '<td>'.$this->numField('photo_albums', 5).'</td>';
            echo '<td>'.$this->button('photo_albums').'</td>';
            $photo_albums = dbcount('(album_id)', DB_PHOTO_ALBUMS);
            echo '<td>'.$this->locale['cg_045'].': '.$photo_albums.'</td>';
            echo '<td>'.$this->button('photo_albums', TRUE).'</td>';
            echo '</tr>';
        }

        if (defined('NEWS_EXISTS')) {
            $this->news();
            echo '<tr><td colspan="4" class="info text-center strong">'.$this->locale['cg_026'].'</td></tr>';
            echo '<tr>';
            echo '<td>'.$this->numField('news_cats', 5).'</td>';
            echo '<td>'.$this->button('news_cats').'</td>';
            $news_cats = dbcount('(news_cat_id)', DB_NEWS_CATS);
            echo '<td>'.$this->locale['cg_025'].': '.$news_cats.'</td>';
            echo '<td>'.$this->button('news_cats', TRUE).'</td>';
            echo '</tr>';

            if (!empty($news_cats)) {
                echo '<tr>';
                echo '<td>'.$this->numField('news').'</td>';
                echo '<td>'.$this->button('news').'</td>';
                $news = dbcount('(news_id)', DB_NEWS);
                echo '<td>'.$this->locale['cg_026'].': '.$news.'</td>';
                echo '<td>'.$this->button('news', TRUE).'</td>';
                echo '</tr>';
            } else {
                echo '<tr><td colspan="4" class="warning text-center">'.sprintf($this->locale['cg_036'], $this->locale['cg_025']).'</td></tr>';
            }
        }

        if (defined('MEMBER_POLL_PANEL_EXISTS')) {
            $this->polls();
            echo '<tr><td colspan="4" class="info text-center strong">'.$this->locale['cg_030'].'</td></tr>';
            echo '<tr>';
            echo '<td>'.$this->numField('polls', 5).'</td>';
            echo '<td>'.$this->button('polls').'</td>';
            $polls = dbcount('(poll_id)', DB_POLLS);
            echo '<td>'.$this->locale['cg_030'].': '.$polls.'</td>';
            echo '<td>'.$this->button('polls', TRUE).'</td>';
            echo '</tr>';
        }

        if (defined('SHOUTBOX_PANEL_EXISTS')) {
            $this->shouts();
            echo '<tr><td colspan="4" class="info text-center strong">'.$this->locale['cg_032'].'</td></tr>';
            echo '<tr>';
            echo '<td>'.$this->numField('shouts').'</td>';
            echo '<td>'.$this->button('shouts').'</td>';
            $shouts = dbcount('(shout_id)', DB_SHOUTBOX);
            echo '<td>'.$this->locale['cg_031'].': '.$shouts.'</td>';
            echo '<td>'.$this->button('shouts', TRUE).'</td>';
            echo '</tr>';
        }

        if (defined('VIDEOS_EXISTS') && defined('DB_VIDEO_CATS')) {
            $this->videos();
            echo '<tr><td colspan="4" class="info text-center strong">'.$this->locale['cg_052'].'</td></tr>';
            echo '<tr>';
            echo '<td>'.$this->numField('video_cats', 5).'</td>';
            echo '<td>'.$this->button('video_cats').'</td>';
            $video_cats = dbcount('(video_cat_id)', DB_VIDEO_CATS);
            echo '<td>'.$this->locale['cg_051'].': '.$video_cats.'</td>';
            echo '<td>'.$this->button('video_cats', TRUE).'</td>';
            echo '</tr>';

            if (!empty($video_cats) && defined('DB_VIDEOS')) {
                echo '<tr>';
                echo '<td>'.$this->numField('videos').'</td>';
                echo '<td>'.$this->button('videos').'</td>';
                $videos = dbcount('(video_id)', DB_VIDEOS);
                echo '<td>'.$this->locale['cg_052'].': '.$videos.'</td>';
                echo '<td>'.$this->button('videos', TRUE).'</td>';
                echo '</tr>';
            } else {
                echo '<tr><td colspan="4" class="warning text-center">'.sprintf($this->locale['cg_036'], $this->locale['cg_051']).'</td></tr>';
            }
        }

        if (defined('WEBLINKS_EXISTS')) {
            $this->webLinks();
            echo '<tr><td colspan="4" class="info text-center strong">'.$this->locale['cg_035'].'</td></tr>';
            echo '<tr>';
            echo '<td>'.$this->numField('weblink_cats', 5).'</td>';
            echo '<td>'.$this->button('weblink_cats').'</td>';
            $weblink_cats = dbcount('(weblink_cat_id)', DB_WEBLINK_CATS);
            echo '<td>'.$this->locale['cg_034'].': '.$weblink_cats.'</td>';
            echo '<td>'.$this->button('weblink_cats', TRUE).'</td>';
            echo '</tr>';

            if (!empty($weblink_cats)) {
                echo '<tr>';
                echo '<td>'.$this->numField('weblinks').'</td>';
                echo '<td>'.$this->button('weblinks').'</td>';
                $weblinks = dbcount('(weblink_id)', DB_WEBLINKS);
                echo '<td>'.$this->locale['cg_035'].': '.$weblinks.'</td>';
                echo '<td>'.$this->button('weblinks', TRUE).'</td>';
                echo '</tr>';
            } else {
                echo '<tr><td colspan="4" class="warning text-center">'.sprintf($this->locale['cg_036'], $this->locale['cg_034']).'</td></tr>';
            }
        }

        echo '<tr><td colspan="4" class="info text-center strong">'.$this->locale['cg_047'].' & '.$this->locale['cg_049'].'</td></tr>';
        $this->commentsAndRatings();
        echo '<tr>';
        echo '<td>'.$this->numField('comments', 50).'</td>';
        echo '<td>'.$this->button('comments').'</td>';
        $comments = dbcount('(comment_id)', DB_COMMENTS);
        echo '<td>'.$this->locale['cg_047'].': '.$comments.'</td>';
        echo '<td>'.$this->button('comments', TRUE).'</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td>'.$this->numField('ratings', 50).'</td>';
        echo '<td>'.$this->button('ratings').'</td>';
        $ratings = dbcount('(rating_id)', DB_RATINGS);
        echo '<td>'.$this->locale['cg_049'].': '.$ratings.'</td>';
        echo '<td>'.$this->button('ratings', TRUE).'</td>';
        echo '</tr>';

        $this->siteLinks();
        echo '<tr><td colspan="4" class="info text-center strong">'.$this->locale['cg_053'].'</td></tr>';
        echo '<tr>';
        echo '<td>'.$this->numField('sitelinks', 10).'</td>';
        echo '<td>'.$this->button('sitelinks').'</td>';
        $sitelinks = dbcount('(link_id)', DB_SITE_LINKS);
        echo '<td>'.$this->locale['cg_053'].': '.$sitelinks.'</td>';
        echo '<td>'.$this->button('sitelinks', TRUE).'</td>';
        echo '</tr>';

        $this->panels();
        echo '<tr><td colspan="4" class="info text-center strong">'.$this->locale['cg_054'].'</td></tr>';
        echo '<tr>';
        echo '<td>'.$this->numField('panels', 10).'</td>';
        echo '<td>'.$this->button('panels').'</td>';
        $panels = dbcount('(panel_id)', DB_PANELS);
        echo '<td>'.$this->locale['cg_054'].': '.$panels.'</td>';
        echo '<td>'.$this->button('panels', TRUE).'</td>';
        echo '</tr>';

        echo '</tbody>';
        echo '</table></div>';

        echo closeform();
        closetable();
    }
}

$cc = new ContentGenerator();
$cc->displayAdmin();

require_once THEMES.'templates/footer.php';
