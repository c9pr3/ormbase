<?php
/**
 * Database.php
 *
 * @package WPLIBS
 * @subpackage MONGODB
 * @author Christian Senkowski <c.senkowski@kon.de>
 * @since 20150106 14:07
 */

namespace wplibs\database\mongo;

use wplibs\config\Config;
use wplibs\database\iDatabase;
use wplibs\database\iSelection;
use wplibs\database\iSelectStrategy;

/**
 * class Database
 *
 * @package WPLIBS
 * @subpackage MONGODB
 * @author Christian Senkowski <c.senkowski@kon.de>
 * @since 20150106 14:07
 */
class Database extends \MongoDB implements iDatabase {

	/**
	 * @var array
	 */
	private static $instances = [ ];

	/**
	 * @var int
	 */
	private static $queryCount = 0;
	/**
	 * @var string
	 */
	private static $lastQuery = '';
	/**
	 * @var array
	 */
	private static $queries = [ ];
	/**
	 * @var null
	 */
	private static $dbConfig = null;

	private $configName = '';

	/**
	 * Construct
	 *
	 * @param \wplibs\config\Config $dbConfig
	 * @return Database
	 */
	public function __construct( Config $dbConfig ) {
		parent::__construct( new \MongoClient(), $dbConfig->getSection( 'database' )->getValue( 'dbname' ) );
		self::$dbConfig = $dbConfig;
		self::$queryCount -= 2;
		$this->configName = $dbConfig->getConfigName();
	}

	/**
	 * @param \wplibs\config\Config $dbConfig
	 * @return mixed
	 */
	public static function getNamedInstance( Config $dbConfig ) {
		$configName = $dbConfig->getConfigName();
		if ( !isset( self::$instances[ $configName ] ) ) {
			self::$instances[ $configName ] = new self( $dbConfig );
		}

		return self::$instances[ $configName ];
	}

	/**
	 * @return int
	 */
	public static function getQueryCount() {
		return self::$queryCount;
	}

	/**
	 * @return array
	 */
	public static function getQueries() {
		return self::$queries;
	}

	/**
	 * Query
	 *
	 * @param $sql
	 * @throws \DatabaseException
	 * @return mixed
	 */
	public function query( $sql ) {

		if ( !( $sql instanceof Selection ) ) {
			throw new \wplibs\exception\DatabaseException( "Invalid query" );
		}
		self::$lastQuery = $sql;
		self::$queryCount++;

		$query = $sql->getQuery();
		$collection = $this->selectCollection( $query[ 0 ] );
		$callName = $query[ 1 ];
		$query = $query[ 2 ];

		if ( isset( $query[ 0 ] ) ) {
			$res = $collection->$callName( ...$query );
		} else {
			$res = $collection->$callName( $query );
		}

		return $res;
	}

	/**
	 * @return \MongoCollection
	 */
	public function getConfigName() {
		return $this->configName;
	}

	/**
	 * prepare
	 *
	 * @param mixed $sql
	 * @param ... $params
	 * @return void
	 */
	public function prepareQuery( iSelection $sql, ...$params ) {
		// TODO: Implement prepare() method.
	}

	/**
	 * select
	 *
	 * @param iSelectStrategy $selector
	 * @return iSelection
	 */
	public function select( iSelectStrategy $selector = null ) {
		return ( new Selection() )->select( $selector );
	}

	/**
	 * create
	 *
	 * @param string $additionalInfo
	 * @return Selection
	 */
	public function create( $additionalInfo = '' ) {
		return ( new Selection() )->create( $additionalInfo );
	}

	/**
	 * insert
	 *
	 * @return iSelection
	 */
	public function insert() {
		return ( new Selection() )->insert();
	}

	/**
	 * replace
	 *
	 * @return iSelection
	 */
	public function replace() {
		return ( new Selection() )->replace();
	}

	/**
	 * delete
	 *
	 * @return iSelection
	 */
	public function delete() {
		return ( new Selection() )->delete();
	}
}
