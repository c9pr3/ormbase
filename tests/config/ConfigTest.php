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

        $config = \wplibs\config\Config::getNamedInstance( CONFIG_NAME );

        $this->assertNotEmpty( $config );
        $this->assertInstanceOf( '\wplibs\config\Config', $config );

        return $config;
    }

    public function testConstructSection() {

        $config = \wplibs\config\Config::getNamedInstance( CONFIG_NAME, 'database' );

        $this->assertNotEmpty( $config );
        $this->assertInstanceOf( '\wplibs\config\Config', $config );
        $this->assertEquals( CONFIG_NAME . 'DATABASE', $config->getConfigName() );
    }

    /**
     * @depends testConstruct
     *
     * @param $config
     */
    public function testGetConfigName( $config ) {

        $this->assertEquals( CONFIG_NAME, $config->getConfigName() );
    }

    /**
     * @depends testConstruct
     *
     * @param $config
     */
    public function testGetSection( $config ) {

        $section = $config->getSection( 'database' );

        $this->assertEquals( CONFIG_NAME . 'DATABASE', $section->getConfigName() );
    }

    public function testGetValue() {

        $config = \wplibs\config\Config::getNamedInstance( CONFIG_NAME, 'CONFIG' );

        $value = $config->getValue( 'server_name' );

        $this->assertEquals( CONFIG_NAME, $value );
    }
}
