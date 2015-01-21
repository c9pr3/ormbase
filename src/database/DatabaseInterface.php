<?php
/**
 * DatabaseInterface.php
 * @package    ecsco\ormbase
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:06
 */

namespace ecsco\ormbase\database;

use ecsco\ormbase\config\ConfigSection;

/**
 * DatabaseInterface
 * @package    ecsco\ormbase
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:06
 */
interface DatabaseInterface {
    /**
     * @param ConfigSection $dbConfig
     *
     * @return mixed
     */
    public static function getNamedInstance( ConfigSection $dbConfig );

    /**
     * @return int
     */
    public static function getQueryCount();

    /**
     * @return array
     */
    public static function getQueries();

    /**
     * @param $sql
     *
     * @return mixed
     */
    public function query( $sql );

    /**
     * @return SelectionInterface
     */
    public function desc();

    /**
     * select
     *
     * @param SelectStrategyInterface $selector
     *
     * @return SelectionInterface
     */
    public function select( SelectStrategyInterface $selector = null );

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
     * update
     * @return SelectionInterface
     */
    public function update();

    /**
     * prepare
     *
     * @param mixed $sql
     * @param ... $params
     *
     * @return array
     */
    public function prepareQuery( SelectionInterface $sql, ...$params );

    /**
     * @return mixed
     */
    public function getConfigName();
}
