<?php
/**
 * Database.php
 * @package    WPLIBS
 * @subpackage MONGODB
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:07
 */

namespace wplibs\database\mongo;

use Packaged\Config\Provider\ConfigSection;
use wplibs\database\iDatabase;
use wplibs\database\iSelection;
use wplibs\database\iSelectStrategy;
use wplibs\exception\DatabaseException;

/**
 * class Database
 * @package    WPLIBS
 * @subpackage MONGODB
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:07
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
     * @param ConfigSection $dbConfig
     *
     * @return Database
     */
    public function __construct( ConfigSection $dbConfig ) {

        parent::__construct( new \MongoClient(), $dbConfig->getItem( 'dbname' ) );
        self::$dbConfig = $dbConfig;
        self::$queryCount -= 2;
        $this->configName = md5( serialize( $dbConfig ) );
    }

    /**
     * @param ConfigSection $dbConfig
     *
     * @return mixed
     */
    public static function getNamedInstance( ConfigSection $dbConfig ) {

        $configName = md5( serialize( $dbConfig ) );
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
     *
     * @throws \wplibs\exception\DatabaseException
     * @return mixed
     */
    public function query( $sql ) {

        if ( !( $sql instanceof Selection ) ) {
            throw new DatabaseException( "Invalid query" );
        }
        self::$lastQuery = $sql;
        self::$queryCount++;

        $query = $sql->getQuery();
        $collection = $this->selectCollection( $query[ 0 ] );
        $callName = $query[ 1 ];
        $query = $query[ 2 ];

        if ( isset( $query[ 0 ] ) ) {
            $res = $collection->$callName( ...$query );
        }
        else {
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
     *
     * @return void
     */
    public function prepareQuery( iSelection $sql, ...$params ) {
        // TODO: Implement prepare() method.
    }

    /**
     * select
     *
     * @param iSelectStrategy $selector
     *
     * @return iSelection
     */
    public function select( iSelectStrategy $selector = null ) {

        return ( new Selection() )->select( $selector );
    }

    /**
     * Desc
     * @return iSelection^
     */
    public function desc() {

        return ( new Selection() )->desc();
    }

    /**
     * update
     * @return \wplibs\database\iSelection
     * @internal param \wplibs\database\iSelectStrategy $selector
     */
    public function update() {

        return ( new Selection() )->update();
    }

    /**
     * create
     *
     * @param string $additionalInfo
     *
     * @return Selection
     */
    public function create( $additionalInfo = '' ) {

        return ( new Selection() )->create( $additionalInfo );
    }

    /**
     * insert
     * @return iSelection
     */
    public function insert() {

        return ( new Selection() )->insert();
    }

    /**
     * replace
     * @return iSelection
     */
    public function replace() {

        return ( new Selection() )->replace();
    }

    /**
     * delete
     * @return iSelection
     */
    public function delete() {

        return ( new Selection() )->delete();
    }
}
