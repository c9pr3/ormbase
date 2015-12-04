<?php
/**
 * Selection.php
 * @package    ecsco\ormbase
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:08
 */

declare(strict_types=1);

namespace ecsco\ormbase\database\mysql;

use ecsco\ormbase\database\SelectionInterface;
use ecsco\ormbase\database\SelectStrategyInterface;
use ecsco\ormbase\exception\DatabaseException;
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

    use CallTrait;
    use GetTrait;

    private $select       = [ ];
    private $tables       = [ ];
    private $where        = [ ];
    private $sort         = [ ];
    private $limit        = [ ];
    private $set          = [ ];
    private $createInfo   = [ ];
    private $duplicateKey = [ ];

    private $mode = ''; # can be select, delete, insert ...

    private $query  = [ ];
    private $params = [ ];


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
    public function select( SelectStrategyInterface $selector = null ): SelectionInterface {

        if ( !$this->mode ) {
            $this->mode = 'select';
        }

        if ( $selector instanceof SelectStrategyInterface ) {
            $select = $selector->get();
            foreach ( $select AS $k ) {
                $this->select[ $k ] = true;
            }
        }
        else {
            $this->select[ '*' ] = true;
        }

        return $this;
    }

    /**
     * Desc
     * @return Selection
     */
    public function desc(): SelectionInterface {

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
    public function create( string $additionalInfo = '' ): SelectionInterface {

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
    public function insert(): SelectionInterface {

        if ( !$this->mode ) {
            $this->mode = 'insert';
        }

        return $this;
    }

    /**
     * replace
     * @return Selection
     */
    public function replace(): SelectionInterface {

        if ( !$this->mode ) {
            $this->mode = 'replace';
        }

        return $this;
    }

    public function delete(): SelectionInterface {

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
    public function from( string $tableName, string $alias = '' ): SelectionInterface {

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
    public function into( string $tableName ): SelectionInterface {

        $this->tables[ ] = $tableName;

        return $this;
    }

    /**
     * View
     *
     * @param mixed $viewName
     *
     * @return Selection
     */
    public function view( string $viewName ): SelectionInterface {

        $this->tables[ ] = $viewName;
        $this->mode = 'view';

        return $this;
    }

    /**
     * Table
     *
     * @param mixed  $tableName
     * @param string $alias
     * @param string $term
     *
     * @return Selection
     */
    public function table( string $tableName, string $alias = '', string $term = '' ): SelectionInterface {

        if ( $alias || $term ) {
            $tableName = [ $tableName, $alias, $term ];
        }
        $this->tables[ ] = $tableName;

        return $this;
    }

    /**
     * set
     *
     * @param string $fieldName
     * @param mixed $fieldValue
     *
     * @return Selection
     */
    public function set( string $fieldName, $fieldValue ): SelectionInterface {

        list( $fieldType, $fieldValue, $nameTag ) = $this->prepareValue( $fieldValue );
        if ( $fieldType !== false ) {
            $this->set[ ] = "`$fieldName` = $nameTag";
            $this->params[ 'set' ][ ] = [ $fieldType, $fieldValue ];
        }

        return $this;
    }

    /**
     * prepareValue
     *
     * @param mixed $value
     *
     * @throws \ecsco\ormbase\exception\DatabaseException
     * @return string[]
     */
    private function prepareValue( $value ): array {

        $type = 's';

        #
        # for example: POINTFROMTEXT("POINT(1.1 2.2)")
        #
        if ( preg_match( '/^.*\(.*\).*$/', $value ) ) {
            $func = preg_replace( '/^(.*?)\(.*$/', '\1', $value );
            $val = preg_replace( '/^.*?\(["\']?(.*)["\']?\)$/', '\1', $value );

            $return = [ 's', $val, "$func(?)" ];
            #
            # special for IN(n,m,n2,...)
            #
            if ( strstr( $val, ',' ) ) {
                $val = explode( ',', $val );
                $qms = str_repeat( '?,', count( $val ) );
                $qms = substr( $qms, 0, -1 );

                $return = [ str_repeat( 's', count( $val ) ), $val, "$func($qms)" ];
            }

            return $return;

        }
        #
        # int or bool
        #
        elseif ( is_int( $value ) || is_bool( $value ) ) {
            $value = (int)$value;
            $type = 'i';
        }
        #
        # float and doubles
        #
        elseif ( is_double( $value ) || is_float( $value ) ) {
            $type = 'd';
        }
        #
        # objects
        #
        elseif ( is_object( $value ) ) {
            throw new DatabaseException( "Cannot insert object: " . var_export( $value, true ) );
        }
        #
        # null-values will be sorted out
        #
        elseif ( $value === null ) {
            $type = false;
            $value = false;
        }

        return [ $type, $value, '?' ];
    }

    /**
     * where
     *
     * @param mixed $fieldName
     * @param mixed $operator
     * @param mixed $fieldValue
     *
     * @throws \ecsco\ormbase\exception\DatabaseException
     * @internal param string $where
     * @return Selection
     */
    public function where( string $fieldName, string $operator, $fieldValue ): SelectionInterface {

        list( $fieldType, $fieldValue, $nameTag ) = $this->prepareValue( $fieldValue );

        if ( strstr( $fieldName, '.' ) && !strstr( $fieldName, '(' ) ) {
            $fieldName = preg_replace( '/^(.*?)\.(.*)$/', '\1.`\2`', $fieldName );
            $this->where[ ] = "$fieldName $operator $nameTag";
        }
        else {
            if ( strstr( $fieldName, '(' ) ) {
                $this->where[ ] = "$fieldName $operator $nameTag";
            }
            else {
                $this->where[ ] = "`$fieldName` $operator $nameTag";
            }
        }

        if ( is_array( $fieldValue ) ) {
            $this->params[ 'where' ][ ] = $this->arrayFlatten( [ $fieldType, $fieldValue ] );
        }
        else {
            $this->params[ 'where' ][ ] = [ $fieldType, $fieldValue ];
        }

        return $this;
    }

    /**
     * array_flatten
     *
     * @param mixed $array
     *
     * @return array
     */
    private function arrayFlatten( array $array ): array {

        $result = [ ];

        foreach ( $array as $element ) {
            if ( is_array( $element ) ) {
                $result = array_merge( $result, $this->arrayFlatten( $element ) );
            }
            else {
                array_push( $result, $element );
            }
        }

        return $result;
    }

    /**
     * sort
     *
     * @param mixed  $fieldName
     * @param string $ascDesc
     *
     * @return Selection
     */
    public function sort( string $fieldName, string $ascDesc = 'ASC' ): SelectionInterface {

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
    public function limit( int $count = 1 ): SelectionInterface {

        $this->limit = $count;

        return $this;
    }

    /**
     * duplicateKey
     *
     * @param mixed $fieldName
     * @param mixed $fieldValue
     *
     * @return Selection
     */
    public function duplicateKey( string $fieldName, $fieldValue ): SelectionInterface {

        $this->duplicateKey[ ] = "`$fieldName` = ?";

        list( $fieldType, $fieldValue ) = $this->prepareValue( $fieldValue );

        $this->params[ 'duplicatekey' ][ ] = [ $fieldType, $fieldValue ];

        return $this;
    }

    /**
     * unparameterize
     * @return Selection
     */
    public function unparameterize(): SelectionInterface {

        if ( !$this->query ) {
            return false;
        }

        $params = $this->getQueryParams();
        array_shift( $params );

        $fromParams = array_fill( 0, count( $params ), '/\?/' );
        $this->query = preg_replace( $fromParams, $params, $this->query, 1 );

        return $this;
    }

    /**
     * getQueryParams
     * @return string[]
     */
    public function getQueryParams(): array {

        $params = [ 0 => '' ];

        if ( isset( $this->params[ 'set' ] ) ) {
            foreach ( $this->params[ 'set' ] AS $values ) {
                $params[ 0 ] .= $values[ 0 ];
                array_shift( $values );
                array_push( $params, ...$values );
            }
        }
        if ( isset( $this->params[ 'where' ] ) ) {
            foreach ( $this->params[ 'where' ] AS $values ) {
                $params[ 0 ] .= $values[ 0 ];
                array_shift( $values );
                array_push( $params, ...$values );
            }
        }
        if ( isset( $this->params[ 'duplicatekey' ] ) ) {
            foreach ( $this->params[ 'duplicatekey' ] AS $values ) {
                $params[ 0 ] .= $values[ 0 ];
                array_shift( $values );
                array_push( $params, ...$values );
            }
        }
        if ( !$params[ 0 ] ) {
            $params = [ ];
        }

        return $params;
    }

    /**
     * __toString
     * @return string
     */
    public function __toString(): string {

        if ( !$this->query ) {
            $this->getQuery();
        }

        return $this->query;
    }

    /**
     * getQuery
     * @throws \ecsco\ormbase\exception\DatabaseException
     * @return string
     */
    public function getQuery(): SelectionInterface {

        if ( !$this->mode ) {
            throw new DatabaseException( 'Empty query not allowed' );
        }
        $strName = "buildQuery" . ucfirst( $this->mode );

        $this->$strName();

        return $this;
    }

    /**
     * update
     * @return Selection
     */
    public function update(): SelectionInterface {

        if ( !$this->mode ) {
            $this->mode = 'update';
        }

        return $this;
    }

    /**
     * buildQueryDesc
     * @return Selection
     */
    protected function buildQueryDesc() {

        if ( !$this->tables ) {
            return;
        }

        $this->query = sprintf( 'DESC %s', $this->tables[ 0 ] );
    }

    /**
     * buildQueryView
     * @return void
     */
    protected function buildQueryView() {

        if ( !$this->select || !$this->tables ) {
            return;
        }

        $allTables = $this->tables;
        $this->query = sprintf( 'CREATE %s VIEW `%s` AS ', implode( ' ', $this->createInfo ), $allTables[ 0 ] );
        array_shift( $allTables );

        $tables = '';
        foreach ( $allTables AS $t ) {
            if ( is_array( $t ) ) {
                $ft = '`' . $t[ 0 ] . '`';
                if ( isset( $t[ 1 ] ) ) {
                    $ft .= ' AS ' . $t[ 1 ] . ' ';
                }
                if ( isset( $t[ 2 ] ) ) {
                    $ft = ' LEFT JOIN ' . $ft . ' ' . $t[ 2 ] . ' ';
                }
            }
            else {
                $ft = '`' . $t . '`';
            }

            $tables .= $ft;
        }

        if ( $this->select ) {
            $this->query .= sprintf( 'SELECT %s FROM %s WHERE %s ',
                                     implode( ',', array_keys( $this->select ) ),
                                     $tables,
                                     implode( ' AND ', $this->where )
            );
        }
    }

    /**
     * buildQueryReplace
     * @throws \ecsco\ormbase\exception\DatabaseException
     * @return void
     */
    protected function buildQueryReplace() {

        throw new DatabaseException( 'replace not implemented yet' );
    }

    /**
     * buildQuery
     * @return void
     */
    protected function buildQuerySelect() {

        if ( !$this->select || !$this->tables ) {
            return;
        }

        $allTables = $this->tables;
        $tables = '';
        foreach ( $allTables AS $t ) {
            if ( is_array( $t ) ) {
                $ft = '`' . $t[ 0 ] . '`';
                if ( isset( $t[ 1 ] ) ) {
                    $ft .= ' AS ' . $t[ 1 ] . ' ';
                }
                if ( isset( $t[ 2 ] ) ) {
                    $ft = ' LEFT JOIN ' . $ft . ' ' . $t[ 2 ] . ' ';
                }
            }
            else {
                $ft = '`' . $t . '`';
            }

            $tables .= $ft;
        }

        $this->query = sprintf( 'SELECT %s FROM %s ', implode( ',', array_keys( $this->select ) ), $tables );

        if ( $this->where ) {
            $this->query .= sprintf( ' WHERE %s ', implode( ' AND ', $this->where ) );
        }

        if ( $this->sort ) {
            $this->query .= sprintf( ' ORDER BY %s', implode( ',', $this->sort ) );
        }

        if ( $this->limit ) {
            $this->query .= sprintf( ' LIMIT %s', $this->limit );
        }

        #print $this->query."\n\n";
    }

    /**
     * buildQueryInsert
     * @return void
     */
    protected function buildQueryInsert() {

        if ( !$this->tables || !$this->set ) {
            return;
        }

        $this->query = sprintf( 'INSERT INTO %s SET %s', implode( '`,`', $this->tables ), implode( ', ', $this->set ) );

        if ( $this->duplicateKey ) {
            $this->query .= sprintf( ' ON DUPLICATE KEY UPDATE %s ', implode( ',', $this->duplicateKey ) );
        }
    }

    /**
     * buildQueryUpdate
     * @return void
     */
    protected function buildQueryUpdate() {

        if ( !$this->tables || !$this->set ) {
            return;
        }

        $this->query = sprintf( 'UPDATE %s SET %s', implode( '`,`', $this->tables ), implode( ', ', $this->set ) );

        if ( $this->where ) {
            $this->query .= sprintf( ' WHERE %s ', implode( ' AND ', $this->where ) );
        }
    }

    /**
     * buildQueryDelete
     * @return void
     */
    protected function buildQueryDelete() {

        if ( !$this->tables ) {
            return;
        }

        $this->query = sprintf( 'DELETE FROM %s ', implode( '`,`', $this->tables ) );

        if ( $this->where ) {
            $this->query .= sprintf( ' WHERE %s ', implode( ' AND ', $this->where ) );
        }

        if ( $this->limit ) {
            $this->query .= sprintf( ' LIMIT %s', $this->limit );
        }
    }
}
