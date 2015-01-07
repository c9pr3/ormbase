<?php
/**
 * class.aContainer.php
 *
 * @package WPLIBS
 * @subpackage DBINTERFACE
 * @author Christian Senkowski <cs@e-cs.co>
 * @since 20150106 14:08
 */

namespace wplibs\dbinterface;

use wplibs\cache\Cache;
use wplibs\config\Config;
use wplibs\database\iSelection;

/**
 * class aContainer
 *
 * @package WPLIBS
 * @subpackage DBINTERFACE
 * @author Christian Senkowski <cs@e-cs.co>
 * @since 20150106 14:08
 */
abstract class aContainer {

	const OBJECT_NAME = '';

	private static $dbConnections = [ ];
	protected $basicFields = [ ];
	private $configName = '';

	/**
	 * Construct
	 *
	 * @param string
	 * @return aContainer
	 */
	protected function __construct( $name ) {
		if ( Config::$db !== null ) {
			self::$dbConnections[ $name ] = Config::$db;
		}
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
		$row = self::descObject( $class );

		/** @noinspection PhpUndefinedFieldInspection */
		$objectName = $class::OBJECT_NAME;
		$objectName = '\wplibs\dbinterface\\' . $objectName;

		$obj = new $objectName( $row, $this->getDatabase() );

		return $obj;
	}

	/**
	 * Desc table of object
	 *
	 * @param string
	 * @return string[]
	 */
	public static function descObject( $objectName ) {

		$res = Config::$db->query( sprintf( 'DESC %s', $objectName::TABLE_NAME ) );
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
	 * @return array
	 */
	private function getFromCache( $objectName, $sql, $params ) {
		$sql = $sql . ' - ' . implode( '', $params );
		$fullObjectName = '\wplibs\dbinterface\\' . $objectName;
		if ( is_subclass_of( $fullObjectName, '\wplibs\dbinterface\iCachable' ) ) {
			/** @noinspection PhpUndefinedFieldInspection */
			if ( Cache::has( $fullObjectName::CACHE_TYPE, $sql ) ) {
				/** @noinspection PhpUndefinedFieldInspection */
				return Cache::get( $fullObjectName::CACHE_TYPE, $sql );
			} else {
				/** @noinspection PhpUndefinedFieldInspection */
				Cache::$stats[ 'stats' ][ 'hasnot' ][ ] = $objectName . ',' . $fullObjectName::CACHE_TYPE . ',' . $sql;
			}
		} else {
			Cache::$stats[ 'stats' ][ 'notfound' ][ ] = $objectName;
		}

		return null;
	}

	/**
	 * Make object
	 *
	 * @param array $row
	 * @param       string []
	 * @return \wplibs\dbinterface\aObject
	 */
	private function makeObject( array $row, $objectName ) {
		$objectName = '\wplibs\dbinterface\\' . $objectName;
		$obj = $objectName::Factory( $row, $this->getDatabase() );
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
	 * @return void
	 */
	private function addToCache( $objectName, $sql, $params, $retVal ) {
		$sql = $sql . ' - ' . implode( '', $params );
		$fullObjectName = '\wplibs\dbinterface\\' . $objectName;
		if ( is_subclass_of( $fullObjectName, '\wplibs\dbinterface\iCachable' ) ) {
			/** @noinspection PhpUndefinedFieldInspection */
			Cache::$stats[ 'stats' ][ 'added' ][ ] = $objectName . ',' . $fullObjectName::CACHE_TYPE . ',' . $sql;
			/** @noinspection PhpUndefinedFieldInspection */
			Cache::add( $fullObjectName::CACHE_TYPE, $sql, $retVal );
		}
	}

	/**
	 * makePreparedObject
	 *
	 * @param mixed $query
	 * @param mixed $objectName
	 * @param ... $params
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
