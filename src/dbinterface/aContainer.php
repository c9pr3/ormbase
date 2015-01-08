<?php
/**
 * class.aContainer.php
 *
 * @package    WPLIBS
 * @subpackage DBINTERFACE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:08
 */

namespace wplibs\dbinterface;

use wplibs\cache\Cache;
use wplibs\config\Config;
use wplibs\database\iSelection;

/**
 * class aContainer
 *
 * @package    WPLIBS
 * @subpackage DBINTERFACE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:08
 */
abstract class aContainer {

    const OBJECT_NAME = '';

    /**
     * @var array[\wplibs\database\iDatabase]
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
    protected function __construct( $name ) {

        self::$dbConnections[ $name ] = Config::getNamedInstance( $name )->getDatabase();

        $this->configName = $name;
    }

    /**
     * Clone is forbidden
     *
     * @throws \Exception
     * @return void
     */
    public function __clone() {

        throw new \Exception( "Cloning not allowed" );
    }

    /**
     * getBasicSelectFields
     *
     * @return string[]
     */
    public function getBasicSelectionFields() {

        return $this->basicFields;
    }

    /**
     * Create a new object
     *
     * @return \wplibs\dbinterface\aObject
     */
    public function createNew() {

        $class = get_class( $this );
        $row = self::descObject( $class, $this->configName );

        /** @noinspection PhpUndefinedFieldInspection */
        $objectName = $class::OBJECT_NAME;
        #$objectName = '\wplibs\dbinterface\\' . $objectName;

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
     *
     */
    public static function descObject( $objectName, $configName ) {

        /**
         * @TODO use selection
         */
        $res = self::$dbConnections[ $configName ]->query( sprintf( 'DESC %s', $objectName::TABLE_NAME ) );
        $rVal = [ ];

        while ( $row = $res->fetch_assoc() ) {
            $key = $row[ 'Field' ];
            $value = $row[ 'Default' ];

            $rVal[ $key ] = $value;
        }
        $res->free();

        return $rVal;
    }

    /**
     * Get database
     *
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
        while ( $row = $result->fetch_assoc() ) {
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
            if ( Cache::has( $objectName::CACHE_TYPE, $sql ) ) {
                /** @noinspection PhpUndefinedFieldInspection */
                return Cache::get( $objectName::CACHE_TYPE, $sql );
            }
            else {
                /** @noinspection PhpUndefinedFieldInspection */
                Cache::$stats[ 'stats' ][ 'hasnot' ][ ] = $objectName . ',' . $objectName::CACHE_TYPE . ',' . $sql;
            }
        }
        else {
            Cache::$stats[ 'stats' ][ 'notfound' ][ ] = $objectName;
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
            Cache::$stats[ 'stats' ][ 'added' ][ ] = $objectName . ',' . $objectName::CACHE_TYPE . ',' . $sql;
            /** @noinspection PhpUndefinedFieldInspection */
            Cache::add( $objectName::CACHE_TYPE, $sql, $retVal );
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
        $row = $result->fetch_assoc();
        if ( !$row ) {
            return [ ];
        }
        $retVal = $this->makeObject( $row, $objectName );

        $this->addToCache( $objectName, $query, $params, $retVal );

        return $retVal;
    }

    /**
     * Get config name
     *
     * @return string
     */
    final protected function getConfigName() {

        return $this->configName;
    }
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
