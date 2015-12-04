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
class MysqlSelectionTest extends PHPUnit_Framework_TestCase {

    public function testConstruct() {

        $sel = new \ecsco\ormbase\database\mysql\Selection();

        $this->assertNotNull( $sel );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $sel );

        return $sel;
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     */
    public function testSelect( $sel ) {

        $selection = $sel->select( new \ecsco\ormbase\database\FieldSelection( '*' ) )->from( 'test' );

        $this->assertNotNull( $selection );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $selection );
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     */
    public function testCreate( $sel ) {

        $selection = $sel->create();

        $this->assertNotNull( $selection );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $selection );
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     */
    public function testCreateAdditionalInfo( $sel ) {

        $selection = $sel->create( 'additionalInfo' );

        $this->assertNotNull( $selection );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $selection );
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     */
    public function testInsert( $sel ) {

        $selection = $sel->insert();

        $this->assertNotNull( $selection );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $selection );
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     */
    public function testReplace( $sel ) {

        $selection = $sel->replace()->table( 'additionalInfo' );

        $this->assertNotNull( $selection );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $selection );
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     */
    public function testUpdate( $sel ) {

        $selection = $sel->update()->table( 'additionalInfo' );

        $this->assertNotNull( $selection );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $selection );
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     */
    public function testDelete( $sel ) {

        $selection = $sel->delete();

        $this->assertNotNull( $selection );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $selection );
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     */
    public function testFrom( $sel ) {

        $selection = $sel->delete()->from( 'additionalInfo' );

        $this->assertNotNull( $selection );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $selection );
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     */
    public function testFromAlias( $sel ) {

        $selection = $sel->delete()->from( 'additionalInfo', 'alias' );

        $this->assertNotNull( $selection );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $selection );
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     */
    public function testInto( $sel ) {

        $selection = $sel->insert()->into( 'additionalInfo' );

        $this->assertNotNull( $selection );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $selection );
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     */
    public function testView( $sel ) {

        $selection = $sel->create()->view( 'foo' );

        $this->assertNotNull( $selection );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $selection );
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     */
    public function testTable( $sel ) {

        $selection = $sel->table( 'foo' );

        $this->assertNotNull( $selection );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $selection );
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     */
    public function testSet( $sel ) {

        $selection = $sel->set( 'fieldName', '=', 'fieldValue' );

        $this->assertNotNull( $selection );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $selection );
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     */
    public function testWhere( $sel ) {

        $selection = $sel->where( 'fieldName', '=', 'fieldValue' );

        $this->assertNotNull( $selection );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $selection );
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     */
    public function testSort( $sel ) {

        $selection = $sel->sort( 'fieldSort', 'DESC' );

        $this->assertNotNull( $selection );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $selection );
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     */
    public function testLimit( $sel ) {

        $selection = $sel->limit( '10,100' );

        $this->assertNotNull( $selection );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $selection );
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     */
    public function testDuplicateKey( $sel ) {

        $selection = $sel->duplicateKey( 'fieldDuplicate', 'valueDuplicate' );

        $this->assertNotNull( $selection );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $selection );
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     * @expectedException \ecsco\ormbase\exception\DatabaseException
     */
    public function testUnparameterize( $sel ) {

        $selection = $sel->unparameterize();

        $this->assertFalse( $selection );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $sel );
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     */
    public function testGetQuery( $sel ) {

        $selection = $sel->getQuery();

        $this->assertNotNull( $selection );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $selection );
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     */
    public function testGetQueryParams( $sel ) {

        $selection = $sel->getQueryParams();

        $this->assertNotNull( $selection );
        $this->assertInstanceOf( '\ecsco\ormbase\database\mysql\Selection', $sel );
        $this->assertNotEmpty( $selection );
        $this->assertEquals( 4, count( $selection ) );
    }

    /**
     * @depends testConstruct
     *
     * @param $sel
     */
    public function testToString( $sel ) {

        $selection = $sel->__toString();

        $this->assertNotNull( $selection );
        $this->assertNotEmpty( $selection );
    }
}
