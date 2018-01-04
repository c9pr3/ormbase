<?php
/**
 * SelectionInterface.php
 * @package    ecsco\ormbase
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:07
 */

declare(strict_types=1);

namespace ecsco\ormbase\database;

/**
 * SelectionInterface
 * @package    ecsco\ormbase
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
    public function desc(): SelectionInterface;

    /**
     * create
     *
     * @param string $additionalInfo
     *
     * @return SelectionInterface
     */
    public function create( string $additionalInfo = '' ): SelectionInterface;

    /**
     * insert
     * @return SelectionInterface
     */
    public function insert(): SelectionInterface;

    /**
     * replace
     * @return SelectionInterface
     */
    public function replace(): SelectionInterface;

    /**
     * delete
     * @return SelectionInterface
     */
    public function delete(): SelectionInterface;

    /**
     * from
     *
     * @param mixed  $tableName
     * @param string $alias
     *
     * @return SelectionInterface
     */
    public function from( string $tableName, string $alias = '' ): SelectionInterface;

    /**
     * into
     *
     * @param mixed $tableName
     *
     * @return SelectionInterface
     */
    public function into( string $tableName ): SelectionInterface;

    /**
     * view
     *
     * @param mixed $viewName
     *
     * @return SelectionInterface
     */
    public function view( string $viewName ): SelectionInterface;

    /**
     * table
     *
     * @param mixed  $tableName
     * @param string $alias
     *
     * @return SelectionInterface
     */
    public function table( string $tableName, string $alias = '' ): SelectionInterface;

    /**
     * set
     *
     * @param mixed $fieldName
     * @param mixed $fieldValue
     *
     * @return SelectionInterface
     */
    public function set( string $fieldName, $fieldValue ): SelectionInterface;

    /**
     * where
     *
     * @param mixed $fieldName
     * @param mixed $operator
     * @param mixed $fieldValue
     *
     * @return SelectionInterface
     */
    public function where( string $fieldName, string $operator, $fieldValue ): SelectionInterface;

    /**
     * sort
     *
     * @param mixed  $fieldName
     * @param string $ascDesc
     *
     * @return SelectionInterface
     */
    public function sort( string $fieldName, string $ascDesc = 'ASC' ): SelectionInterface;

    /**
     * limit, i.e. 10,1
     *
     * @param string limit
     *
     * @return SelectionInterface
     */
    public function limit( string $limit = '1' ): SelectionInterface;

    /**
     * duplicateKey
     *
     * @param mixed $fieldName
     * @param mixed $fieldValue
     *
     * @return SelectionInterface
     */
    public function duplicateKey( string $fieldName, $fieldValue ): SelectionInterface;

    /**
     * getQuery
     * @return SelectionInterface
     */
    public function getQuery(): SelectionInterface;

    /**
     * unparameterize
     * @return SelectionInterface
     */
    public function unparameterize(): SelectionInterface;

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
