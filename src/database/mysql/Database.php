<?php
/**
 * class.Database.php
 *
 * @package    WPLIBS
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:08
 */

namespace wplibs\database\mysql;

use wplibs\config\Config;
use wplibs\database\iDatabase;
use wplibs\database\iSelection;
use wplibs\database\iSelectStrategy;
use wplibs\exception\DatabaseException;

/**
 * class Database
 *
 * @package    WPLIBS
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:08
 */
class Database extends \MySQLi implements iDatabase {

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
     * @throws \wplibs\exception\ConfigException
     * @throws DatabaseException
     * @internal param $Config
     * @return Database
     */
    public function __construct( Config $dbConfig ) {

        @parent::__construct( $dbConfig->getValue( 'server' ),
                              $dbConfig->getValue( 'username' ),
                              $dbConfig->getValue( 'password' ),
                              $dbConfig->getValue( 'dbName' ),
                              $dbConfig->getValue( 'port' )
        );
        self::$dbConfig = $dbConfig;

        $this->debug = $dbConfig->getValue( 'debugsql' );

        if ( $this->connect_error ) {
            throw new DatabaseException( "Database connection failed: '" . $this->connect_error . "'" );
        }

        $this->query( 'SET NAMES UTF8' );
        $this->query( 'SET CHARACTER SET UTF8' );
        $this->set_charset( 'UTF8' );
        self::$queryCount -= 2;

        $this->configName = $dbConfig->getConfigName();
    }

    /**
     * Query
     *
     * @param string $sql
     *
     * @return bool|\mysqli_result
     * @throws \wplibs\exception\ConfigException
     * @throws \wplibs\exception\DatabaseException
     * @internal param $string
     */
    final public function query( $sql ) {

        if ( !$this->ping() ) {
            $this->real_connect( self::$dbConfig->getValue( 'server' ),
                                 self::$dbConfig->getValue( 'username' ),
                                 self::$dbConfig->getValue( 'password' ),
                                 self::$dbConfig->getValue( 'dbName' ),
                                 self::$dbConfig->getValue( 'port' )
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

        return $this->store_result();
    }

    /**
     * prepare
     *
     * @param mixed $sql
     * @param       $params
     *
     * @throws \wplibs\exception\ConfigException
     * @throws \wplibs\exception\DatabaseException
     * @internal param $ ... $params
     * @return \mysqli_result
     */
    final public function prepareQuery( iSelection $sql, ...$params ) {

        if ( !$params ) {
            return $this->query( $sql );
        }

        if ( !$this->ping() ) {
            $this->real_connect( self::$dbConfig->getValue( 'server' ),
                                 self::$dbConfig->getValue( 'username' ),
                                 self::$dbConfig->getValue( 'password' ),
                                 self::$dbConfig->getValue( 'dbName' ),
                                 self::$dbConfig->getValue( 'port' )
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
            $sql             = preg_replace( array_fill( 0, count( $params ), '/\?/' ), $params, $sql, 1 );
            self::$lastQuery = $sql;
            self::$queries[ ] = $sql;
        }

        return $result;
    }

    /**
     * Get an instance
     *
     * @param \wplibs\config\Config $dbConfig
     *
     * @return Database
     */
    public static function getNamedInstance( Config $dbConfig ) {

        $configName = $dbConfig->getConfigName();
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
     * @return \wplibs\database\iSelection
     */
    public function create( $additionalInfo = '' ) {

        return ( new Selection() )->create( $additionalInfo );
    }

    /**
     * Select
     *
     * @param \wplibs\database\iSelectStrategy
     *
     * @return iSelection
     */
    public function select( iSelectStrategy $selector = null ) {

        return ( new Selection() )->select( $selector );
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
     * @return Selection
     */
    public function replace() {

        return ( new Selection() )->replace();
    }

    /**
     * Delete
     *
     * @return iSelection
     */
    public function delete() {

        return ( new Selection() )->delete();
    }

    /**
     * Get full query count (Debug only)
     *
     * @return int
     */
    final public static function getQueryCount() {

        return self::$queryCount;
    }

    /**
     * Get all queries done (Debug only)
     *
     * @return string[]
     */
    final public static function getQueries() {

        return self::$queries;
    }

    /**
     * Get config name
     *
     * @return string
     */
    final public function getConfigName() {

        return $this->configName;
    }

    /**
     * Update
     *
     * @return iSelection
     */
    public function update() {

        return ( new Selection() )->update();
    }
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
