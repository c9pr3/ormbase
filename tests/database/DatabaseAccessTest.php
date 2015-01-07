<?php
/**
 * filebasierte Dokumentation
 *
 * @package
 * @author Christian Senkowski <cs@e-cs.co>
 * @since 20141201 17:14
 */

/**
 * Klassendefinition
 *
 * @package
 * @subpackage
 * @author Christian Senkowski <cs@e-cs.co>
 * @since 20141201 17:14
 */
class DatabaseAccessTest extends PHPUnit_Framework_TestCase {

	public function testConfig() {
		$config = \wplibs\config\Config::getNamedInstance( CONFIG_NAME, 'database' );

		$this->assertNotEmpty( $config );
		$this->assertInstanceOf( '\wplibs\config\Config', $config );

		return $config;
	}

	/**
	 * @depends testConfig
	 * @param $config
	 */
	public function testConstructMysql( $config ) {
		$db = \wplibs\database\DatabaseAccess::getDatabaseInstance( $config, 'mysql' );

		$this->assertNotEmpty( $db );
		$this->assertInstanceOf( '\wplibs\database\mysql\Database', $db );
	}

	/**
	 * @depends testConfig
	 * @param $config
	public function testConstructMongo( $config ) {
		$db = \wplibs\database\DatabaseAccess::getDatabaseInstance( $config, 'mongo' );

		$this->assertNotEmpty( $db );
		$this->assertInstanceOf( '\wplibs\database\mongo\Database', $db );
	}
	 */

	/**
	 * @depends testConfig
	 * @depends testConstructMysql
	 * @param $config
	 */
	public function testQueryCount( $config ) {

		$queryCount = \wplibs\database\DatabaseAccess::getQueryCount( $config );
		$this->assertEquals( 0, $queryCount );

		$db = \wplibs\database\DatabaseAccess::getDatabaseInstance( $config );
		$db->query( 'SHOW VARIABLES' );

		$queryCount = \wplibs\database\DatabaseAccess::getQueryCount( $config );
		$this->assertEquals( 1, $queryCount );
	}

	/**
	 * @depends testConfig
	 * @depends testConstructMysql
	 * @param $config
	 */
	public function testQueries( $config ) {

		$queries = \wplibs\database\DatabaseAccess::getQueries( $config );

		$this->assertNotEmpty( $queries );

		#
		# [0] => SET NAMES UTF8
		# [1] => SET CHARACTER SET UTF8
		# [2] => SHOW VARIABLES
		#
		$this->assertEquals( 3, count( $queries ) );
	}
}
