<?php

/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 10.12.2014
 * Time: 12:45
 */
class CacheTest extends PHPUnit_Framework_TestCase {

    public function testAdd() {

        #public static function add( $cacheType, $identifier, $objects ) {

        $bool = \wplibs\cache\Cache::add( 'contact', 'test', 'cachedcontent' );
        $this->assertTrue( $bool );
    }

    public function testHas() {

        #final public static function has( $cacheType, $identifier ) {

        $bool = \wplibs\cache\Cache::has( 'contact', 'test' );
        $this->assertTrue( $bool );

        $bool = \wplibs\cache\Cache::has( 'contact', 'test1' );
        $this->assertFalse( $bool );
    }

    /**
     * @depends testAdd
     */
    public function testGet() {

        #public static function get( $cacheType, $identifier ) {

        $cachedRes = \wplibs\cache\Cache::get( 'contact', 'test' );
        $this->assertEquals( 'cachedcontent', $cachedRes );
    }

    public function testDestroy() {

        #public static function destroy( $cacheType, $identifier = false ) {

        $bool = \wplibs\cache\Cache::destroy( 'contact', 'test' );
        $this->assertTrue( $bool );

        $bool = \wplibs\cache\Cache::has( 'contact', 'test' );
        $this->assertFalse( $bool );
    }

    public function testToArray() {

        #public static function toArray() {

        $bool = \wplibs\cache\Cache::add( 'contact', 'test', 'cachedcontent' );
        $this->assertTrue( $bool );

        $content = \wplibs\cache\Cache::get( 'contact', 'test' );
        $this->assertEquals( 'cachedcontent', $content );

        $toArray = \wplibs\cache\Cache::toArray();
        $this->assertNotEmpty( $toArray );
        $this->assertEquals( 1, count( $toArray ) );
        $this->assertNotEmpty( $toArray[ 'stats' ] );
        $this->assertEquals( 3, count( $toArray[ 'stats' ] ) );
        $this->assertEquals( '2', $toArray[ 'stats' ][ 'added' ] );
        $this->assertEquals( '2', $toArray[ 'stats' ][ 'provided' ] );
        $this->assertEquals( '1', $toArray[ 'stats' ][ 'destroyed' ] );

        $bool = \wplibs\cache\Cache::destroy( 'contact', 'test' );
        $this->assertTrue( $bool );
    }
}
