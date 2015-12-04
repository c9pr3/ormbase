<?php
/**
 * Database.php
 * @package    ecsco\ormbase
 * @subpackage MONGODB
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:07
 */

declare(strict_types=1);

namespace ecsco\ormbase\database\mongo;

use ecsco\ormbase\config\Config;
use ecsco\ormbase\database\DatabaseInterface;
use ecsco\ormbase\database\SelectionInterface;
use ecsco\ormbase\database\SelectStrategyInterface;
use ecsco\ormbase\exception\DatabaseException;
use ecsco\ormbase\traits\CallTrait;
use ecsco\ormbase\traits\GetTrait;
use ecsco\ormbase\traits\NoCloneTrait;
use ecsco\ormbase\traits\SingletonTrait;

/**
 * class Database
 * @package    ecsco\ormbase
 * @subpackage MONGODB
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:07
 */
class Database extends \MongoDB implements DatabaseInterface {

    use GetTrait;
    use CallTrait;
    use NoCloneTrait;
    use SingletonTrait;

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
     * @var Config
     */
    private static $dbConfig = null;
    /**
     * @var string
     */
    private $configName = '';

    /**
     * Construct
     *
     * @param Config $dbConfig
     *
     * @throws \Exception
     */
    public function __construct( Config $dbConfig ) {

        parent::__construct( new \MongoClient(), $dbConfig->getItem( 'dbname' ) );
        self::$dbConfig = $dbConfig;
        self::$queryCount -= 2;
        $this->configName = md5( serialize( $dbConfig ) );
    }


    /**
     * @return int
     */
    public static function getQueryCount(): int {

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
     * @throws \ecsco\ormbase\exception\DatabaseException
     * @return mixed
     * @TODO revalidate code. isset query[0] vs selectCollection
     */
    public function query( string $sql ) {

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
     * @return string
     */
    public function getConfigName(): string {

        return $this->configName;
    }

    /**
     * prepare
     *
     * @param SelectionInterface $sql
     * @param ... $params
     *
     * @return void
     */
    public function prepareQuery( SelectionInterface $sql, string ...$params ) {
        // TODO: Implement prepare() method.
    }

    /**
     * select
     *
     * @param SelectStrategyInterface $selector
     *
     * @return SelectionInterface
     */
    public function select( SelectStrategyInterface $selector = null ): SelectionInterface {

        return ( new Selection() )->select( $selector );
    }

    /**
     * Desc
     * @return SelectionInterface
     */
    public function desc(): SelectionInterface {

        return ( new Selection() )->desc();
    }

    /**
     * update
     * @return SelectionInterface
     */
    public function update(): SelectionInterface {

        return ( new Selection() )->update();
    }

    /**
     * create
     *
     * @param string $additionalInfo
     *
     * @return SelectionInterface
     */
    public function create( string $additionalInfo = '' ): SelectionInterface {

        return ( new Selection() )->create( $additionalInfo );
    }

    /**
     * insert
     * @return SelectionInterface
     */
    public function insert(): SelectionInterface {

        return ( new Selection() )->insert();
    }

    /**
     * replace
     * @return SelectionInterface
     */
    public function replace(): SelectionInterface {

        return ( new Selection() )->replace();
    }

    /**
     * delete
     * @return SelectionInterface
     */
    public function delete(): SelectionInterface {

        return ( new Selection() )->delete();
    }
}
