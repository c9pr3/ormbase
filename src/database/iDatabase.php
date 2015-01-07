<?php
/**
 * iDatabase.php
 *
 * @package WPLIBS
 * @subpackage DATABASE
 * @author Christian Senkowski <cs@e-cs.co>
 * @since 20150106 14:06
 */

namespace wplibs\database;

use wplibs\config\Config;

/**
 * Interface iDatabase
 *
 * @package WPLIBS
 * @subpackage DATABASE
 * @author Christian Senkowski <cs@e-cs.co>
 * @since 20150106 14:06
 */
interface iDatabase {
	/**
	 * @param \wplibs\config\Config $dbConfig
	 * @return mixed
	 */
	public static function getNamedInstance( Config $dbConfig );

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
	 * @return \mysqli_result
	 */
	public function query( $sql );

	/**
	 * select
	 *
	 * @param iSelectStrategy $selector
	 * @return iSelection
	 */
	public function select( iSelectStrategy $selector = null );

	/**
	 * create
	 *
	 * @param string $additionalInfo
	 * @return iSelection
	 */
	public function create( $additionalInfo = '' );

	/**
	 * insert
	 *
	 * @return iSelection
	 */
	public function insert();

	/**
	 * replace
	 *
	 * @return iSelection
	 */
	public function replace();

	/**
	 * delete
	 *
	 * @return iSelection
	 */
	public function delete();

	/**
	 * prepare
	 *
	 * @param mixed $sql
	 * @param ... $params
	 * @return \mysqli_result
	 */
	public function prepareQuery( iSelection $sql, ...$params );

	/**
	 * @return mixed
	 */
	public function getConfigName();
}
