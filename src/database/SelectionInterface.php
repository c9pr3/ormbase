<?php
/**
 * SelectionInterface.php
 * @package    WPLIBS
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:07
 */

namespace wplibs\database;

/**
 * SelectionInterface
 * @package    WPLIBS
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:06
 */
interface SelectionInterface {

    /**
     * select
     *
     * @param SelectStrategyInterface $selector
     *
     * @return SelectionInterface
     */
    public function select( SelectStrategyInterface $selector = null );

    /**
     * Describe
     * @return SelectionInterface
     */
    public function desc();

    /**
     * create
     *
     * @param string $additionalInfo
     *
     * @return SelectionInterface
     */
    public function create( $additionalInfo = '' );

    /**
     * insert
     * @return SelectionInterface
     */
    public function insert();

    /**
     * replace
     * @return SelectionInterface
     */
    public function replace();

    /**
     * delete
     * @return SelectionInterface
     */
    public function delete();

    /**
     * from
     *
     * @param mixed  $tableName
     * @param string $alias
     *
     * @return SelectionInterface
     */
    public function from( $tableName, $alias = '' );

    /**
     * into
     *
     * @param mixed $tableName
     *
     * @return SelectionInterface
     */
    public function into( $tableName );

    /**
     * view
     *
     * @param mixed $viewName
     *
     * @return SelectionInterface
     */
    public function view( $viewName );

    /**
     * table
     *
     * @param mixed  $tableName
     * @param string $alias
     *
     * @return SelectionInterface
     */
    public function table( $tableName, $alias = '' );

    /**
     * set
     *
     * @param mixed $fieldName
     * @param mixed $fieldValue
     *
     * @return SelectionInterface
     */
    public function set( $fieldName, $fieldValue );

    /**
     * where
     *
     * @param mixed $fieldName
     * @param mixed $operator
     * @param mixed $fieldValue
     *
     * @return SelectionInterface
     */
    public function where( $fieldName, $operator, $fieldValue );

    /**
     * sort
     *
     * @param mixed  $fieldName
     * @param string $ascDesc
     *
     * @return SelectionInterface
     */
    public function sort( $fieldName, $ascDesc = 'ASC' );

    /**
     * limit
     *
     * @param int $count
     *
     * @return SelectionInterface
     */
    public function limit( $count = 1 );

    /**
     * duplicateKey
     *
     * @param mixed $fieldName
     * @param mixed $fieldValue
     *
     * @return SelectionInterface
     */
    public function duplicateKey( $fieldName, $fieldValue );

    /**
     * getQuery
     * @return string
     */
    public function getQuery();

    /**
     * unparameterize
     * @return SelectionInterface
     */
    public function unparameterize();

    /**
     * getQueryParams
     * @return string[]
     */
    public function getQueryParams();

    /**
     * __toString
     * @return string
     */
    public function __toString();
}
