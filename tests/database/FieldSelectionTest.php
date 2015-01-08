<?php
/**
 * filebasierte Dokumentation
 *
 * @package
 * @author Christian Senkowski <cs@e-cs.co>
 * @since  20141202 12:33
 */

/**
 * Klassendefinition
 *
 * @package
 * @subpackage
 * @author Christian Senkowski <cs@e-cs.co>
 * @since  20141202 12:33
 */
class FieldSelectionTest extends PHPUnit_Framework_TestCase {

    public function testConstruct() {

        $fs = new \wplibs\database\FieldSelection();

        $this->assertNotNull( $fs );
        $this->assertInstanceOf( '\wplibs\database\FieldSelection', $fs );
    }

    public function testConstructWithParams() {

        $params = [ 'id', 'foo' ];
        $fs = new \wplibs\database\FieldSelection( $params );

        $this->assertNotNull( $fs );
        $this->assertInstanceOf( '\wplibs\database\FieldSelection', $fs );

        return $fs;
    }

    /**
     * @depends testConstructWithParams
     *
     * @param $fs
     */
    public function testParams( $fs ) {

        $fields = $fs->get();

        $this->assertNotEmpty( $fields );
        $this->assertEquals( 2, count( $fields[ 0 ] ) );
    }
}
