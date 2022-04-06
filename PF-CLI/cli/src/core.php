<?php
/*
 * This file is part of the PF-CLI package.
 *
 * (c) Róbert Kelčák <robo@kelcak.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Load important core components from current PHPFusion installation

use Defender\Token;
use PHPFusion\Authenticate;
use PHPFusion\Installer\Infusions;

if (!defined('IN_FUSION')) {
    define('IN_FUSION', true);
}

require_once PATH.'includes/core_resources_include.php';

// Establish mySQL database connection
if (!empty($db_host) && !empty($db_user) && !empty($db_name)) {
    dbconnect($db_host, $db_user, (!empty($db_pass) ? $db_pass : ''), $db_name, (!empty($db_port) ? $db_port : 3306));
}

// Fetch the settings from the database
$settings = fusion_get_settings();

// Settings dependent functions
date_default_timezone_set('UTC');

// Sanitise $_SERVER globals
$_SERVER['PHP_SELF'] = cleanurl($_SERVER['PHP_SELF']);
$_SERVER['QUERY_STRING'] = isset($_SERVER['QUERY_STRING']) ? cleanurl($_SERVER['QUERY_STRING']) : "";
$_SERVER['REQUEST_URI'] = isset($_SERVER['REQUEST_URI']) ? cleanurl($_SERVER['REQUEST_URI']) : "";

define("FUSION_QUERY", $_SERVER['QUERY_STRING'] ?? "");
define("FUSION_SELF", basename($_SERVER['PHP_SELF']));
define("FUSION_REQUEST", isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != "" ? $_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_NAME']);

// Calculate ROOT path for Permalinks
$current_path = html_entity_decode($_SERVER['REQUEST_URI']);
if (isset($settings['site_path']) && strcmp($settings['site_path'], "/") != 0) {
    $current_path = str_replace($settings['site_path'], '', $current_path);
} else {
    $current_path = ltrim($current_path, "/");
}

// for Permalinks include files.
define("PERMALINK_CURRENT_PATH", $current_path);
define('FORM_REQUEST', fusion_get_settings('site_seo') && defined('IN_PERMALINK') ? PERMALINK_CURRENT_PATH : FUSION_REQUEST);
//BREADCRUMB URL, INCLUDES PATH TO FILE AND FILENAME
//E.G. infusions/downloads/downloads.php OR VIEWPAGE.PHP
if (explode("?", PERMALINK_CURRENT_PATH)) {
    $filelink = explode("?", PERMALINK_CURRENT_PATH);
    define("FUSION_FILELINK", $filelink[0]);
} else {
    define("FUSION_FILELINK", PERMALINK_CURRENT_PATH);
}

const ROOT = SITE_ROOT;
const FUSION_ROOT = SITE_ROOT;
const TRUE_PHP_SELF = SITE_ROOT;

$userdata = Authenticate::getEmptyUserData();

// User level, Admin Rights & User Group definitions
define("iGUEST", $userdata['user_level'] == USER_LEVEL_PUBLIC ? 1 : 0);
define("iMEMBER", $userdata['user_level'] <= USER_LEVEL_MEMBER ? 1 : 0);
define("iADMIN", $userdata['user_level'] <= USER_LEVEL_ADMIN ? 1 : 0);
define("iSUPERADMIN", $userdata['user_level'] == USER_LEVEL_SUPER_ADMIN ? 1 : 0);
define("iUSER", $userdata['user_level']);
define("iUSER_RIGHTS", $userdata['user_rights']);
define("iUSER_GROUPS", substr($userdata['user_groups'], 1));

if (!defined('LANGUAGE')) {
    define('LANGUAGE', 'English');
}
if (!defined('LOCALESET')) {
    define('LOCALESET', 'English/');
}

Infusions::loadConfiguration();
