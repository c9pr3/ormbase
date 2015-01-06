<?php
/**
 * class.DatabaseAccess.php
 *
 * @package wplibs
 * @subpackage DATABASE
 * @author Christian Senkowski <c.senkowski@kon.de>
 * @since 20150106 14:05
 */

namespace wplibs\database;

use wplibs\config\Config;
use wplibs\traits\tCall;

/**
 * class DatabaseAccess
 *
 * @package wplibs
 * @subpackage DATABASE
 * @author Christian Senkowski <c.senkowski@kon.de>
 * @since 20150106 14:05
 */
class DatabaseAccess {

	use tCall;

	/**
	 * Create new DatabaseAccess
	 *
	 * @return DatabaseAccess
	 */
	private function __construct() {
	}

	/**
	 * Get a database instance
	 *
	 * @param \wplibs\config\Config $config
	 * @param string               $forceBackend
	 * @throws \ConfigException
	 * @return iDatabase
	 */
	public static function getDatabaseInstance( Config $config, $forceBackend = 'mysql' ) {
		$backend = $forceBackend;
		if ( !$forceBackend ) {
			$backend = $config->getValue( 'dbbackend' );
		}
		$nameSpace = "\wplibs\database\\" . $backend . "\Database";

		/** @noinspection PhpUndefinedMethodInspection */

		return $nameSpace::getNamedInstance( $config->getSection('DATABASE') );
	}

	/**
	 * Get query count for specific config
	 *
	 * @param \wplibs\config\Config $config
	 * @throws \ConfigException
	 * @return int
	 */
	public static function getQueryCount( Config $config ) {
		$nameSpace = "\wplibs\database\\" . $config->getValue( 'dbbackend' ) . "\Database";

		/** @noinspection PhpUndefinedMethodInspection */

		return $nameSpace::getQueryCount();
	}

	/**
	 * Get queries for specific config
	 *
	 * @param \wplibs\config\Config $config
	 * @throws \ConfigException
	 * @return string[]
	 */
	public static function getQueries( Config $config ) {
		$nameSpace = "\wplibs\database\\" . $config->getValue( 'dbbackend' ) . "\Database";

		/** @noinspection PhpUndefinedMethodInspection */

		return $nameSpace::getQueries();
	}
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
