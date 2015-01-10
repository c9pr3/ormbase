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

        $config = \wplibs\config\Config::getInstance();

        $this->assertNotEmpty( $config );
        $this->assertInstanceOf( '\wplibs\config\Config', $config );

        $config->addItem( 'database', 'server', 'localhost' );
        $config->addItem( 'database', 'port', '3306' );
        $config->addItem( 'database', 'username', 'my_dbuser' );
        $config->addItem( 'database', 'password', 'my_dbpass' );
        $config->addItem( 'database', 'dbname', 'my_dbname' );
        $config->addItem( 'database', 'dbbackend', 'mysql' );
        $config->addItem( 'database', 'debugsql', '1' );

        $config->addItem( 'config', 'debuglog', '1' );

        return $config;
    }

    /**
     * @depends testConfig

     *
*@param $config
     *
*@expectedException \wplibs\exception\DatabaseException
     */
    public function testConstructMysql( $config ) {

        $db = \wplibs\database\DatabaseAccess::getDatabaseInstance( $config, 'mysql' );

        $this->assertNotEmpty( $db );
        $this->assertInstanceOf( '\wplibs\database\mysql\Database', $db );
    }

    /**
     * @depends testConfig
     *
     * @param $config
    public function testConstructMongo( $config ) {
     * $db = \wplibs\database\DatabaseAccess::getDatabaseInstance( $config, 'mongo' );
     * $this->assertNotEmpty( $db );
     * $this->assertInstanceOf( '\wplibs\database\mongo\Database', $db );
     * }
     */

    /**
     * @depends testConfig
     * @depends testConstructMysql
     * @expectedException \wplibs\exception\DatabaseException
     *
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
     *
     * @param $config
     */
    public function testQueries( $config ) {

        $queries = \wplibs\database\DatabaseAccess::getQueries( $config );

        $this->assertEmpty( $queries );

        #
        # [0] => SET NAMES UTF8
        # [1] => SET CHARACTER SET UTF8
        # [2] => SHOW VARIABLES
        #
        $this->assertEquals( 0, count( $queries ) );
    }
}
