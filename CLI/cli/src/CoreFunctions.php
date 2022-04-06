<?php
/*
 * This file is part of the PF-CLI package.
 *
 * (c) Róbert Kelčák <robo@kelcak.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PFCli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\ConsoleOutput;

class CoreFunctions {
    public $is_loaded = false;

    /**
     * Load PHPFusion core
     */
    public function load() {
        define('SITE_ROOT', $this->getCurrentRoot());
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.0';
        $_SERVER['HTTP_USER_AGENT'] = '';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $cwd = isset($_SERVER['PWD']) && is_dir($_SERVER['PWD']) ? $_SERVER['PWD'] : getcwd();
        define('PATH', realpath($cwd).DIRECTORY_SEPARATOR);

        if (file_exists(PATH.'includes/core_resources_include.php')) {
            require_once __DIR__.'/core.php';

            $this->is_loaded = true;

            return true;
        } else {
            $output = new ConsoleOutput();
            $output->writeln('<error>PHPFusion not found.</>');

            return Command::FAILURE;
        }
    }

    /**
     * Replaces special characters in a string with their "non-special" counterpart.
     *
     * @param $value
     *
     * @return string
     */
    public function normalize($value) {
        $table = [
            '&amp;' => 'and', '@' => 'at', '©' => 'c', '®' => 'r', 'À' => 'a', '(' => '', ')' => '', '.' => '',
            'Á'     => 'a', 'Â' => 'a', 'Ä' => 'a', 'Å' => 'a', 'Æ' => 'ae', 'Ç' => 'c',
            'È'     => 'e', 'É' => 'e', 'Ë' => 'e', 'Ì' => 'i', 'Í' => 'i', 'Î' => 'i',
            'Ï'     => 'i', 'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Õ' => 'o', 'Ö' => 'o',
            'Ø'     => 'o', 'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'Ý' => 'y',
            'ß'     => 'ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', 'å' => 'a',
            'æ'     => 'ae', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì'     => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ò' => 'o', 'ó' => 'o',
            'ô'     => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u',
            'û'     => 'u', 'ü' => 'u', 'ý' => 'y', 'þ' => 'p', 'ÿ' => 'y', 'Ā' => 'a',
            'ā'     => 'a', 'Ă' => 'a', 'ă' => 'a', 'Ą' => 'a', 'ą' => 'a', 'Ć' => 'c',
            'ć'     => 'c', 'Ĉ' => 'c', 'ĉ' => 'c', 'Ċ' => 'c', 'ċ' => 'c', 'Č' => 'c',
            'č'     => 'c', 'Ď' => 'd', 'ď' => 'd', 'Đ' => 'd', 'đ' => 'd', 'Ē' => 'e',
            'ē'     => 'e', 'Ĕ' => 'e', 'ĕ' => 'e', 'Ė' => 'e', 'ė' => 'e', 'Ę' => 'e',
            'ę'     => 'e', 'Ě' => 'e', 'ě' => 'e', 'Ĝ' => 'g', 'ĝ' => 'g', 'Ğ' => 'g',
            'ğ'     => 'g', 'Ġ' => 'g', 'ġ' => 'g', 'Ģ' => 'g', 'ģ' => 'g', 'Ĥ' => 'h',
            'ĥ'     => 'h', 'Ħ' => 'h', 'ħ' => 'h', 'Ĩ' => 'i', 'ĩ' => 'i', 'Ī' => 'i',
            'ī'     => 'i', 'Ĭ' => 'i', 'ĭ' => 'i', 'Į' => 'i', 'į' => 'i', 'İ' => 'i',
            'ı'     => 'i', 'Ĳ' => 'ij', 'ĳ' => 'ij', 'Ĵ' => 'j', 'ĵ' => 'j', 'Ķ' => 'k',
            'ķ'     => 'k', 'ĸ' => 'k', 'Ĺ' => 'l', 'ĺ' => 'l', 'Ļ' => 'l', 'ļ' => 'l',
            'Ľ'     => 'l', 'ľ' => 'l', 'Ŀ' => 'l', 'ŀ' => 'l', 'Ł' => 'l', 'ł' => 'l',
            'Ń'     => 'n', 'ń' => 'n', 'Ņ' => 'n', 'ņ' => 'n', 'Ň' => 'n', 'ň' => 'n',
            'ŉ'     => 'n', 'Ŋ' => 'n', 'ŋ' => 'n', 'Ō' => 'o', 'ō' => 'o', 'Ŏ' => 'o',
            'ŏ'     => 'o', 'Ő' => 'o', 'ő' => 'o', 'Œ' => 'oe', 'œ' => 'oe', 'Ŕ' => 'r',
            'ŕ'     => 'r', 'Ŗ' => 'r', 'ŗ' => 'r', 'Ř' => 'r', 'ř' => 'r', 'Ś' => 's',
            'ś'     => 's', 'Ŝ' => 's', 'ŝ' => 's', 'Ş' => 's', 'ş' => 's', 'Š' => 's',
            'š'     => 's', 'Ţ' => 't', 'ţ' => 't', 'Ť' => 't', 'ť' => 't', 'Ŧ' => 't',
            'ŧ'     => 't', 'Ũ' => 'u', 'ũ' => 'u', 'Ū' => 'u', 'ū' => 'u', 'Ŭ' => 'u',
            'ŭ'     => 'u', 'Ů' => 'u', 'ů' => 'u', 'Ű' => 'u', 'ű' => 'u', 'Ų' => 'u',
            'ų'     => 'u', 'Ŵ' => 'w', 'ŵ' => 'w', 'Ŷ' => 'y', 'ŷ' => 'y', 'Ÿ' => 'y',
            'Ź'     => 'z', 'ź' => 'z', 'Ż' => 'z', 'ż' => 'z', 'Ž' => 'z', 'ž' => 'z',
            'ſ'     => 'z', 'Ə' => 'e', 'ƒ' => 'f', 'Ơ' => 'o', 'ơ' => 'o', 'Ư' => 'u',
            'ư'     => 'u', 'Ǎ' => 'a', 'ǎ' => 'a', 'Ǐ' => 'i', 'ǐ' => 'i', 'Ǒ' => 'o',
            'ǒ'     => 'o', 'Ǔ' => 'u', 'ǔ' => 'u', 'Ǖ' => 'u', 'ǖ' => 'u', 'Ǘ' => 'u',
            'ǘ'     => 'u', 'Ǚ' => 'u', 'ǚ' => 'u', 'Ǜ' => 'u', 'ǜ' => 'u', 'Ǻ' => 'a',
            'ǻ'     => 'a', 'Ǽ' => 'ae', 'ǽ' => 'ae', 'Ǿ' => 'o', 'ǿ' => 'o', 'ə' => 'e',
            'Ё'     => 'jo', 'Є' => 'e', 'І' => 'i', 'Ї' => 'i', 'А' => 'a', 'Б' => 'b',
            'В'     => 'v', 'Г' => 'g', 'Д' => 'd', 'Е' => 'e', 'Ж' => 'zh', 'З' => 'z',
            'И'     => 'i', 'Й' => 'j', 'К' => 'k', 'Л' => 'l', 'М' => 'm', 'Н' => 'n',
            'О'     => 'o', 'П' => 'p', 'Р' => 'r', 'С' => 's', 'Т' => 't', 'У' => 'u',
            'Ф'     => 'f', 'Х' => 'h', 'Ц' => 'c', 'Ч' => 'ch', 'Ш' => 'sh', 'Щ' => 'sch',
            'Ъ'     => '-', 'Ы' => 'y', 'Ь' => '-', 'Э' => 'je', 'Ю' => 'ju', 'Я' => 'ja',
            'а'     => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ж'     => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l',
            'м'     => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
            'т'     => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш'     => 'sh', 'щ' => 'sch', 'ъ' => '-', 'ы' => 'y', 'ь' => '-', 'э' => 'je',
            'ю'     => 'ju', 'я' => 'ja', 'ё' => 'jo', 'є' => 'e', 'і' => 'i', 'ї' => 'i',
            'Ґ'     => 'g', 'ґ' => 'g', 'א' => 'a', 'ב' => 'b', 'ג' => 'g', 'ד' => 'd',
            'ה'     => 'h', 'ו' => 'v', 'ז' => 'z', 'ח' => 'h', 'ט' => 't', 'י' => 'i',
            'ך'     => 'k', 'כ' => 'k', 'ל' => 'l', 'ם' => 'm', 'מ' => 'm', 'ן' => 'n',
            'נ'     => 'n', 'ס' => 's', 'ע' => 'e', 'ף' => 'p', 'פ' => 'p', 'ץ' => 'C',
            'צ'     => 'c', 'ק' => 'q', 'ר' => 'r', 'ש' => 'w', 'ת' => 't', '™' => 'tm',
            'ء'     => 'a', 'ا' => 'a', 'آ' => 'a', 'ب' => 'b', 'پ' => 'p', 'ت' => 't',
            'ث'     => 's', 'ج' => 'j', 'چ' => 'ch', 'ح' => 'h', 'خ' => 'kh', 'د' => 'd',
            'ر'     => 'r', 'ز' => 'z', 'ژ' => 'zh', 'س' => 's', 'ص' => 's', 'ض' => 'z',
            'ط'     => 't', 'ظ' => 'z', 'غ' => 'gh', 'ف' => 'f', 'ق' => 'q', 'ک' => 'k',
            'گ'     => 'g', 'ل' => 'l', 'م' => 'm', 'ن' => 'n', 'و' => 'w', 'ه' => 'h', 'ی' => 'y ',
        ];

        return strtr($value, $table);
    }

    /**
     * Get path of PHPFusion config.php
     *
     * @param int $max_level
     *
     * @return string|null The relative path of the base directory or NULL if config.php was not found
     */
    public function fusionGetConfig($max_level = 7) {
        static $config_path = null;
        if ($config_path === null) {
            $basedir = "";
            $i = 0;
            while ($i <= $max_level and !file_exists($basedir.'config.php')) {
                $basedir .= '../';
                $i++;
            }
            $config_path = file_exists($basedir.'config.php') ? $basedir.'config.php' : null;
        }

        return $config_path;
    }

    /**
     * Get CMS root
     *
     * @return string
     */
    public function getSiteRoot() {
        return strpos($this->fusionGetConfig(), '/') === false ? '' : dirname($this->fusionGetConfig()).'/';
    }

    /**
     * Get CLI root
     *
     * @return string
     */
    public function getCurrentRoot() {
        $cwd = isset($_SERVER['PWD']) && is_dir($_SERVER['PWD']) ? $_SERVER['PWD'] : getcwd();

        return realpath($cwd).DIRECTORY_SEPARATOR;
    }

    /**
     * Get settings
     *
     * @param null $key
     *
     * @return false|string|string[]|null
     */
    public function getSettings($key = null) {
        $core = new CoreFunctions();

        if ($core->is_loaded !== true) {
            $core->load();
        }

        if ($core->is_loaded) {
            return fusion_get_settings($key);
        }

        return false;
    }
}
