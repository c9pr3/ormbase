<?php
/**
 * iSelection.php
 * @package    WPLIBS
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:07
 */

namespace wplibs\database;

/**
 * iSelection
 * @package    WPLIBS
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:06
 */
interface iSelection {

    /**
     * select
     *
     * @param iSelectStrategy $selector
     *
     * @return iSelection
     */
    public function select( iSelectStrategy $selector = null );

    /**
     * Describe
     * @return iSelection
     */
    public function desc();

    /**
     * create
     *
     * @param string $additionalInfo
     *
     * @return iSelection
     */
    public function create( $additionalInfo = '' );

    /**
     * insert
     * @return iSelection
     */
    public function insert();

    /**
     * replace
     * @return iSelection
     */
    public function replace();

    /**
     * delete
     * @return iSelection
     */
    public function delete();

    /**
     * from
     *
     * @param mixed  $tableName
     * @param string $alias
     *
     * @return iSelection
     */
    public function from( $tableName, $alias = '' );

    /**
     * into
     *
     * @param mixed $tableName
     *
     * @return iSelection
     */
    public function into( $tableName );

    /**
     * view
     *
     * @param mixed $viewName
     *
     * @return iSelection
     */
    public function view( $viewName );

    /**
     * table
     *
     * @param mixed  $tableName
     * @param string $alias
     *
     * @return iSelection
     */
    public function table( $tableName, $alias = '' );

    /**
     * set
     *
     * @param mixed $fieldName
     * @param mixed $fieldValue
     *
     * @return iSelection
     */
    public function set( $fieldName, $fieldValue );

    /**
     * where
     *
     * @param mixed $fieldName
     * @param mixed $operator
     * @param mixed $fieldValue
     *
     * @return iSelection
     */
    public function where( $fieldName, $operator, $fieldValue );

    /**
     * sort
     *
     * @param mixed  $fieldName
     * @param string $ascDesc
     *
     * @return iSelection
     */
    public function sort( $fieldName, $ascDesc = 'ASC' );

    /**
     * limit
     *
     * @param int $count
     *
     * @return iSelection
     */
    public function limit( $count = 1 );

    /**
     * duplicateKey
     *
     * @param mixed $fieldName
     * @param mixed $fieldValue
     *
     * @return iSelection
     */
    public function duplicateKey( $fieldName, $fieldValue );

    /**
     * getQuery
     * @return string
     */
    public function getQuery();

    /**
     * unparameterize
     * @return iSelection
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
