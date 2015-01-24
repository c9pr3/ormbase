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

        $config = \ecsco\ormbase\config\Config::getInstance();

        $config->addItem( 'database', 'server', 'localhost' );
        $config->addItem( 'database', 'port', '3306' );
        $config->addItem( 'database', 'username', 'my_dbuser' );
        $config->addItem( 'database', 'password', 'my_dbpass' );
        $config->addItem( 'database', 'dbname', 'my_dbname' );
        $config->addItem( 'database', 'dbbackend', 'mysql' );
        $config->addItem( 'database', 'debugsql', '1' );

        $this->assertNotEmpty( $config );
        $this->assertInstanceOf( '\ecsco\ormbase\config\Config', $config );

        return $config;
    }

    public function testConstructSection() {

        $config = \ecsco\ormbase\config\Config::getInstance();

        $this->assertNotEmpty( $config );
        $this->assertInstanceOf( '\ecsco\ormbase\config\Config', $config );
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
