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
class ConfigTest extends PHPUnit_Framework_TestCase {

    public function testConstruct() {

        $config = \wplibs\config\Config::getInstance();

        $config->addItem( 'database', 'server', 'localhost' );
        $config->addItem( 'database', 'port', '3306' );
        $config->addItem( 'database', 'username', 'my_dbuser' );
        $config->addItem( 'database', 'password', 'my_dbpass' );
        $config->addItem( 'database', 'dbname', 'my_dbname' );
        $config->addItem( 'database', 'dbbackend', 'mysql' );
        $config->addItem( 'database', 'debugsql', '1' );

        $this->assertNotEmpty( $config );
        $this->assertInstanceOf( '\wplibs\config\Config', $config );

        return $config;
    }

    public function testConstructSection() {

        $config = \wplibs\config\Config::getInstance();

        $this->assertNotEmpty( $config );
        $this->assertInstanceOf( '\wplibs\config\Config', $config );
    }

    /**
     * @depends testConstruct
     *
     * @param $config
     */
    public function testGetSection( $config ) {

        $section = $config->getSection( 'database' );
        $this->assertInstanceOf( '\Packaged\Config\Provider\ConfigSection', $section );
        $items = $section->getItems();

        $this->assertEquals( 7, count( $items ) );
    }

    /**
     * @depends testConstruct
     *
     * @param $config
     */
    public function testGetValue( $config ) {

        $value = $config->getItem( 'database', 'server' );

        $this->assertEquals( 'localhost', $value );
    }
}
