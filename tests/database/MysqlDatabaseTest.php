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

    /**
     * @expectedException \wplibs\exception\DatabaseException
     */
    public function testConstruct() {

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

        $db = \wplibs\database\mysql\Database::getNamedInstance( $config->getSection( 'database' ) );

        return $db;
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
