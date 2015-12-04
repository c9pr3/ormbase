<?php
/**
 * AbstractContainer.php
 * @package    ecsco\ormbase
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:08
 */

declare(strict_types=1);

namespace ecsco\ormbase;

use ecsco\ormbase\cache\CacheAccess;
use ecsco\ormbase\config\Config;
use ecsco\ormbase\database\DatabaseAccess;
use ecsco\ormbase\database\SelectionInterface;
use ecsco\ormbase\traits\CallTrait;
use ecsco\ormbase\traits\GetTrait;
use ecsco\ormbase\traits\NoCloneTrait;

/**
 * AbstractContainer
 * @package    ecsco\ormbase
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:08
 */
abstract class AbstractContainer {

    use CallTrait;
    use GetTrait;
    use NoCloneTrait;

    const OBJECT_NAME = '';

    /**
     * Used db connections
     * @var \ecsco\ormbase\database\DatabaseInterface[]
     */
    private static $dbConnections = [ ];

    /**
     * This keys will always be selected
     * regardless what FieldSelection says
     * Usually this contains 'id'
     * @var array
     */
    protected $basicSelectionFields = [ ];

    /**
     * Current configName
     * @var string
     */
    private $configName = '';

    /**
     * Construct
     *
     * @param string
     *
     * @return AbstractContainer
     */
    protected function __construct() {

        $name = md5( serialize( Config::getInstance()->getItem( 'database' ) ) );
        self::$dbConnections[ $name ] = DatabaseAccess::getDatabaseInstance( Config::getInstance() );

        $this->configName = $name;
    }

    /**
     * getBasicSelectFields
     * @return string[]
     */
    public function getBasicSelectionFields(): array {

        return $this->basicSelectionFields;
    }

    /**
     * Create a new object
     * @return \ecsco\ormbase\AbstractObject
     */
    public function createNew(): AbstractObject {

        $class = get_called_class();
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
     */
    public static function descObject( string $objectName, string $configName ): array {

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
     * @return \ecsco\ormbase\database\DatabaseInterface
     */
    final protected function getDatabase(): DatabaseInterface {

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
    protected function makePreparedObjects( SelectionInterface $query, string $objectName, string ...$params ): array {

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
     * @param mixed $params
     *
     * @return array
     */
    private function getFromCache( string $objectName, string $sql, array $params ): array {

        $sql = $sql . ' - ' . implode( '', $params );
        if ( is_subclass_of( $objectName, '\ecsco\ormbase\CachableInterface' ) ) {
            $cache = CacheAccess::getCacheInstance();
            if ( $cache->has( $objectName::getCacheIdentifier(), $sql ) ) {
                return $cache->get( $objectName::getCacheIdentifier(), $sql );
            }
            else {
                CacheAccess::$stats[ 'stats' ][ 'hasnot' ][ ] =
                    $objectName . ',' . $objectName::getCacheIdentifier() . ',' . $sql;
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
     * @param       aObject
     *
     * @return \ecsco\ormbase\AbstractObject
     */
    private function makeObject( array $row, string $objectName ): AbstractObject {

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
     * @param mixed $params
     * @param mixed $retVal
     *
     * @return void
     */
    private function addToCache( string $objectName, string $sql, array $params, $retVal ) {

        $sql = $sql . ' - ' . implode( '', $params );
        if ( is_subclass_of( $objectName, '\ecsco\ormbase\CachableInterface' ) ) {
            $cache = CacheAccess::getCacheInstance();
            CacheAccess::$stats[ 'stats' ][ 'added' ][ ] =
                $objectName . ',' . $objectName::getCacheIdentifier() . ',' . $sql;
            $cache->add( $objectName::getCacheIdentifier(), $sql, $retVal );
        }
    }

    /**
     * makePreparedObject
     *
     * @param mixed $query
     * @param mixed $objectName
     * @param ... $params
     *
     * @return \ecsco\ormbase\AbstractObject
     */
    protected function makePreparedObject( SelectionInterface $query, string $objectName, string ...$params ): AbstractObject {

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
    final protected function getConfigName(): string {

        return $this->configName;
    }
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
