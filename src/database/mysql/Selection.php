<?php
/**
 * Selection.php
 * @package    WPLIBS
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:08
 */

namespace wplibs\database\mysql;

use wplibs\database\iSelection;
use wplibs\database\iSelectStrategy;
use wplibs\exception\DatabaseException;

/**
 * Selection
 * @package    WPLIBS
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:08
 */
class Selection implements iSelection {

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
     * @param iSelectStrategy $selector
     *
     * @return Selection
     */
    public function select( iSelectStrategy $selector = null ) {

        if ( !$this->mode ) {
            $this->mode = 'select';
        }

        if ( $selector instanceof iSelectStrategy ) {
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
            $this->mode = 'replace';
        }

        return $this;
    }

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
     * view
     *
     * @param mixed $viewName
     *
     * @return Selection
     */
    public function view( $viewName ) {

        $this->tables[ ] = $viewName;
        $this->mode = 'view';

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
     * @throws \wplibs\exception\DatabaseException
     * @return string[]
     */
    private function prepareValue( $value ) {

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
     * @throws \wplibs\exception\DatabaseException
     * @internal param string $where
     * @return Selection
     */
    public function where( $fieldName, $operator, $fieldValue ) {

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
            $this->params[ 'where' ][ ] = $this->array_flatten( [ $fieldType, $fieldValue ] );
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
    private function array_flatten( $array ) {

        $result = [ ];

        foreach ( $array as $element ) {
            if ( is_array( $element ) ) {
                $result = array_merge( $result, $this->array_flatten( $element ) );
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
     * duplicateKey
     *
     * @param mixed $fieldName
     * @param mixed $fieldValue
     *
     * @return Selection
     */
    public function duplicateKey( $fieldName, $fieldValue ) {

        $this->duplicateKey[ ] = "`$fieldName` = ?";

        list( $fieldType, $fieldValue ) = $this->prepareValue( $fieldValue );

        $this->params[ 'duplicatekey' ][ ] = [ $fieldType, $fieldValue ];

        return $this;
    }

    /**
     * unparameterize
     * @return Selection
     */
    public function unparameterize() {

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
    public function getQueryParams() {

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
    public function __toString() {

        if ( !$this->query ) {
            $this->getQuery();
        }

        return $this->query;
    }

    /**
     * getQuery
     * @throws \wplibs\exception\DatabaseException
     * @return string
     */
    public function getQuery() {

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
    public function update() {

        if ( !$this->mode ) {
            $this->mode = 'update';
        }

        return $this;
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
     * @throws \wplibs\exception\DatabaseException
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
