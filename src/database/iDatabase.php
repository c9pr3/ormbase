<?php
/**
 * iDatabase.php
 * @package    WPLIBS
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:06
 */

namespace wplibs\database;

use Packaged\Config\Provider\ConfigSection;

/**
 * Interface iDatabase
 * @package    WPLIBS
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:06
 */
interface iDatabase {
    /**
     * @param \Packaged\Config\Provider\ConfigSection $dbConfig
     *
     * @return mixed
     */
    public static function getNamedInstance( ConfigSection $dbConfig );

    /**
     * @return mixed
     */
    public static function getQueryCount();

    /**
     * @return mixed
     */
    public static function getQueries();

    /**
     * @param $sql
     *
     * @return array
     */
    public function query( $sql );

    /**
     * @return array
     */
    public function desc();

    /**
     * select
     *
     * @param iSelectStrategy $selector
     *
     * @return iSelection
     */
    public function select( iSelectStrategy $selector = null );

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
     * update
     * @return iSelection
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
    public function prepareQuery( iSelection $sql, ...$params );

    /**
     * @return mixed
     */
    public function getConfigName();
}
