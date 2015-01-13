<?php

/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 10.12.2014
 * Time: 12:45
 */
class CacheTest extends PHPUnit_Framework_TestCase {

    public function testAdd() {

        $bool = \wplibs\cache\CacheAccess::getCacheInstance()->add( 'contact', 'test', 'cachedcontent' );
        $this->assertTrue( $bool );
    }

    public function testHas() {
        
        $bool = \wplibs\cache\CacheAccess::getCacheInstance()->add( 'contact', 'test', 'cachedcontent' );
        $this->assertTrue( $bool );

        $bool = \wplibs\cache\CacheAccess::getCacheInstance()->has( 'contact', 'test' );
        $this->assertTrue( $bool );

        $bool = \wplibs\cache\CacheAccess::getCacheInstance()->has( 'contact', 'test1' );
        $this->assertFalse( $bool );
    }

    public function testGet() {

        $bool = \wplibs\cache\CacheAccess::getCacheInstance()->add( 'contact', 'test', 'cachedcontent' );
        $this->assertTrue( $bool );

        $cachedRes = \wplibs\cache\CacheAccess::getCacheInstance()->get( 'contact', 'test' );
        $this->assertEquals( 'cachedcontent', $cachedRes );
    }

    public function testDestroy() {
        
        $bool = \wplibs\cache\CacheAccess::getCacheInstance()->add( 'contact', 'test', 'cachedcontent' );
        $this->assertTrue( $bool );

        $bool = \wplibs\cache\CacheAccess::getCacheInstance()->destroy( 'contact', 'test' );
        $this->assertTrue( $bool );

        $bool = \wplibs\cache\CacheAccess::getCacheInstance()->has( 'contact', 'test' );
        $this->assertFalse( $bool );
    }

    public function testToArray() {

        $bool = \wplibs\cache\CacheAccess::getCacheInstance()->add( 'contact', 'test', 'cachedcontent' );
        $this->assertTrue( $bool );

        $content = \wplibs\cache\CacheAccess::getCacheInstance()->get( 'contact', 'test', 'foobar' );
        $this->assertEquals( 'cachedcontent', $content );

        $toArray = \wplibs\cache\CacheAccess::getCacheInstance()->toArray();
        $this->assertNotEmpty( $toArray );
        $this->assertEquals( 2, count( $toArray ) );
        $this->assertNotEmpty( $toArray[ 'stats' ] );
        $this->assertEquals( 3, count( $toArray[ 'stats' ] ) );
        $this->assertEquals( '5', $toArray[ 'stats' ][ 'added' ] );
        $this->assertEquals( '2', $toArray[ 'stats' ][ 'provided' ] );
        $this->assertEquals( '1', $toArray[ 'stats' ][ 'destroyed' ] );

        $bool = \wplibs\cache\CacheAccess::getCacheInstance()->destroy( 'contact', 'test' );
        $this->assertTrue( $bool );
    }
}
