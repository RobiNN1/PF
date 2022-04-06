<?php
/*
 * This file is part of the PF-CLI package.
 *
 * (c) Róbert Kelčák <robo@kelcak.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PFCli\Commands\Generate;

use PFCli\CoreFunctions;
use FilesystemIterator;
use Symfony\Component\Filesystem\Filesystem;

class AddonGenerator extends Filesystem {
    private $root_path;
    private $target_path;
    private $replace;
    private $is_file;

    /**
     * Generate Adoon
     *
     * @return bool
     */
    public function generate() {
        $path = $this->is_file ? '' : '/';
        $target = $this->target_path.$this->replace['folder_name'].$path;

        if (!$this->is_file) {
            $this->mkdir($target);
            $directoryIterator = new \RecursiveDirectoryIterator($this->root_path, FilesystemIterator::SKIP_DOTS);
            $iterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($iterator as $item) {
                if ($item->isDir()) {
                    $targetDir = strtr($target.$iterator->getSubPathName(), $this->replace);
                    $this->mkdir($targetDir);
                } else {
                    $targetFilename = $target.strtr($iterator->getSubPathName(), $this->replace);
                    $this->copy($item, $targetFilename);
                    $old = file_get_contents($targetFilename);
                    file_put_contents($targetFilename, strtr($old, $this->replace));
                }
            }

        } else {
            $targetFilename = strtr($target, $this->replace).'.php';
            $this->copy($this->root_path, $targetFilename);
            $old = file_get_contents($targetFilename);
            file_put_contents($targetFilename, strtr($old, $this->replace));

        }

        return true;

        return false;
    }

    /**
     * Set target path
     *
     * @param string $target_path
     */
    public function setTargetPath($target_path) {
        $this->target_path = str_replace('/', DIRECTORY_SEPARATOR, $target_path);
    }

    /**
     * Get target path
     */
    public function getTargetPath() {
        return $this->target_path;
    }

    /**
     * Set boilerplate path
     *
     * @param string $root_path
     * @param false  $is_file
     */
    public function setBoilerplatePath($root_path, $is_file = false) {
        $this->root_path = $root_path;
        $this->is_file = $is_file;
    }

    /**
     * Set the strings to be replaced
     *
     * @param array $replace
     */
    public function setReplace($replace) {
        $default = [
            'folder_name'   => '',
            'INF_EXIST'     => '',
            'ADDON_NAME'    => '',
            'YOUR_NAME'     => '',
            'YOUR_EMAIL'    => '',
            'YOUR_WEBSITE'  => '',
            'ADMIN_RIGHTS'  => '',
            'LOCALE_PREFIX' => '',
            'LICENSE_TITLE' => '',
            'LICENSE_TEXT'  => ''
        ];

        $replace += $default;

        $this->replace = $replace;
    }

    /**
     * Check if string ends with
     *
     * @param string $whole
     * @param string $end
     *
     * @return bool
     */
    public function stringEndsWith($whole, $end) {
        return (strpos($whole, $end, strlen($whole) - strlen($end)) !== false);
    }

    /**
     * Activate addon
     *
     * @param string $type
     * @param string $name
     * @param string $folder_name
     *
     * @return bool
     */
    public function activate(string $type, string $name, string $folder_name) {
        $core = new CoreFunctions();

        if ($core->is_loaded !== true) {
            $core->load();
        }

        if ($core->is_loaded) {
            switch ($type) {
                case 'panel':
                    dbquery("INSERT INTO ".DB_PANELS." (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_url_list, panel_restriction, panel_languages) VALUES ('".$name."', '".$folder_name."', '', '2', '1', 'file', '0', '1', '1', '', '2', '".fusion_get_settings('enabled_languages')."')");

                    return true;
                    break;
                case 'infusion':
                    define('FUSION_NULL', false); // disable redirect
                    \PHPFusion\Installer\Infusions::getInstance()->infuse($folder_name);

                    return true;
                    break;
                case 'theme':
                    dbquery("UPDATE ".DB_SETTINGS." SET settings_value='".$folder_name."' WHERE settings_name='theme'");

                    return true;
                    break;
                case 'admintheme':
                    dbquery("UPDATE ".DB_SETTINGS." SET settings_value='".$folder_name."' WHERE settings_name='admin_theme'");

                    return true;
                    break;
            }
        }

        return false;
    }

    /**
     * Return Addon licene
     *
     * @param string $type
     *
     * @return string[]
     */
    public function license($type) {
        $title = '';
        $text = '';

        if ($type !== 'none') {
            $text = PHP_EOL.'+--------------------------------------------------------+'.PHP_EOL;

            if ($type == 'epal') {
                $title = 'EPAL';
                $text .= '| This program is released under the Enduser PHPFusion Addon License'.PHP_EOL;
                $text .= '| This software is licensed, not sold.'.PHP_EOL;
                $text .= '| Please read the attached License Agreement.'.PHP_EOL;
                $text .= '| You can read it by viewing the included epal.txt'.PHP_EOL;
                $text .= '| or online https://phpfusion.com/licensing/?epal'.PHP_EOL;
                $text .= '| Removal of this copyright header is strictly prohibited without'.PHP_EOL;
            } else {
                $title = 'AGPL3';
                $text .= '| This program is released as free software under the'.PHP_EOL;
                $text .= '| Affero GPL license. You can redistribute it and/or'.PHP_EOL;
                $text .= '| modify it under the terms of this license which you'.PHP_EOL;
                $text .= '| can read by viewing the included agpl.txt or online'.PHP_EOL;
                $text .= '| at www.gnu.org/licenses/agpl.html. Removal of this'.PHP_EOL;
                $text .= '| copyright header is strictly prohibited without'.PHP_EOL;
            }
            $text .= '| written permission from the original author(s).';
        }

        return ['title' => $title, 'text' => $text];
    }
}
