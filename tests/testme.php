<?php

require_once '../vendor/autoload.php';

print "This will be replaced by a proper phpunit-testing soon :-) ... i swear (tm)\n ";

print "Creating Config-Object ... ";
$conf = wplibs\config\Config::getNamedInstance('wp');
print "OK\n";

print "Creating Database-Object ... ";
$db = wplibs\database\DatabaseAccess::getDatabaseInstance( $conf );
print "OK\n";

