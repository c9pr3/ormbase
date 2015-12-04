<?php
/**
 * DatabaseInterface.php
 * @package    ecsco\ormbase
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:06
 */

declare(strict_types=1);

namespace ecsco\ormbase\database;

use ecsco\ormbase\config\Config;

/**
 * DatabaseInterface
 * @package    ecsco\ormbase
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:06
 */
interface DatabaseInterface {
    /**
     * @param Config $dbConfig
     *
     * @return mixed
     */
    public static function getNamedInstance( Config $dbConfig );

    /**
     * @return int
     */
    public static function getQueryCount(): int;

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
    public function desc(): SelectionInterface;

    /**
     * select
     *
     * @param SelectStrategyInterface $selector
     *
     * @return SelectionInterface
     */
    public function select( SelectStrategyInterface $selector = null ): SelectionInterface;

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
     * update
     * @return SelectionInterface
     */
    public function update(): SelectionInterface;

    /**
     * prepare
     *
     * @param mixed $sql
     * @param ... $params
     *
     * @return array
     */
    public function prepareQuery( SelectionInterface $sql, string ...$params );

    /**
     * @return mixed
     */
    public function getConfigName(): string;
}
