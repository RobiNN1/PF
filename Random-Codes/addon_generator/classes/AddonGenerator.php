<?php

/**
 * Class AddonGenerator
 *
 * @author RobiNN
 */
class AddonGenerator extends Filesystem {
    private $root_path;
    public $folder_name;
    private $tmp_path;
    private $replace;

    public function __construct() {
        $this->tmp_path = __DIR__.'/../tmp/';

        if (!is_dir($this->tmp_path)) {
            mkdir($this->tmp_path, 0777, TRUE);
        }

        $this->folder_name = md5(time());
    }

    public function createZipPack() {
        $dir = $this->tmp_path.$this->folder_name;

        $this->copyDir();

        $zip = new \ZipArchive();
        $zip->open($dir.'.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(realpath($dir)), \RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen(realpath($dir)) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        $this->remove($dir);
    }

    private function copyDir() {
        $target = $this->tmp_path.$this->folder_name.'/'.$this->replace['folder_name'].'/';

        $this->mkdir($target);

        $directoryIterator = new \RecursiveDirectoryIterator($this->root_path, \RecursiveDirectoryIterator::SKIP_DOTS);
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
    }

    public function downloadZip($name) {
        $file = $this->tmp_path.$name.'.zip';

        if (is_file($file)) {
            require_once INCLUDES.'class.httpdownload.php';

            ob_end_clean();

            $object = new \PHPFusion\httpdownload;
            $object->set_mime('application/zip');
            $object->set_byfile($file);
            $object->use_resume = TRUE;
            $object->download();
            exit;
        }
    }

    /**
     * @param string $root_path
     */
    public function setRootPath($root_path) {
        $this->root_path = $root_path;
    }

    /**
     * @param array $replace
     */
    public function setReplace($replace) {
        $this->replace = $replace;
    }
}
