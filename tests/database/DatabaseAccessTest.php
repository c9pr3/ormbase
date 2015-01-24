<?php
/**
 * filebasierte Dokumentation
 * @package
 * @author Christian Senkowski <cs@e-cs.co>
 * @since  20141201 17:14
 */

/**
 * Klassendefinition
 * @package
 * @subpackage
 * @author Christian Senkowski <cs@e-cs.co>
 * @since  20141201 17:14
 */
class DatabaseAccessTest extends PHPUnit_Framework_TestCase {

    public function testConfig() {

        $config = \ecsco\ormbase\config\Config::getInstance();

        $this->assertNotEmpty( $config );
        $this->assertInstanceOf( '\ecsco\ormbase\config\Config', $config );

        if ( !$config->hasItem( 'database' ) ) {
            $config->addItem( 'database', 'server', 'localhost' );
            $config->addItem( 'database', 'port', '3306' );
            $config->addItem( 'database', 'username', 'my_dbuser' );
            $config->addItem( 'database', 'password', 'my_dbpass' );
            $config->addItem( 'database', 'dbname', 'my_dbname' );
            $config->addItem( 'database', 'dbbackend', 'mysql' );
            $config->addItem( 'database', 'debugsql', '1' );
            $config->addItem( 'database', 'databaseclass', '\ecsco\ormbase\database\mysql\Database' );
        }

        return $config;
    }

    /**
     * @depends testConfig
     *
     * @param $config
     *
     * @expectedException \ecsco\ormbase\exception\DatabaseException
     */
    public function testConstructMysql( $config ) {

        $db = \ecsco\ormbase\database\DatabaseAccess::getDatabaseInstance( $config, 'mysql' );

        $this->assertNotEmpty( $db );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Database', $db );
    }

    /**
     * @depends testConfig
     *
     * @param $config
    public function testConstructMongo( $config ) {
     * $db = \ecsco\ormbase\database\DatabaseAccess::getDatabaseInstance( $config, 'mongo' );
     * $this->assertNotEmpty( $db );
     * $this->assertInstanceOf( '\ecsco\ormbase\database\mongo\Database', $db );
     * }
     */

    /**
     * @depends testConfig
     * @depends testConstructMysql
     * @expectedException \ecsco\ormbase\exception\DatabaseException
     *
     * @param $config

    public function testQueryCount( $config ) {
     * $queryCount = \ecsco\ormbase\database\DatabaseAccess::getQueryCount( $config );
     * $this->assertEquals( 0, $queryCount );
     * $db = \ecsco\ormbase\database\DatabaseAccess::getDatabaseInstance( $config );
     * $db->query( 'SHOW VARIABLES' );
     * $queryCount = \ecsco\ormbase\database\DatabaseAccess::getQueryCount( $config );
     * $this->assertEquals( 1, $queryCount );
     * }
     * /**
     *
     * @depends testConfig
     * @depends testConstructMysql
     *
     * @param $config

    public function testQueries( $config ) {
     * $queries = \ecsco\ormbase\database\DatabaseAccess::getQueries( $config );
     * $this->assertEmpty( $queries );
     * #
     * # [0] => SET NAMES UTF8
     * # [1] => SET CHARACTER SET UTF8
     * # [2] => SHOW VARIABLES
     * #
     * $this->assertEquals( 0, count( $queries ) );
     * }  */
}
