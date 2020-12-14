<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: github.php
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

use Github\Client;

require_once '../../maincore.php';
require_once THEMES.'templates/admin_header.php';

/**
 * GitHub API Documentation
 * @link https://github.com/KnpLabs/php-github-api/tree/2.x/doc
 * Markdown parser
 * @link https://github.com/erusev/parsedown
 */

pageAccess('GH');

$locale = fusion_get_locale('', GH_LOCALE);
$gh_settings = get_settings('github');
$userdata = fusion_get_userdata();

add_to_title($locale['gh_title']);

add_breadcrumb(['link' => INFUSIONS.'github/github.php'.fusion_get_aidlink(), 'title' => $locale['gh_title']]);

opentable($locale['gh_title'].'<br><small class="m-b-20" style="font-size: 13px;">'.$locale['gh_001'].'</small>');

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => [
            'User-Agent: PHP-Fusion'
        ]
    ]
]);
$github_json = file_get_contents('https://api.github.com/rate_limit', FALSE, $context);
$github_api = json_decode($github_json);

$rate_limit = $github_api->rate->remaining;
$rate_limit_reset = $github_api->rate->reset;

// Only for development purposes
// echo 'Your max. Rate Limit is: <b>'.$github_api->rate->limit.'</b>. Actual limit is: <b>'.$rate_limit.'</b>. Will be restored in '.showdate('%d.%m.%Y %H:%M:%S', $rate_limit_reset);

if ($rate_limit > 2) {
    require_once INFUSIONS.'github/includes/vendor/autoload.php';

    $filesystemAdapter = new \League\Flysystem\Adapter\Local(__DIR__.'/');
    $filesystem        = new \League\Flysystem\Filesystem($filesystemAdapter);
    $pool              = new \Cache\Adapter\Filesystem\FilesystemCachePool($filesystem);

    $client = new \Github\Client();
    if (!empty($userdata['user_github_access_token'])) {
        $client->authenticate($userdata['user_github_access_token'], NULL, Client::AUTH_ACCESS_TOKEN);
    }

    $client->addCache($pool);

    $owner = $gh_settings['owner'];
    $user = $client->api('user')->show($owner);
    $type = $user['type'] == 'Organization' ? 'organization' : 'uset';
    $repos = $client->api($type)->repositories($owner);
    $repositories = [];

    foreach ($repos as $key => $repo) {
        $repositories[] .= $repo['name'];
    }

    $settings_tab = '';
    $repos_tab = '';

    if (isset($_GET['tab']) && $_GET['tab'] == 'settings') {
        add_breadcrumb(['link' => INFUSIONS.'github/github.php'.fusion_get_aidlink(), 'title' => $locale['gh_002']]);
        ob_start();

        if (isset($_POST['save_settings'])) {
            $settings = [
                'owner' => form_sanitizer($_POST['owner'], '', 'owner')
            ];

            if (\defender::safe()) {
                foreach ($settings as $settings_name => $settings_value) {
                    $db = [
                        'settings_name'  => $settings_name,
                        'settings_value' => $settings_value,
                        'settings_inf'   => 'github'
                    ];

                    dbquery_insert(DB_SETTINGS_INF, $db, 'update', ['primary_key' => 'settings_name']);
                }

                addNotice('success', $locale['gh_003']);
                redirect(FUSION_REQUEST);
            }
        }

        if (isset($_POST['add_access_token'])) {
            $token = form_sanitizer($_POST['access_token'], '', 'access_token');

            dbquery("UPDATE ".DB_USERS." SET user_github_access_token=:access_token WHERE user_id=:user_id", [
                ':access_token' => $token,
                ':user_id'      => $userdata['user_id']
            ]);

            addNotice('success', $locale['gh_004']);
            redirect(FUSION_REQUEST);
        }

        openside('');
        echo openform('gh_settings', 'post', FUSION_REQUEST);
        echo form_text('owner', $locale['gh_005'], $gh_settings['owner'], ['inline' => TRUE, 'ext_tip' => $locale['gh_006']]);
        echo form_button('save_settings', $locale['save'], $locale['save'], ['class' => 'btn-success', 'icon' => 'fa fa-hdd-o']);
        echo closeform();
        closeside();

        openside('');
        echo openform('access_tokenform', 'post', FUSION_REQUEST);
        echo form_text('access_token', $locale['gh_007'], $userdata['user_github_access_token'], [
            'inline'  => TRUE,
            'ext_tip' => $locale['gh_008'].' <a target="_blank" href="https://github.com/settings/tokens/new">https://github.com/settings/tokens/new</a><br>'.
                         $locale['gh_009'].': PHP-Fusion GitHub Client<br>'.
                         $locale['gh_010'].' <b>repo</b>, <b>write:discussion</b>'
        ]);
        echo form_button('add_access_token', $locale['save'], $locale['save'], ['class' => 'btn-success', 'icon' => 'fa fa-hdd-o']);
        echo closeform();
        closeside();

        $settings_tab = ob_get_contents();
        ob_end_clean();
    } else {
        ob_start();
        $repo_name           = !empty($_GET['repo']) ? $_GET['repo'] : $repositories[0];
        $repo                = $client->api('repo')->show($owner, $repo_name);
        $branches            = $client->api('repo')->branches($owner, $repo_name);
        $current_branch      = isset($_GET['branch']) ? $_GET['branch'] : $repo['default_branch'];
        $commits             = $client->api('repo')->commits()->all($owner, $repo_name, ['sha' => $current_branch]);
        $contributors        = $client->api('repo')->contributors($owner, $repo_name, TRUE);
        $issues_state        = isset($_GET['issues']) ? $_GET['issues'] : 'open';
        $issues              = $client->api('issue')->all($owner, $repo_name, ['state' => $issues_state]);
        $pull_requests_state = isset($_GET['pull_requests']) ? $_GET['pull_requests'] : 'open';
        $pull_requests       = $client->api('pull_requests')->all($owner, $repo_name, ['state' => $pull_requests_state]);

        add_breadcrumb(['link' => INFUSIONS.'github/github.php'.fusion_get_aidlink().'&tab=repos&repo='.$repo_name, 'title' => $locale['gh_011'].$repo_name]);

        echo '<h4>'.$locale['gh_011'].$repo_name.'</h4>';

        echo '<div class="m-b-10">';
            echo '<div class="dropdown display-inline m-r-10">';
                echo '<button class="btn btn-default dropdown-toggle" type="button" id="'.$repo['full_name'].'-branhces" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$locale['gh_012'].(isset($_GET['branch']) ? $_GET['branch'] : $repo['default_branch']).' <span class="caret"></span></button>';
                echo '<ul class="dropdown-menu" aria-labelledby="'.$repo['full_name'].'-branhces">';
                    foreach ($branches as $key => $branch) {
                        $active_branch = (!isset($_GET['branch']) && $branch['name'] == $repo['default_branch']) || (isset($_GET['branch']) && $_GET['branch'] == $branch['name']) ? ' class="active"' : '';
                        echo '<li'.$active_branch.'><a href="'.INFUSIONS.'github/github.php'.fusion_get_aidlink().'&tab=repos&repo='.$repo_name.'&branch='.$branch['name'].'">'.$branch['name'].'</a></li>';
                    }
                echo '</ul>';
            echo '</div>';

            echo '<a class="btn btn-default m-r-10" target="_blank" href="https://github.com/'.$repo['full_name'].'/watchers"><i class="fa fa-fw fa-eye"></i> '.$locale['gh_013'].$repo['subscribers_count'].'</a>';
            echo '<a class="btn btn-default m-r-10" target="_blank" href="https://github.com/'.$repo['full_name'].'/stargazers"><i class="fa fa-fw fa-star"></i> '.$locale['gh_014'].$repo['stargazers_count'].'</a>';
            echo '<a class="btn btn-default m-r-10" target="_blank" href="https://github.com/'.$repo['full_name'].'/network/members"><i class="fa fa-fw fa-code-fork"></i> '.$locale['gh_015'].$repo['forks_count'].'</a>';
            echo '<a class="btn btn-success pull-right" href="https://github.com/'.$repo['full_name'].'/archive/'.$current_branch.'.zip"><i class="fa fa-fw fa-download"></i> '.$locale['gh_016'].'</a>';
        echo '</div>';

        $allowed_section = ['commits', 'contributors', 'issues', 'pull_requests'];
        $_GET['section'] = isset($_GET['section']) && in_array($_GET['section'], $allowed_section) ? $_GET['section'] : 'commits';

        $tab['title'][] = $locale['gh_017'];
        $tab['id'][]    = 'commits';
        $tab['icon'][]  = 'fa fa-clock';

        $tab['title'][] = $locale['gh_018'];
        $tab['id'][]    = 'contributors';
        $tab['icon'][]  = 'fa fa-users';

        if ($repo['has_issues'] == 1) {
            $tab['title'][] = $locale['gh_019'].' <span class="badge">'.$repo['open_issues_count'].'</span>';
            $tab['id'][] = 'issues';
            $tab['icon'][] = 'fa fa-exclamation-circle';
        }

        $pull_requests_open  = count($client->api('pull_requests')->all($owner, $repo_name, ['state' => 'open']));
        $tab['title'][] = $locale['gh_020'].' <span class="badge">'.$pull_requests_open.'</span>';
        $tab['id'][]    = 'pull_requests';
        $tab['icon'][]  = 'fa fa-code-fork';

        add_to_jquery('$(\'[data-toggle="tooltip"]\').tooltip();');

        echo opentab($tab, $_GET['section'], $repo['name'], TRUE, 'nav-tabs m-b-20');
        switch ($_GET['section']) {
            case 'contributors':
                add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $locale['gh_018']]);
                echo '<div class="row">';
                $i = 1;
                foreach ($contributors as $key => $contributor) {
                    if ($contributor['type'] == 'User') {
                        echo '<div class="col-xs-12 col-sm-6 col-md-4"><div class="panel panel-default m-b-20 p-10">';
                            echo '<div class="pull-right" style="font-size: 15px;">#'.$i.'</div>';
                            echo '<div class="clearfix">';
                                echo '<div class="display-inline-block img-responsive pull-left m-r-10"><img style="width: 45px;height: 45px;" alt="'.$contributor['login'].'" src="'.$contributor['avatar_url'].'"/></div>';
                                echo '<div class="display-inline">';
                                    echo '<a target="_blank" href="'.$contributor['html_url'].'"><h4 class="m-0">'.$contributor['login'].'</h4></a>';
                                    echo '<div style="font-size: 17px;">'.format_word($contributor['contributions'], $locale['gh_021']).'</div>';
                                echo '</div>';
                            echo '</div>';
                        echo '</div></div>';
                        $i++;
                    }
                }
                echo '</div>';
                break;
            case 'issues':
                add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $locale['gh_019']]);
                if ($repo['has_issues'] == 1) {
                    echo '<div class="m-b-10">';
                        if (isset($_GET['issue']) && $_GET['issue'] == 'create') {
                            $active = '';
                        } else {
                            $active = !isset($_GET['issues']) || isset($_GET['issues']) && $_GET['issues'] == 'open' ? ' text-dark strong' : '';
                        }

                        echo '<a class="m-r-10'.$active.'" href="'.INFUSIONS.'github/github.php'.fusion_get_aidlink().'&tab=repos&repo='.$repo_name.'&section=issues&issues=open">'.$locale['gh_022'].'</a>';
                        echo '<a class="m-r-10'.(isset($_GET['issues']) && $_GET['issues'] == 'closed' ? ' text-dark strong' : '').'" href="'.INFUSIONS.'github/github.php'.fusion_get_aidlink().'&tab=repos&repo='.$repo_name.'&section=issues&issues=closed">'.$locale['gh_023'].'</a>';
                        echo '<a'.(isset($_GET['issue']) && $_GET['issue'] == 'create' ? ' class="text-dark strong"' : '').' href="'.INFUSIONS.'github/github.php'.fusion_get_aidlink().'&tab=repos&repo='.$repo_name.'&section=issues&issue=create">'.$locale['gh_024'].'</a>';
                    echo '</div>';

                    if (isset($_GET['issue']) && $_GET['issue'] == 'create') {
                        if (!empty($userdata['user_github_access_token'])) {
                            if (isset($_POST['create_issue'])) {
                                $title = form_sanitizer($_POST['title'], '', 'title');

                                if (\defender::safe()) {
                                    $client->api('issue')->create($owner, $repo_name, ['title' => $title, 'body' => !empty($_POST['comment_body']) ? $_POST['comment_body'] : '']);

                                    addNotice('success', $locale['gh_025']);
                                    redirect(FUSION_REQUEST);
                                }
                            }

                            /**
                             * https://github.com/kartik-v/krajee-markdown-editor
                             * Docs http://plugins.krajee.com/markdown-editor
                             */

                            if (file_exists(INFUSIONS.'github/includes/markdown-editor/js/locales/'.$locale['short_lang_name'].'.js')) {
                                $lang = $locale['short_lang_name'];
                            } else {
                                $lang = 'en';
                            }

                            add_to_head('
                                <link href="'.INFUSIONS.'github/includes/markdown-editor/css/markdown-editor.min.css" media="all" rel="stylesheet""/>
                                <link href="'.INFUSIONS.'github/includes/markdown-editor/plugins/highlight/highlight.min.css" media="all" rel="stylesheet""/>
                                <script src="'.INFUSIONS.'github/includes/markdown-editor/plugins/purify/purify.min.js"></script>
                                <script src="'.INFUSIONS.'github/includes/markdown-editor/plugins/markdown-it/markdown-it.min.js"></script>
                                <script src="'.INFUSIONS.'github/includes/markdown-editor/plugins/markdown-it/markdown-it-deflist.min.js"></script>
                                <script src="'.INFUSIONS.'github/includes/markdown-editor/plugins/markdown-it/markdown-it-footnote.min.js"></script>
                                <script src="'.INFUSIONS.'github/includes/markdown-editor/plugins/markdown-it/markdown-it-abbr.min.js"></script>
                                <script src="'.INFUSIONS.'github/includes/markdown-editor/plugins/markdown-it/markdown-it-sub.min.js"></script>
                                <script src="'.INFUSIONS.'github/includes/markdown-editor/plugins/markdown-it/markdown-it-sup.min.js"></script>
                                <script src="'.INFUSIONS.'github/includes/markdown-editor/plugins/markdown-it/markdown-it-ins.min.js"></script>
                                <script src="'.INFUSIONS.'github/includes/markdown-editor/plugins/markdown-it/markdown-it-mark.min.js"></script>
                                <script src="'.INFUSIONS.'github/includes/markdown-editor/plugins/markdown-it/markdown-it-smartarrows.min.js"></script>
                                <script src="'.INFUSIONS.'github/includes/markdown-editor/plugins/markdown-it/markdown-it-checkbox.min.js"></script>
                                <script src="'.INFUSIONS.'github/includes/markdown-editor/plugins/markdown-it/markdown-it-cjk-breaks.min.js"></script>
                                <script src="'.INFUSIONS.'github/includes/markdown-editor/plugins/markdown-it/markdown-it-emoji.min.js"></script>
                                <script src="'.INFUSIONS.'github/includes/markdown-editor/plugins/highlight/highlight.min.js"></script>
                                <script src="'.INFUSIONS.'github/includes/markdown-editor/js/markdown-editor.min.js"></script>
                                <script src="'.INFUSIONS.'github/includes/markdown-editor/js/locales/'.$lang.'.js"></script>
                            ');

                            add_to_jquery("$('#comment_body').markdownEditor({
                                bsVersion: '3.4.1',
                                hiddenActions: ['emoji', 'export']
                            });");

                            echo openform('createissueform', 'post', FUSION_REQUEST);
                            echo form_text('title', '', '', ['placeholder' => $locale['gh_009'], 'error_text' => $locale['gh_026'], 'inline' => TRUE, 'required' => TRUE]);
                            echo '<textarea data-use-twemoji="false" name="comment_body" id="comment_body" rows="12" placeholder="'.$locale['gh_027'].'"></textarea>';
                            echo '<div class="m-b-10"><small>'.$locale['gh_028'].'</small></div>';
                            echo form_button('create_issue', $locale['gh_029'], $locale['gh_029'], ['class' => 'btn-success']);
                            echo closeform();
                        } else {
                            echo '<div class="well text-center">'.str_replace(['[LINK]', '[/LINK]'], ['<a href="'.INFUSIONS.'github/github.php'.fusion_get_aidlink().'&tab=settings">', '</a>'], $locale['gh_030']).'</div>';
                        }
                    } else {
                        add_to_css('.issues-list img {width: 100%; height: auto;}');

                        echo '<div class="list-group issues-list">';
                            foreach ($issues as $key => $issue) {
                                if (!isset($issue['pull_request'])) {
                                    $date = new DateTime($issue['created_at']);
                                    $date_open = $date->getTimestamp();

                                    echo '<div class="list-group-item">';
                                        if (!empty($issue['comments'])) {
                                            echo '<span class="pull-right"><i class="fa fa-comments-o"></i> '.$issue['comments'].'</span>';
                                        }

                                        if (!empty($issue['body'])) {
                                            echo '<a data-toggle="collapse" href="#collapse-issue-'.$issue['id'].'" aria-expanded="false" aria-controls="collapse-issue-'.$issue['id'].'" class="m-r-10"><h5 style="font-weight: 600;" class="m-0 display-inline">'.$issue['title'].' <span class="caret"></span></h5></a>';
                                        } else {
                                            echo '<div class="m-r-10 display-inline"><h5 style="font-weight: 600;" class="m-0 display-inline">'.$issue['title'].'</h5></div>';
                                        }

                                        if (!empty($issue['labels'])) {
                                            foreach ($issue['labels'] as $id => $label) {
                                                $bg_color = '#'.$label['color'];
                                                $color = '';

                                                if (function_exists('get_brightness')) {
                                                    $color = get_brightness($bg_color) > 130 ? '000' : 'fff';
                                                    $color = ' color: #'.$color.';';
                                                }

                                                echo '<span class="label m-l-5 m-r-5" style="background: '.$bg_color.';'.$color.' box-shadow: inset 0 -1px 0 rgba(27,31,35,0.12);">'.$label['name'].'</span>';
                                            }
                                        }

                                        echo '<div>';
                                            echo strtr($locale['gh_031'], [
                                                '[ID]'   => '#'.$issue['number'],
                                                '[TIME]' => timer($date_open),
                                                '[NAME]' => '<a target="_blank" href="https://github.com/'.$repo['full_name'].'/issues/created_by/'.$issue['user']['login'].'">'.$issue['user']['login'].'</a>'
                                            ]);

                                            if (!empty($issue['closed_at'])) {
                                                $date = new DateTime($issue['closed_at']);
                                                $date_closed = $date->getTimestamp();
                                                echo ' - '.$locale['gh_032'].' '.timer($date_closed);
                                            }
                                        echo '</div>';

                                        if (!empty($issue['body'])) {
                                            $body = preg_replace('/<!--(.*?)-->/', '', $issue['body']); // remove HTML comments
                                            $parsedown = new Parsedown;
                                            $parsedown->setSafeMode(TRUE);
                                            $body = $parsedown->text($body);
                                            echo '<div class="collapse" id="collapse-issue-'.$issue['id'].'"><div class="panel panel-default"><div class="panel-body">'.$body.'</div></div></div>';
                                        }

                                        echo '<a class="m-t-10" target="_blank" href="'.$issue['html_url'].'">'.$locale['gh_033'].'</a>';
                                    echo '</div>';
                                }
                            }
                        echo '</div>';
                    }

                    echo '<div class="text-center"><a class="btn btn-info" target="_blank" href="https://github.com/'.$repo['full_name'].'/issues">'.$locale['gh_034'].'</a></div>';
                }
                break;
            case 'pull_requests':
                add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $locale['gh_020']]);
                echo '<div class="m-b-10">';
                    echo '<a class="m-r-10'.(!isset($_GET['pull_requests']) || isset($_GET['pull_requests']) && $_GET['pull_requests'] == 'open' ? ' text-dark strong' : '').'" href="'.INFUSIONS.'github/github.php'.fusion_get_aidlink().'&tab=repos&repo='.$repo_name.'&section=pull_requests&pull_requests=open">'.$locale['gh_022'].'</a>';
                    echo '<a'.(isset($_GET['pull_requests']) && $_GET['pull_requests'] == 'closed' ? ' class="text-dark strong"' : '').' href="'.INFUSIONS.'github/github.php'.fusion_get_aidlink().'&tab=repos&repo='.$repo_name.'&section=pull_requests&pull_requests=closed">'.$locale['gh_023'].'</a>';
                echo '</div>';

                echo '<div class="list-group">';
                    $closed = isset($_GET['pull_requests']) && $_GET['pull_requests'] == 'closed' ? '?q=is%3Apr+is%3Aclosed' : '';
                    foreach ($pull_requests as $key => $pull_request) {
                        if (!isset($pull_request['pull_request'])) {
                            $date = new DateTime($pull_request['created_at']);
                            $date_open = $date->getTimestamp();

                            echo '<div class="list-group-item">';
                                if (!empty($pull_request['body'])) {
                                    echo '<a data-toggle="collapse" href="#collapse-pr-'.$pull_request['id'].'" aria-expanded="false" aria-controls="collapse-pr-'.$pull_request['id'].'" class="m-r-10"><h5 style="font-weight: 600;" class="m-0 display-inline">'.$pull_request['title'].' <span class="caret"></span></h5></a>';
                                } else {
                                    echo '<div class="m-r-10 display-inline"><h5 style="font-weight: 600;" class="m-0 display-inline">'.$pull_request['title'].'</h5></div>';
                                }

                                if (!empty($pull_request['labels'])) {
                                    foreach ($pull_request['labels'] as $id => $label) {
                                        $bg_color = '#'.$label['color'];
                                        $color = '';

                                        if (function_exists('get_brightness')) {
                                            $color = get_brightness($bg_color) > 130 ? '000' : 'fff';
                                            $color = ' color: #'.$color.';';
                                        }

                                        echo '<span class="label m-l-5 m-r-5" style="background: '.$bg_color.';'.$color.' box-shadow: inset 0 -1px 0 rgba(27,31,35,0.12);">'.$label['name'].'</span>';
                                    }
                                }

                                echo '<div>';
                                    echo strtr($locale['gh_031'], [
                                        '[ID]'   => '#'.$pull_request['number'],
                                        '[TIME]' => timer($date_open),
                                        '[NAME]' => '<a target="_blank" href="https://github.com/'.$repo['full_name'].'/pulls/'.$pull_request['user']['login'].$closed.'">'.$pull_request['user']['login'].'</a>'
                                    ]);

                                    if (!empty($pull_request['merged_at'])) {
                                        $date = new DateTime($pull_request['merged_at']);
                                        $date_closed = $date->getTimestamp();
                                        echo ' - '.$locale['gh_035'].' '.timer($date_closed);
                                    } else if (!empty($pull_request['closed_at'])) {
                                        $date = new DateTime($pull_request['closed_at']);
                                        $date_closed = $date->getTimestamp();
                                        echo ' - '.$locale['gh_032'].' '.timer($date_closed);
                                    }
                                echo '</div>';

                                if (!empty($pull_request['body'])) {
                                    $body = preg_replace('/<!--(.*?)-->/', '', $pull_request['body']); // remove HTML comments
                                    $parsedown = new Parsedown;
                                    $parsedown->setSafeMode(TRUE);
                                    $body = $parsedown->text($body);
                                    echo '<div class="collapse panel panel-default p-10 m-t-10" id="collapse-pr-'.$pull_request['id'].'">'.$body.'</div>';
                                }

                                echo '<a class="m-t-10" target="_blank" href="'.$pull_request['html_url'].'">'.$locale['gh_036'].'</a>';
                            echo '</div>';
                        }
                    }
                echo '</div>';

                echo '<div class="text-center"><a class="btn btn-info" target="_blank" href="https://github.com/'.$repo['full_name'].'/pulls'.$closed.'">'.$locale['gh_037'].'</a></div>';
                break;
            default:
                add_breadcrumb(['link' => FUSION_REQUEST, 'title' => $locale['gh_017']]);
                echo '<ul class="list-style-none">';
                foreach ($commits as $key => $commit) {
                    $commit_author = !empty($commit['commit']['author']['name']) ? $commit['commit']['author']['name'] : $locale['user_na'];
                    $date = new DateTime($commit['commit']['author']['date']);
                    $timestamp = $date->getTimestamp();

                    $message = explode("\n", $commit['commit']['message']);
                    $commit_title = $message[0];

                    unset($message[0]);
                    $message = array_filter($message);
                    $commit_desc = '';

                    if (!empty($message)) {
                        $commit_desc = '<a class="label label-default m-l-10" role="button" data-toggle="collapse" href="#'.$commit['sha'].'" aria-expanded="false" aria-controls="'.$commit['sha'].'"><i class="fa fa-ellipsis-h"></i></a>';
                        $commit_desc .= '<div class="collapse" id="'.$commit['sha'].'">'.nl2br(implode("\n", $message)).'</div>';
                    }

                    echo '<li class="m-t-5 m-b-5">';
                        echo '<div><a target="_blank" style="font-weight: 700;" href="'.$commit['html_url'].'">'.$commit_title.'</a>'.$commit_desc.'</div>';
                        echo '<a target="_blank" href="'.$commit['author']['html_url'].'"><div class="display-inline-block img-responsive pull-left m-r-5"><img style="width: 25px;height: 25px;" alt="'.$commit_author.'" src="'.$commit['author']['avatar_url'].'"/></div></a>';
                        echo '<div class="display-inline">';
                            echo '<a target="_blank" style="margin-bottom: -7px;" data-toggle="tooltip" title="View all commits by '.$commit_author.'" href="https://github.com/'.$repo['full_name'].'/commits?author='.$commit_author.'">'.$commit_author.'</a>';
                            echo ' <span>'.$locale['gh_038'].' '.timer($timestamp).'</span>';
                        echo '</div>';
                    echo '</li>';
                }
                echo '</ul>';

                echo '<div class="text-center"><a class="btn btn-info" target="_blank" href="https://github.com/'.$repo['full_name'].'/commits/'.$current_branch.'">'.$locale['gh_039'].'</a></div>';
                break;
        }
        echo closetab();

        $repos_tab = ob_get_contents();
        ob_end_clean();
    }

    $tabs = [];

    if (isset($_GET['tab']) && $_GET['tab'] == 'settings') {
        $tabs['back'] = [
            'name'    => $locale['gh_040'],
            'icon'    => 'fa fa-arrow-left',
            'content' => ''
        ];
    }

    if (isset($_GET['tab']) && $_GET['tab'] == 'back') {
        redirect(INFUSIONS.'github/github.php'.fusion_get_aidlink());
    }

    echo '<div class="m-b-20">';
        echo '<h4 class="display-inline va m-r-5">'.$locale['gh_041'].'</h4> ';
        echo '<img style="width: 30px;height:30px;" alt="'.$user['name'].'" src="'.$user['avatar_url'].'"/>';
        echo '<h3 class="display-inline m-l-5 va">'.$user['name'].'</h3>';

        $date = new DateTime($user['created_at']);
        $account_date = $date->getTimestamp();

        echo '<p class="m-t-5">'.$locale['gh_042'].' '.showdate('longdate', $account_date).'</p>';
    echo '</div>';

    $tabs += [
        'repos'   => [
            'name'     => $locale['gh_043'].' <span class="badge">'.count($repos).'</span>',
            'icon'     => 'fa fa-folder-open-o',
            'dropdown' => $repositories,
            'content'  => $repos_tab
        ],
        'settings' => [
            'name'    => $locale['gh_002'],
            'icon'    => 'fa fa-cog',
            'content' => $settings_tab
        ]
    ];

    echo '<div id="github-client">';
    echo '<ul class="nav nav-tabs m-b-15" role="tablist">';
    $i = 0;
    $i2 = 0;
    foreach ($tabs as $key => $tab) {
        if (!empty($tab['dropdown'])) {
            $active = (!isset($_GET['tab']) && $i == 0) || (isset($_GET['tab']) && $_GET['tab'] == $key) ? ' active' : '';
            echo '<li class="dropdown'.$active.'">';
                echo '<a href="#" class="dropdown-toggle" id="'.$key.'-tab" data-toggle="dropdown" aria-controls="'.$key.'-tab-contents" aria-haspopup="true" aria-expanded="false">'.(!empty($tab['icon']) ? '<i class="'.$tab['icon'].'"></i> ' : '').$tab['name'].' <span class="caret"></span></a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="'.$key.'-tab" id="'.$key.'-tab-contents">';
                        $active_ = (!isset($_GET['tab']) && $i == 0) || (isset($_GET['tab']) && $_GET['tab'] == $key) ? ' class="active"' : '';
                        // echo '<li'.$active_.'><a href="'.(!empty($tab['link']) ? $tab['link'] : FUSION_SELF.fusion_get_aidlink().'&tab='.$key).'">'.(!empty($tab['icon']) ? '<i class="'.$tab['icon'].'"></i> ' : '').$tab['name'].'</a></li>';

                        foreach ($tab['dropdown'] as $id => $name) {
                            $active_repo = (!isset($_GET['repo']) && $i2 == 0) ||(isset($_GET['repo']) && $_GET['repo'] == $name) ? ' class="active"' : '';
                            echo '<li'.$active_repo.'><a href="'.FUSION_SELF.fusion_get_aidlink().'&tab='.$key.'&repo='.$name.'">'.$name.'</a></li>';
                            $i2++;
                        }
                    echo '</ul>';
            echo '</li>';
        } else {
            $active = (!isset($_GET['tab']) && $i == 0) || (isset($_GET['tab']) && $_GET['tab'] == $key) ? ' class="active"' : '';
            echo '<li'.$active.'><a href="'.(!empty($tab['link']) ? $tab['link'] : FUSION_SELF.fusion_get_aidlink().'&tab='.$key).'">'.(!empty($tab['icon']) ? '<i class="'.$tab['icon'].'"></i> ' : '').$tab['name'].'</a></li>';
        }
        $i++;

    }
    echo '</ul>';

    echo '<div class="tab-content">';
    $i = 0;
    foreach ($tabs as $key => $tab) {
        $active = (!isset($_GET['tab']) && $i == 0) || (isset($_GET['tab']) && $_GET['tab'] == $key) ? ' active' : '';
        echo '<div class="tab-pane'.$active.'">'.$tab['content'].'</div>';
        $i++;
    }
    echo '</div>';
    echo '</div>';

    closetable();

    $client->removeCache();
} else {
    echo '<div class="well text-center">'.$locale['gh_044'].' '.showdate('%d.%m.%Y %H:%M:%S', $rate_limit_reset).' <a target="_blank" href="https://developer.github.com/v3/#rate-limiting">'.$locale['gh_045'].'</a></div>';
}

require_once THEMES.'templates/footer.php';
