<?php











namespace Composer;

use Composer\Autoload\ClassLoader;
use Composer\Semver\VersionParser;








class InstalledVersions
{
private static $installed = array (
  'root' => 
  array (
    'pretty_version' => 'dev-master',
    'version' => 'dev-master',
    'aliases' => 
    array (
    ),
    'reference' => '22b7284ea01cd8be88429b2c3c8b74088ffefea8',
    'name' => '__root__',
  ),
  'versions' => 
  array (
    '__root__' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
      ),
      'reference' => '22b7284ea01cd8be88429b2c3c8b74088ffefea8',
    ),
    'cache/adapter-common' => 
    array (
      'pretty_version' => '1.2.0',
      'version' => '1.2.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '6b87c5cbdf03be42437b595dbe5de8e97cd1d497',
    ),
    'cache/filesystem-adapter' => 
    array (
      'pretty_version' => '1.1.0',
      'version' => '1.1.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '1501ca71502f45114844824209e6a41d87afb221',
    ),
    'cache/tag-interop' => 
    array (
      'pretty_version' => '1.0.1',
      'version' => '1.0.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '909a5df87e698f1665724a9e84851c11c45fbfb9',
    ),
    'clue/stream-filter' => 
    array (
      'pretty_version' => 'v1.5.0',
      'version' => '1.5.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'aeb7d8ea49c7963d3b581378955dbf5bc49aa320',
    ),
    'erusev/parsedown' => 
    array (
      'pretty_version' => '1.7.4',
      'version' => '1.7.4.0',
      'aliases' => 
      array (
      ),
      'reference' => 'cb17b6477dfff935958ba01325f2e8a2bfa6dab3',
    ),
    'guzzlehttp/guzzle' => 
    array (
      'pretty_version' => '7.3.0',
      'version' => '7.3.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '7008573787b430c1c1f650e3722d9bba59967628',
    ),
    'guzzlehttp/promises' => 
    array (
      'pretty_version' => '1.4.1',
      'version' => '1.4.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '8e7d04f1f6450fef59366c399cfad4b9383aa30d',
    ),
    'guzzlehttp/psr7' => 
    array (
      'pretty_version' => '1.8.2',
      'version' => '1.8.2.0',
      'aliases' => 
      array (
      ),
      'reference' => 'dc960a912984efb74d0a90222870c72c87f10c91',
    ),
    'http-interop/http-factory-guzzle' => 
    array (
      'pretty_version' => '1.0.0',
      'version' => '1.0.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '34861658efb9899a6618cef03de46e2a52c80fc0',
    ),
    'knplabs/github-api' => 
    array (
      'pretty_version' => 'v3.3.0',
      'version' => '3.3.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '0442abd1361956e3a87e71683154c5c33c5a0dad',
    ),
    'league/flysystem' => 
    array (
      'pretty_version' => '1.1.4',
      'version' => '1.1.4.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f3ad69181b8afed2c9edf7be5a2918144ff4ea32',
    ),
    'league/mime-type-detection' => 
    array (
      'pretty_version' => '1.7.0',
      'version' => '1.7.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '3b9dff8aaf7323590c1d2e443db701eb1f9aa0d3',
    ),
    'php-http/cache-plugin' => 
    array (
      'pretty_version' => '1.7.2',
      'version' => '1.7.2.0',
      'aliases' => 
      array (
      ),
      'reference' => '922409f10541b0d581b8ffe5cd810f4efc9e9e32',
    ),
    'php-http/client-common' => 
    array (
      'pretty_version' => '2.4.0',
      'version' => '2.4.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '29e0c60d982f04017069483e832b92074d0a90b2',
    ),
    'php-http/discovery' => 
    array (
      'pretty_version' => '1.14.0',
      'version' => '1.14.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '778f722e29250c1fac0bbdef2c122fa5d038c9eb',
    ),
    'php-http/httplug' => 
    array (
      'pretty_version' => '2.2.0',
      'version' => '2.2.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '191a0a1b41ed026b717421931f8d3bd2514ffbf9',
    ),
    'php-http/message' => 
    array (
      'pretty_version' => '1.11.1',
      'version' => '1.11.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '887734d9c515ad9a564f6581a682fff87a6253cc',
    ),
    'php-http/message-factory' => 
    array (
      'pretty_version' => 'v1.0.2',
      'version' => '1.0.2.0',
      'aliases' => 
      array (
      ),
      'reference' => 'a478cb11f66a6ac48d8954216cfed9aa06a501a1',
    ),
    'php-http/message-factory-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0',
      ),
    ),
    'php-http/multipart-stream-builder' => 
    array (
      'pretty_version' => '1.2.0',
      'version' => '1.2.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '11c1d31f72e01c738bbce9e27649a7cca829c30e',
    ),
    'php-http/promise' => 
    array (
      'pretty_version' => '1.1.0',
      'version' => '1.1.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '4c4c1f9b7289a2ec57cde7f1e9762a5789506f88',
    ),
    'psr/cache' => 
    array (
      'pretty_version' => '1.0.1',
      'version' => '1.0.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'd11b50ad223250cf17b86e38383413f5a6764bf8',
    ),
    'psr/cache-implementation' => 
    array (
      'provided' => 
      array (
        0 => '^1.0',
      ),
    ),
    'psr/http-client' => 
    array (
      'pretty_version' => '1.0.1',
      'version' => '1.0.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '2dfb5f6c5eff0e91e20e913f8c5452ed95b86621',
    ),
    'psr/http-client-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0',
      ),
    ),
    'psr/http-factory' => 
    array (
      'pretty_version' => '1.0.1',
      'version' => '1.0.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '12ac7fcd07e5b077433f5f2bee95b3a771bf61be',
    ),
    'psr/http-factory-implementation' => 
    array (
      'provided' => 
      array (
        0 => '^1.0',
      ),
    ),
    'psr/http-message' => 
    array (
      'pretty_version' => '1.0.1',
      'version' => '1.0.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f6561bf28d520154e4b0ec72be95418abe6d9363',
    ),
    'psr/http-message-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0',
      ),
    ),
    'psr/log' => 
    array (
      'pretty_version' => '1.1.4',
      'version' => '1.1.4.0',
      'aliases' => 
      array (
      ),
      'reference' => 'd49695b909c3b7628b6289db5479a1c204601f11',
    ),
    'psr/simple-cache' => 
    array (
      'pretty_version' => '1.0.1',
      'version' => '1.0.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '408d5eafb83c57f6365a3ca330ff23aa4a5fa39b',
    ),
    'psr/simple-cache-implementation' => 
    array (
      'provided' => 
      array (
        0 => '^1.0',
      ),
    ),
    'ralouphie/getallheaders' => 
    array (
      'pretty_version' => '3.0.3',
      'version' => '3.0.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '120b605dfeb996808c31b6477290a714d356e822',
    ),
    'symfony/deprecation-contracts' => 
    array (
      'pretty_version' => 'v2.4.0',
      'version' => '2.4.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '5f38c8804a9e97d23e0c8d63341088cd8a22d627',
    ),
    'symfony/options-resolver' => 
    array (
      'pretty_version' => 'v5.3.0',
      'version' => '5.3.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '162e886ca035869866d233a2bfef70cc28f9bbe5',
    ),
    'symfony/polyfill-php73' => 
    array (
      'pretty_version' => 'v1.23.0',
      'version' => '1.23.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'fba8933c384d6476ab14fb7b8526e5287ca7e010',
    ),
    'symfony/polyfill-php80' => 
    array (
      'pretty_version' => 'v1.23.0',
      'version' => '1.23.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'eca0bf41ed421bed1b57c4958bab16aa86b757d0',
    ),
  ),
);
private static $canGetVendors;
private static $installedByVendor = array();







public static function getInstalledPackages()
{
$packages = array();
foreach (self::getInstalled() as $installed) {
$packages[] = array_keys($installed['versions']);
}

if (1 === \count($packages)) {
return $packages[0];
}

return array_keys(array_flip(\call_user_func_array('array_merge', $packages)));
}









public static function isInstalled($packageName)
{
foreach (self::getInstalled() as $installed) {
if (isset($installed['versions'][$packageName])) {
return true;
}
}

return false;
}














public static function satisfies(VersionParser $parser, $packageName, $constraint)
{
$constraint = $parser->parseConstraints($constraint);
$provided = $parser->parseConstraints(self::getVersionRanges($packageName));

return $provided->matches($constraint);
}










public static function getVersionRanges($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

$ranges = array();
if (isset($installed['versions'][$packageName]['pretty_version'])) {
$ranges[] = $installed['versions'][$packageName]['pretty_version'];
}
if (array_key_exists('aliases', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['aliases']);
}
if (array_key_exists('replaced', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['replaced']);
}
if (array_key_exists('provided', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['provided']);
}

return implode(' || ', $ranges);
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getVersion($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['version'])) {
return null;
}

return $installed['versions'][$packageName]['version'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getPrettyVersion($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['pretty_version'])) {
return null;
}

return $installed['versions'][$packageName]['pretty_version'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getReference($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['reference'])) {
return null;
}

return $installed['versions'][$packageName]['reference'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getRootPackage()
{
$installed = self::getInstalled();

return $installed[0]['root'];
}








public static function getRawData()
{
@trigger_error('getRawData only returns the first dataset loaded, which may not be what you expect. Use getAllRawData() instead which returns all datasets for all autoloaders present in the process.', E_USER_DEPRECATED);

return self::$installed;
}







public static function getAllRawData()
{
return self::getInstalled();
}



















public static function reload($data)
{
self::$installed = $data;
self::$installedByVendor = array();
}





private static function getInstalled()
{
if (null === self::$canGetVendors) {
self::$canGetVendors = method_exists('Composer\Autoload\ClassLoader', 'getRegisteredLoaders');
}

$installed = array();

if (self::$canGetVendors) {
foreach (ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
if (isset(self::$installedByVendor[$vendorDir])) {
$installed[] = self::$installedByVendor[$vendorDir];
} elseif (is_file($vendorDir.'/composer/installed.php')) {
$installed[] = self::$installedByVendor[$vendorDir] = require $vendorDir.'/composer/installed.php';
}
}
}

$installed[] = self::$installed;

return $installed;
}
}
