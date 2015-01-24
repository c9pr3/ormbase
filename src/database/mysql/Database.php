<?php
/**
 * class.Database.php
 * @package    ecsco\ormbase
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:08
 */

namespace ecsco\ormbase\database\mysql;

use ecsco\ormbase\config\Config;
use ecsco\ormbase\database\DatabaseInterface;
use ecsco\ormbase\database\SelectionInterface;
use ecsco\ormbase\database\SelectStrategyInterface;
use ecsco\ormbase\exception\DatabaseException;
use ecsco\ormbase\traits\CallTrait;
use ecsco\ormbase\traits\GetTrait;
use ecsco\ormbase\traits\NoCloneTrait;

/**
 * class Database
 * @package    ecsco\ormbase
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:08
 */
class Database extends \MySQLi implements DatabaseInterface {

    use GetTrait;
    use CallTrait;
    use NoCloneTrait;

    private static $instances = [ ];

    private static $queryCount = 0;
    private static $lastQuery  = '';
    private static $queries    = [ ];
    private static $dbConfig   = null;

    private $configName = '';
    private $debug      = false;

    /**
     * Create new Database
     *
     * @param Config $dbConfig
     *
*@throws \ecsco\ormbase\exception\ConfigException
     * @throws DatabaseException
     * @return Database
     */
    public function __construct( Config $dbConfig ) {

        @parent::__construct( $dbConfig->getItem( 'server' ),
                              $dbConfig->getItem( 'username' ),
                              $dbConfig->getItem( 'password' ),
                              $dbConfig->getItem( 'dbname' ),
                              $dbConfig->getItem( 'port' )
        );
        self::$dbConfig = $dbConfig;

        $this->debug = $dbConfig->getItem( 'debugsql' );

        if ( $this->connect_error ) {
            throw new DatabaseException( "Database connection failed: '" . $this->connect_error . "'" );
        }

        $this->query( 'SET NAMES UTF8' );
        $this->query( 'SET CHARACTER SET UTF8' );
        $this->set_charset( 'UTF8' );
        self::$queryCount -= 2;

        $this->configName = md5( serialize( $dbConfig ) );
    }

    /**
     * Query
     *
     * @param string $sql
     *
     * @return array|\mysqli_result
     * @throws \ecsco\ormbase\exception\ConfigException
     * @throws \ecsco\ormbase\exception\DatabaseException
     */
    final public function query( $sql ) {

        if ( !$this->ping() ) {
            $this->real_connect( self::$dbConfig->getItem( 'server' ),
                                 self::$dbConfig->getItem( 'username' ),
                                 self::$dbConfig->getItem( 'password' ),
                                 self::$dbConfig->getItem( 'dbname' ),
                                 self::$dbConfig->getItem( 'port' )
            );
        }

        if ( !$this->real_query( $sql ) ) {
            throw new DatabaseException( $this->error );
        }

        self::$queryCount++;
        if ( $this->debug ) {
            self::$lastQuery = $sql;
            self::$queries[ ] = $sql;
        }

        $result = $this->store_result();

        if ( $result instanceof \mysqli_result ) {
            return $result->fetch_all( MYSQLI_ASSOC );
        }

        return [ ];
    }

    /**
     * prepare
     *
     * @param SelectionInterface $sql
     * @param       $params
     *
     * @throws \ecsco\ormbase\exception\ConfigException
     * @throws \ecsco\ormbase\exception\DatabaseException
     * @return \mysqli_result
     */
    final public function prepareQuery( SelectionInterface $sql, ...$params ) {

        if ( !$params ) {
            return $this->query( $sql );
        }

        if ( !$this->ping() ) {
            $this->real_connect( self::$dbConfig->getItem( 'server' ),
                                 self::$dbConfig->getItem( 'username' ),
                                 self::$dbConfig->getItem( 'password' ),
                                 self::$dbConfig->getItem( 'dbName' ),
                                 self::$dbConfig->getItem( 'port' )
            );
        }

        $stmt = parent::prepare( $sql );
        if ( !$stmt && $this->error ) {
            throw new DatabaseException( $this->error );
        }

        if ( $stmt->error || !$stmt->bind_param( ...$params ) ) {
            /** @noinspection PhpUndefinedFieldInspection */
            throw new DatabaseException( $stmt->error . ' ' . var_export( $stmt->error_list, true ) );
        }

        $stmt->execute();
        if ( $stmt->error ) {
            /** @noinspection PhpUndefinedFieldInspection */
            throw new DatabaseException( $stmt->error . ' ' . var_export( $stmt->error_list, true ) );
        }

        $result = $stmt->get_result();
        if ( !$result && $this->error ) {
            throw new DatabaseException( $this->error );
        }

        $stmt->free_result();
        $stmt->close();

        self::$queryCount++;
        if ( $this->debug ) {
            array_shift( $params );
            $sql = preg_replace( array_fill( 0, count( $params ), '/\?/' ), $params, $sql, 1 );
            self::$lastQuery = $sql;
            self::$queries[ ] = $sql;
        }

        if ( $result instanceof \mysqli_result ) {
            return $result->fetch_all( MYSQLI_ASSOC );
        }

        return [ ];
    }

    /**
     * Get an instance
     *
     * @param Config $dbConfig

     *
*@return Database
     */
    public static function getNamedInstance( Config $dbConfig ) {

        $configName = md5( serialize( $dbConfig ) );
        if ( !isset( self::$instances[ $configName ] ) ) {
            self::$instances[ $configName ] = new self( $dbConfig );
        }

        return self::$instances[ $configName ];
    }

    /**
     * create
     *
     * @param string $additionalInfo
     *
     * @return \ecsco\ormbase\database\SelectionInterface
     */
    public function create( $additionalInfo = '' ) {

        return ( new Selection() )->create( $additionalInfo );
    }

    /**
     * Select
     *
     * @param \ecsco\ormbase\database\SelectStrategyInterface
     *
     * @return SelectionInterface
     */
    public function select( SelectStrategyInterface $selector = null ) {

        return ( new Selection() )->select( $selector );
    }

    /**
     * Desc
     * @return SelectionInterface
     */
    public function desc() {

        return ( new Selection() )->desc();
    }

    /**
     * insert
     * @return SelectionInterface
     */
    public function insert() {

        return ( new Selection() )->insert();
    }

    /**
     * replace
     * @return SelectionInterface
     */
    public function replace() {

        return ( new Selection() )->replace();
    }

    /**
     * Delete
     * @return SelectionInterface
     */
    public function delete() {

        return ( new Selection() )->delete();
    }

    /**
     * Get full query count (Debug only)
     * @return int
     */
    final public static function getQueryCount() {

        return self::$queryCount;
    }

    /**
     * Get all queries done (Debug only)
     * @return string[]
     */
    final public static function getQueries() {

        return self::$queries;
    }

    /**
     * Get config name
     * @return string
     */
    final public function getConfigName() {

        return $this->configName;
    }

    /**
     * Update
     * @return SelectionInterface
     */
    public function update() {

        return ( new Selection() )->update();
    }
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
