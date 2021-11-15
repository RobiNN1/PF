<?php
/*
 * This file is part of the PF-CLI package.
 *
 * (c) Róbert Kelčák <robo@kelcak.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Console autoloader

if ('cli' !== PHP_SAPI) {
    echo "Only CLI access.\n";
    die(-1);
}

if (version_compare(PHP_VERSION, '7.2.5', '<')) {
    printf("Error: PF-CLI requires PHP %s or newer. You are running version %s.\n", '7.2.5', PHP_VERSION);
    die(-1);
}

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;

$app = new Application();
$app->setName('PHPFusion CLI');
$app->setVersion('1.0.0');

$directoryIterator = new \RecursiveDirectoryIterator(__DIR__.'/src/Commands/', FilesystemIterator::SKIP_DOTS);
$iterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::SELF_FIRST);

foreach ($iterator as $fileinfo) {
    if (!$fileinfo->isDir()) {
        if (preg_match('/Command.php$/', $iterator->getSubPathName())) {
            $class_name = 'PFCli\\Commands\\'.rtrim(str_replace('/', '\\', $iterator->getSubPathName()), '.php');
            $app->addCommands([new $class_name]);
        }
    }
}

$app->run();
