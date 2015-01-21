<?php
/**
 * Selection.php
 * @package    ecsco\ormbase
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:08
 */

namespace ecsco\ormbase\database\mongo;

use ecsco\ormbase\database\SelectionInterface;
use ecsco\ormbase\database\SelectStrategyInterface;
use ecsco\ormbase\traits\CallTrait;
use ecsco\ormbase\traits\GetTrait;

/**
 * Selection
 * @package    ecsco\ormbase
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:08
 */
class Selection implements SelectionInterface {

    private $tables     = [ ];
    private $where      = [ ];
    private $sort       = [ ];
    private $limit      = [ ];
    private $set        = [ ];
    private $createInfo = [ ];

    private $mode = ''; # can be select, delete, insert ...

    private $query = [ ];

    use CallTrait;
    use GetTrait;

    /**
     * __construct
     * @return Selection
     */
    public function __construct() {
    }

    /**
     * select
     *
     * @param SelectStrategyInterface $selector
     *
     * @return Selection
     */
    public function select( SelectStrategyInterface $selector = null ) {

        if ( !$this->mode ) {
            $this->mode = 'find';
        }

        return $this;
    }

    /**
     * Desc
     * @return Selection
     */
    public function desc() {

        if ( !$this->mode ) {
            $this->mode = 'desc';
        }

        return $this;
    }

    /**
     * create
     *
     * @param string $additionalInfo
     *
     * @return Selection
     */
    public function create( $additionalInfo = '' ) {

        if ( !$this->mode ) {
            $this->mode = 'create';
        }
        $this->createInfo[ ] = $additionalInfo;

        return $this;
    }

    /**
     * insert
     * @return Selection
     */
    public function insert() {

        if ( !$this->mode ) {
            $this->mode = 'insert';
        }

        return $this;
    }

    /**
     * replace
     * @return Selection
     */
    public function replace() {

        if ( !$this->mode ) {
            $this->mode = 'findAndModify';
        }

        return $this;
    }

    /**
     * delete
     * @return Selection
     */
    public function delete() {

        if ( !$this->mode ) {
            $this->mode = 'delete';
        }

        return $this;
    }

    /**
     * from
     *
     * @param string $tableName
     * @param string $alias
     *
     * @return Selection
     */
    public function from( $tableName, $alias = '' ) {

        if ( $alias ) {
            $tableName = [ $tableName, $alias ];
        }
        $this->tables[ ] = $tableName;

        return $this;
    }

    /**
     * into
     *
     * @param mixed $tableName
     *
     * @return Selection
     */
    public function into( $tableName ) {

        $this->tables[ ] = $tableName;

        return $this;
    }

    /**
     * table
     *
     * @param mixed  $tableName
     * @param string $alias
     * @param string $term
     *
     * @return Selection
     */
    public function table( $tableName, $alias = '', $term = '' ) {

        if ( $alias || $term ) {
            $tableName = [ $tableName, $alias, $term ];
        }
        $this->tables[ ] = $tableName;

        return $this;
    }

    /**
     * set
     *
     * @param mixed $fieldName
     * @param mixed $fieldValue
     *
     * @return Selection
     */
    public function set( $fieldName, $fieldValue ) {

        $this->set[ $fieldName ] = $fieldValue;

        return $this;
    }

    /**
     * where
     *
     * @param mixed $fieldName
     * @param mixed $operator
     * @param mixed $fieldValue
     *
     * @return Selection
     */
    public function where( $fieldName, $operator, $fieldValue ) {

        $this->where[ ] = [ $fieldName, $operator, $fieldValue ];

        return $this;
    }

    /**
     * sort
     *
     * @param mixed  $fieldName
     * @param string $ascDesc
     *
     * @return Selection
     */
    public function sort( $fieldName, $ascDesc = 'ASC' ) {

        $this->sort[ ] = " $fieldName " . strtoupper( $ascDesc ) . " ";

        return $this;
    }

    /**
     * limit
     *
     * @param int $count
     *
     * @return Selection
     */
    public function limit( $count = 1 ) {

        $this->limit = $count;

        return $this;
    }

    /**
     * update
     * @return Selection
     */
    public function update() {

        if ( !$this->mode ) {
            $this->mode = 'update';
        }

        return $this;
    }

    /**
     * buildQuery
     * @return void
     */
    protected function buildQueryFind() {

        if ( !$this->where || !$this->tables ) {
            return;
        }

        if ( $this->where ) {
            foreach ( $this->where AS list( $k, $o, $v ) ) {
                if ( $o == '=' ) {
                    $this->query[ $k ] = $v;
                }
                else {
                    $this->query[ $k ] = [ $o => $v ];
                }
            }
        }
    }

    /**
     * buildQueryInsert
     * @return void
     */
    protected function buildQueryInsert() {

        if ( !$this->tables || !$this->set ) {
            return;
        }

        $this->query = $this->set;
    }

    /**
     * buildQueryUpdate
     * @return void
     */
    protected function buildQueryUpdate() {

        if ( !$this->tables || !$this->set ) {
            return;
        }

    }

    /**
     * buildQueryDesc
     * @return Selection
     */
    protected function buildQueryDesc() {

        if ( !$this->tables ) {
            return;
        }

    }

    /**
     * buildQueryReplace
     * @return void
     */
    protected function buildQueryFindAndModify() {

        if ( !$this->tables ) {
            return;
        }

        $where = [ ];
        foreach ( $this->where AS list( $key, $operator, $value ) ) {
            switch ( $operator ) {
                case '=':
                default:
                    $where[ $key ] = $value;
                    break;
            }
        }
        $set = [ ];
        foreach ( $this->set AS $key => $value ) {
            $set[ $key ] = $value;
        }

        $this->query = [ 0 => $where, 1 => [ '$set' => $set ], 2 => null, 3 => [ 'upsert' => true ] ];
    }

    /**
     * buildQueryDelete
     * @return void
     */
    protected function buildQueryDelete() {

        if ( !$this->tables ) {
            return;
        }

    }

    /**
     * buildQueryView
     * @return \string[]
     * @throws \Exception
     */
    protected function buildQueryView() {

        throw new \Exception( 'invalid' );
    }

    /**
     * getQuery
     * @return string
     */
    public function getQuery() {

        $strName = "buildQuery" . ucfirst( $this->mode );

        $this->$strName();

        $return = [ $this->tables[ 0 ], $this->mode, $this->query ];

        return $return;
    }


    /**
     * getQueryParams
     * @return \string[]
     * @throws \Exception
     */
    public function getQueryParams() {

        throw new \Exception( 'invalid' );
    }


    /**
     * duplicateKey
     *
     * @param mixed $fieldName
     * @param mixed $fieldValue
     *
     * @throws \Exception
     * @return \ecsco\ormbase\database\SelectionInterface|void
     */
    public function duplicateKey( $fieldName, $fieldValue ) {

        throw new \Exception( 'invalid, no ' . __METHOD__ . " allowd, called with $fieldName/$fieldValue" );
    }


    /**
     * view
     *
     * @param mixed $viewName
     *
     * @return \ecsco\ormbase\database\SelectionInterface|void
     * @throws \Exception
     */
    public function view( $viewName ) {

        throw new \Exception( 'invalid, no ' . __METHOD__ . " allowed, called with $viewName" );
    }


    /**
     * unparameterize
     * @return \ecsco\ormbase\database\mongo\Selection
     * @throws \Exception
     */
    public function unparameterize() {

        throw new \Exception( 'invalid' );
    }


    /**
     * __toString
     * @return string
     */
    public function __toString() {

        if ( !$this->query ) {
            $this->getQuery();
        }

        return $this->query;
    }

}
