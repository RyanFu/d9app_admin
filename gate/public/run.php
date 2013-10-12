<?php
namespace Gate;

error_reporting(E_ALL); 
ini_set("display_errors", 0);

if ($argc < 2) {
	exit("Wrong parameters");
}

define('ROOT_PATH', __DIR__ . '/..');
define('LIB_PATH', ROOT_PATH . '/../phplib');

// require zoo's autoloader
require_once(LIB_PATH . '/Autoloader.class.php');
require_once(ROOT_PATH . '/config/scripts/config.inc.php');

$root_path_setting = array(
	'gate' => ROOT_PATH,
	'phplib' => LIB_PATH,
);

$autoloader = \Phplib\Autoloader::get($root_path_setting);
// configuration of MySQL, Redis, Memcache, etc.
\Phplib\Config::setConfigNamespace('\\Gate\\Config\\Scripts');
$class = "\\Gate\\Scripts\\{$argv[1]}";
if (!class_exists($class)) {
	exit("Wrong class");
}

ini_set('default_socket_timeout', -1);

//delete run.php from argv
array_shift($argv);
//delete the class name from argv
array_shift($argv);

$worker = new $class($argv);
$worker->run();

exit(0);
