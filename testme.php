<?php

#require_once 'vendor/autoload.php';
require_once 'src/exception/ConfigException.php';
require_once 'src/config/Config.php';

print "OK\n";

$conf = wplibs\config\Config::getNamedInstance('wp');
print "OK\n";
$db = wplibs\database\DatabaseAccess::getDatabaseInstance( $conf );
print "OK\n";

$smarty = new Smarty();

var_dump($smarty);
