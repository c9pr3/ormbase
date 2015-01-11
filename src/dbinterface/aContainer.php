<?php
/**
 * class.aContainer.php
 * @package    WPLIBS
 * @subpackage DBINTERFACE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:08
 */

namespace wplibs\dbinterface;

use wplibs\cache\CacheAccess;
use wplibs\config\Config;
use wplibs\database\iSelection;
use wplibs\database\DatabaseAccess;

/**
 * class aContainer
 * @package    WPLIBS
 * @subpackage DBINTERFACE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:08
 */
abstract class aContainer {

    const OBJECT_NAME = '';

    /**
     * @var \wplibs\database\iDatabase[]
     */
    private static $dbConnections = [ ];
    protected      $basicFields   = [ ];
    private        $configName    = '';

    /**
     * Construct
     *
     * @param string
     *
     * @return aContainer
     */
    protected function __construct() {

        $name = md5( serialize( Config::getInstance()->getSection( 'database' ) ) );
        self::$dbConnections[ $name ] =
            DatabaseAccess::getDatabaseInstance( Config::getInstance(), Config::getInstance()->getItem( 'database', 'dbbackend' ) );

        $this->configName = $name;
    }

    /**
     * Clone is forbidden
     * @throws \Exception
     * @return void
     */
    public function __clone() {

        throw new \Exception( "Cloning not allowed" );
    }

    /**
     * getBasicSelectFields
     * @return string[]
     */
    public function getBasicSelectionFields() {

        return $this->basicFields;
    }

    /**
     * Create a new object
     * @return \wplibs\dbinterface\aObject
     */
    public function createNew() {

        $class = get_class( $this );
        $row = self::descObject( $class, $this->configName );

        /** @noinspection PhpUndefinedFieldInspection */
        $objectName = $class::OBJECT_NAME;
        $obj = new $objectName( $row, $this->getDatabase() );

        return $obj;
    }

    /**
     * Desc table of object
     *
     * @param $objectName
     * @param $configName
     *
     * @return \string[]
     * @internal param $string
     */
    public static function descObject( $objectName, $configName ) {

        $query = self::$dbConnections[ $configName ]->desc()->table( $objectName::TABLE_NAME );
        $res = self::$dbConnections[ $configName ]->prepareQuery( $query, ...$query->getQueryParams() );
        $rVal = [ ];

        foreach ( $res AS $row ) {
            $key = $row[ 'Field' ];
            $value = $row[ 'Default' ];

            $rVal[ $key ] = $value;
        }

        return $rVal;
    }

    /**
     * Get database
     * @return \wplibs\database\iDatabase
     */
    final protected function getDatabase() {

        return self::$dbConnections[ $this->configName ];
    }

    /**
     * makePreparedObjects
     *
     * @param mixed $query
     * @param mixed $objectName
     * @param ... $params
     *
     * @return array
     */
    protected function makePreparedObjects( iSelection $query, $objectName, ...$params ) {

        if ( $retVal = ( $this->getFromCache( $objectName, $query, $params ) ) ) {
            return $retVal;
        }

        $result = $this->getDatabase()->prepareQuery( $query, ...$params );

        $retVal = [ ];
        foreach ( $result AS $row ) {
            $obj = $this->makeObject( $row, $objectName );
            $retVal[ ] = $obj;
        }

        $this->addToCache( $objectName, $query, $params, $retVal );

        return $retVal;
    }

    /**
     * getFromCache
     *
     * @param mixed $objectName
     * @param mixed $sql
     * @param       $params
     *
     * @return array
     */
    private function getFromCache( $objectName, $sql, $params ) {

        $sql = $sql . ' - ' . implode( '', $params );
        if ( is_subclass_of( $objectName, '\wplibs\dbinterface\iCachable' ) ) {
            /** @noinspection PhpUndefinedFieldInspection */
            if ( CacheAccess::has( $objectName::getCacheIdentifier(), $sql ) ) {
                /** @noinspection PhpUndefinedFieldInspection */
                return CacheAccess::get( $objectName::getCacheIdentifier(), $sql );
            }
            else {
                /** @noinspection PhpUndefinedFieldInspection */
                CacheAccess::$stats[ 'stats' ][ 'hasnot' ][ ] = $objectName . ',' . $objectName::getCacheIdentifier(). ',' . $sql;
            }
        }
        else {
            CacheAccess::$stats[ 'stats' ][ 'notfound' ][ ] = $objectName;
        }

        return null;
    }

    /**
     * Make object
     *
     * @param array $row
     * @param       string []
     *
     * @return \wplibs\dbinterface\aObject
     */
    private function makeObject( array $row, $objectName ) {

        /** @noinspection PhpUndefinedMethodInspection */
        $obj = $objectName::Factory( $row, $this->getDatabase() );
        /** @noinspection PhpUndefinedMethodInspection */
        $obj->isNew( false );

        return $obj;
    }

    /**
     * addToCache
     *
     * @param mixed $objectName
     * @param mixed $sql
     * @param       $params
     * @param mixed $retVal
     *
     * @return void
     */
    private function addToCache( $objectName, $sql, $params, $retVal ) {

        $sql = $sql . ' - ' . implode( '', $params );
        if ( is_subclass_of( $objectName, '\wplibs\dbinterface\iCachable' ) ) {
            /** @noinspection PhpUndefinedFieldInspection */
            CacheAccess::$stats[ 'stats' ][ 'added' ][ ] = $objectName . ',' . $objectName::getCacheIdentifier(). ',' . $sql;
            /** @noinspection PhpUndefinedFieldInspection */
            CacheAccess::add( $objectName::getCacheIdentifier(), $sql, $retVal );
        }
    }

    /**
     * makePreparedObject
     *
     * @param mixed $query
     * @param mixed $objectName
     * @param ... $params
     *
     * @return \wplibs\dbinterface\aObject
     */
    protected function makePreparedObject( iSelection $query, $objectName, ...$params ) {

        if ( $retVal = ( $this->getFromCache( $objectName, $query, $params ) ) ) {
            return $retVal;
        }

        $result = $this->getDatabase()->prepareQuery( $query, ...$params );
        if ( !$result ) {
            return [ ];
        }
        $row = array_shift( $result );
        $retVal = $this->makeObject( $row, $objectName );

        $this->addToCache( $objectName, $query, $params, $retVal );

        return $retVal;
    }

    /**
     * Get config name
     * @return string
     */
    final protected function getConfigName() {

        return $this->configName;
    }
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
