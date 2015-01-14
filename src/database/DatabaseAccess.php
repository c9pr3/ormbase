<?php
/**
 * class.DatabaseAccess.php
 * @package    wplibs
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:05
 */

namespace wplibs\database;

use wplibs\config\Config;
use wplibs\exception\DatabaseException;
use wplibs\traits\tCall;

/**
 * class DatabaseAccess
 * @package    wplibs
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:05
 */
class DatabaseAccess {

    use tCall;

    /**
     * Create new DatabaseAccess
     * @return DatabaseAccess
     */
    private function __construct() {
    }

    /**
     * Get a database instance
     *
     * @param \wplibs\config\Config $config
     * @param string                $databaseDriverClass
     *
     * @return \wplibs\database\iDatabase
     * @throws \Exception
     * @throws \wplibs\exception\DatabaseException
     */
    public static function getDatabaseInstance( Config $config, $databaseDriverClass = '' ) {

        if ( !class_exists( $databaseDriverClass ) ) {
            $databaseDriverClass = $config->getItem( 'database', 'databaseclass' );
            if ( !class_exists( $databaseDriverClass ) ) {
                throw new DatabaseException( "Could not find class $databaseDriverClass" );
            }
        }

        if ( !is_subclass_of( $databaseDriverClass, '\wplibs\database\iDatabase' ) ) {
            throw new DatabaseException( "$databaseDriverClass must implement \wplibs\database\iDatabase" );
        }

        return $databaseDriverClass::getNamedInstance( $config->getSection( 'database' ) );
    }

    /**
     * Get query count for specific config
     *
     * @param \wplibs\config\Config $config
     *
     * @throws \wplibs\exception\ConfigException
     * @return int
     */
    public static function getQueryCount( Config $config ) {

        $nameSpace = $config->getItem( 'database', 'databaseclass' );

        /** @noinspection PhpUndefinedMethodInspection */

        return $nameSpace::getQueryCount();
    }

    /**
     * Get queries for specific config
     *
     * @param \wplibs\config\Config $config
     *
     * @throws \wplibs\exception\ConfigException
     * @return string[]
     */
    public static function getQueries( Config $config ) {

        $nameSpace = $config->getItem( 'database', 'databaseclass' );

        /** @noinspection PhpUndefinedMethodInspection */

        return $nameSpace::getQueries();
    }
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
