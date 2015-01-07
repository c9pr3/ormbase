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
class MysqlDatabaseTest extends PHPUnit_Framework_TestCase {

    public function testConstruct() {

        $config = \wplibs\config\Config::getNamedInstance( CONFIG_NAME, 'database' );

        $this->assertNotEmpty( $config );
        $this->assertInstanceOf( '\wplibs\config\Config', $config );

        $db = \wplibs\database\mysql\Database::getNamedInstance( $config );

        return $db;
    }

    /**
     * @depends testConstruct
     *
     * @param $db
     */
    public function testGetConfigName( $db ) {

        $configName = $db->getConfigName();
        $this->assertEquals( CONFIG_NAME . 'DATABASE', $configName );
    }

    /**
     * @depends testConstruct
     *
     * @param $db
    public function testQuery( $db ) {
     * $result = $db->query( 'SELECT * FROM contact LIMIT 1' );
     * $this->assertNotNull( $result );
     * $this->assertInstanceOf( 'mysqli_result', $result );
     * }
     */

    /**
     * @depends testConstruct
     *
     * @param $db
    public function testPrepare( $db ) {
     * $sql = ( new \wplibs\database\mysql\Selection() )->select( new \wplibs\database\FieldSelection() )->from(
     * 'contact' )->limit( 1 );
     * $result = $db->prepareQuery( $sql, ...[ ] );
     * $this->assertNotNull( $result );
     * $this->assertInstanceOf( 'mysqli_result', $result );
     * }
     */

    /**
     * @depends testConstruct
     * @depends testQuery
    public function testGetQueryCount() {
     * $count = \wplibs\database\mysql\Database::getQueryCount();
     * # SET NAMES AND SET CHARACTER SET should not be counted at "user queries"
     * $this->assertEquals( 2, $count );
     * }
     */

    /**
     * @depends testConstruct
     * @depends testQuery
    public function testGetQueries() {
     * $queries = \wplibs\database\mysql\Database::getQueries();
     * $this->assertNotEmpty( $queries );
     * $this->assertEquals( 4, count( $queries ) );
     * }
     */
}
