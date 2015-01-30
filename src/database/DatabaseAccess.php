<?php
/**
 * class.DatabaseAccess.php
 * @package    ecsco\ormbase
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:05
 */

namespace ecsco\ormbase\database;

use ecsco\ormbase\config\Config;
use ecsco\ormbase\exception\DatabaseException;
use ecsco\ormbase\traits\CallTrait;
use ecsco\ormbase\traits\GetTrait;
use ecsco\ormbase\traits\NoCloneTrait;

/**
 * class DatabaseAccess
 * @package    ecsco\ormbase
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:05
 */
class DatabaseAccess {

    use CallTrait;
    use GetTrait;
    use NoCloneTrait;

    /**
     * Create new DatabaseAccess
     * @return DatabaseAccess
     */
    private function __construct() {
    }

    /**
     * Get a database instance
     *
     * @param \ecsco\ormbase\config\Config $config
     * @param string                       $databaseDriverClass
     *
     * @return \ecsco\ormbase\database\DatabaseInterface
     * @throws \Exception
     * @throws \ecsco\ormbase\exception\DatabaseException
     */
    public static function getDatabaseInstance( Config $config, $databaseDriverClass = '' ) {

        if ( !class_exists( $databaseDriverClass ) ) {
            $databaseDriverClass = $config->getItem( 'database', 'databaseclass' );
            if ( !class_exists( $databaseDriverClass ) ) {
                throw new DatabaseException( "Could not find class $databaseDriverClass" );
            }
        }

        if ( !is_subclass_of( $databaseDriverClass, '\ecsco\ormbase\database\DatabaseInterface' ) ) {
            throw new DatabaseException( "$databaseDriverClass must implement \ecsco\ormbase\database\DatabaseInterface" );
        }

        return $databaseDriverClass::getNamedInstance( $config->getItem( 'database' ) );
    }

    /**
     * Get query count for specific config
     *
     * @param \ecsco\ormbase\config\Config $config
     *
     * @throws \ecsco\ormbase\exception\ConfigException
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
     * @param \ecsco\ormbase\config\Config $config
     *
     * @throws \ecsco\ormbase\exception\ConfigException
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
