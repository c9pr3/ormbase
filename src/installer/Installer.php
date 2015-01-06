<?php

namespace wplibs\installer;
use Composer\Script\Event;
 
class Installer {

    public static function preInstall(Event $event) {
        // provides access to the current ComposerIOConsoleIO
        // stream for terminal input/output
        $io = $event->getIO();
        return true;
    }
 
    // run any post install tasks here
    public static function postInstall(Event $event) {
        $composer = $event->getComposer();
        
        $io = $event->getIO();
        if ( $io->askConfirmation("Generating configuration file. Proceed? [YES/no] ", true) ) {

            $func = function () use ( $io ) {
                $configPath = $io->ask("Directory for config-files? [config/] ", 'config/');
                return $configPath;
            };
           

            $configPath = $func();
            while ( empty(trim($configPath)) || !is_dir($configPath) ) {
                echo "'$configPath' could not be found\n";
                $configPath = $func();
            }

            $configArray = [];
            if ( $io->askConfirmation("Create MYSQL-Database-Config? [YES/no]: ", true) ) {
                $configArray['DATABASE']['server']    = $io->ask('MYSQL-Server? [localhost]: ', 'localhost');
                $configArray['DATABASE']['username']  = $io->ask('MYSQL-Server Username?: ');
                $configArray['DATABASE']['password']  = $io->askAndHideAnswer('MYSQL-Server Password?: ');
                $configArray['DATABASE']['port']      = $io->ask('MYSQL-Server port? [3306]: ', '3306');
                $configArray['DATABASE']['dbname']    = $io->ask('MYSQL-Server Databasename?: ');
                $configArray['DATABASE']['dbbackend'] = 'mysql';
                $configArray['DATABASE']['debugsql']  = 1;
            }

            $configArray['CONFIG']['debug']    = 1;
            $configArray['CONFIG']['cache']    = 1;
            $configArray['CONFIG']['debuglog'] = 1;

            if ( !($file = fopen($configPath.'/wp.config.php', 'w+')) ) {
                print "Could not create file $configPath/wp.config.php\n";
                print "manually create it as .ini-file with the following values:\n";
                print_r($configArray);
                return true;
            }
            fwrite($file, "<?php exit; ?>\n");
            fwrite($file, "[DATABASE]\n");
            foreach ( $configArray['DATABASE'] AS $k => $v ) {
                fwrite( $file, "$k=$v\n" );
            }
            fwrite($file, "[CONFIG]\n");
            foreach ( $configArray['CONFIG'] AS $k => $v ) {
                fwrite( $file, "$k=$v\n" );
            }
            fwrite($file, "\n");
            fclose($file);

            print "\nConfigfile created in $configPath/wp.config.php\n";
            print "Everything looks OK\n";
            print "For final test, please try php testme.php\n";
            print "\nhf :-)\n\n - CS -\n\n";

            return true;
        }
    }
 
    // any tasks to run after the package is installed?
    public static function postPackageInstall(Event $event) {
        $installedPackage = $event->getComposer()->getPackage();
        return true;
    }
}
