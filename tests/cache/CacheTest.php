<?php

/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 10.12.2014
 * Time: 12:45
 */
class CacheTest extends PHPUnit_Framework_TestCase {

    public function testAdd() {

        $bool = \ecsco\ormbase\cache\CacheAccess::getCacheInstance()->add( 'contact', 'test', 'cachedcontent' );
        $this->assertTrue( $bool );
    }

    public function testHas() {

        $bool = \ecsco\ormbase\cache\CacheAccess::getCacheInstance()->add( 'contact', 'test', 'cachedcontent' );
        $this->assertTrue( $bool );

        $bool = \ecsco\ormbase\cache\CacheAccess::getCacheInstance()->has( 'contact', 'test' );
        $this->assertTrue( $bool );

        $bool = \ecsco\ormbase\cache\CacheAccess::getCacheInstance()->has( 'contact', 'test1' );
        $this->assertFalse( $bool );
    }

    public function testGet() {

        $bool = \ecsco\ormbase\cache\CacheAccess::getCacheInstance()->add( 'contact', 'test', 'cachedcontent' );
        $this->assertTrue( $bool );

        $cachedRes = \ecsco\ormbase\cache\CacheAccess::getCacheInstance()->get( 'contact', 'test' );
        $this->assertEquals( 'cachedcontent', $cachedRes );
    }

    public function testDestroy() {

        $bool = \ecsco\ormbase\cache\CacheAccess::getCacheInstance()->add( 'contact', 'test', 'cachedcontent' );
        $this->assertTrue( $bool );

        $bool = \ecsco\ormbase\cache\CacheAccess::getCacheInstance()->destroy( 'contact', 'test' );
        $this->assertTrue( $bool );

        $bool = \ecsco\ormbase\cache\CacheAccess::getCacheInstance()->has( 'contact', 'test' );
        $this->assertFalse( $bool );
    }

    public function testToArray() {

        $bool = \ecsco\ormbase\cache\CacheAccess::getCacheInstance()->add( 'contact', 'test', 'cachedcontent' );
        $this->assertTrue( $bool );

        $content = \ecsco\ormbase\cache\CacheAccess::getCacheInstance()->get( 'contact', 'test', 'foobar' );
        $this->assertEquals( 'cachedcontent', $content );

        $toArray = \ecsco\ormbase\cache\CacheAccess::getCacheInstance()->toArray();
        $this->assertNotEmpty( $toArray );
        $this->assertEquals( 2, count( $toArray ) );
        $this->assertNotEmpty( $toArray[ 'stats' ] );
        $this->assertEquals( 3, count( $toArray[ 'stats' ] ) );
        $this->assertEquals( '5', $toArray[ 'stats' ][ 'added' ] );
        $this->assertEquals( '2', $toArray[ 'stats' ][ 'provided' ] );
        $this->assertEquals( '1', $toArray[ 'stats' ][ 'destroyed' ] );

        $bool = \ecsco\ormbase\cache\CacheAccess::getCacheInstance()->destroy( 'contact', 'test' );
        $this->assertTrue( $bool );
    }
}
