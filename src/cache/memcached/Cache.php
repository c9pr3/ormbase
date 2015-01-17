<?php
/**
 * class.Cache.php
 * @package    wplibs
 * @subpackage CACHE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:04
 */

namespace wplibs\cache\memcached;

use wplibs\cache\CacheAccess;
use wplibs\cacheinterface\iCache;
use wplibs\config\Config;
use wplibs\exception\CacheException;
use wplibs\traits\tGetInstance;

/**
 * class Cache
 * @package    wplibs
 * @subpackage CACHE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:04
 */
class Cache implements iCache {

    /**
     * @var int
     */
    private static $cacheTime = 30;

    /**
     * @var \Memcached[]
     */
    private static $memcachedInstances = [ ];

    use tGetInstance;

    /**
     * Has cached ?
     *
     * @param $cacheType
     * @param $identifier
     *
     * @return bool
     */
    final public function has( $cacheType, $identifier ) {

        if ( !isset( self::$memcachedInstances[ $cacheType ] ) ) {
            self::addInstance( $cacheType );
        }
        $cache = self::$memcachedInstances[ $cacheType ];
        $cache->get( md5( $identifier ) );
        if ( $cache->getResultCode() != \Memcached::RES_NOTFOUND ) {
            return true;
        }

        return false;
    }

    /**
     * addInstance
     *
     * @param $instanceName
     *
*@return void
     */
    protected function addInstance( $instanceName ) {

        self::$memcachedInstances[ $instanceName ] = new \Memcached( $instanceName );
        self::$memcachedInstances[ $instanceName ]->setOption( \Memcached::OPT_PREFIX_KEY, $instanceName );
        self::$memcachedInstances[ $instanceName ]->setOption( \Memcached::OPT_BINARY_PROTOCOL, true );
        if ( !count( self::$memcachedInstances[ $instanceName ]->getServerList() ) ) {
            self::$memcachedInstances[ $instanceName ]->addServer( Config::getInstance()->getItem( 'cache', 'server' ), Config::getInstance()->getItem( 'cache', 'port' ) );
        }
    }

    /**
     * Add content to cache
     *
     * @param $cacheType
     * @param $identifier
     * @param $objects
     *
     * @return bool
     * @throws \wplibs\exception\CacheException
     */
    final public function add( $cacheType, $identifier, $objects ) {

        if ( !isset( self::$memcachedInstances[ $cacheType ] ) ) {
            self::addInstance( $cacheType );
        }
        $cache = self::$memcachedInstances[ $cacheType ];

        $res = $cache->set( md5( $identifier ), $objects, self::$cacheTime );
        if ( $cache->getResultCode() == \Memcached::RES_NOTSTORED || !$res ) {
            throw new CacheException( "Could not add memcached objects $identifier" );
        }

        CacheAccess::$stats[ 'added' ]++;

        return true;
    }

    /**
     * Get cached content
     *
     * @param      $cacheType
     * @param bool $identifier
     *
     * @return array|mixed
     */
    final public function get( $cacheType, $identifier = false ) {

        if ( !isset( self::$memcachedInstances[ $cacheType ] ) ) {
            self::addInstance( $cacheType );
        }
        $cache = self::$memcachedInstances[ $cacheType ];
        $r = $cache->get( md5( $identifier ) );
        if ( $cache->getResultCode() == \Memcached::RES_NOTFOUND ) {
            return [ ];
        }

        CacheAccess::$stats[ 'provided' ]++;

        return $r;
    }

    /**
     * Destroy cached content
     *
     * @param      $cacheType
     * @param bool $identifier
     *
     *@return bool
     */
    final public function destroy( $cacheType, $identifier = false ) {

        if ( !isset( self::$memcachedInstances[ $cacheType ] ) ) {
            self::addInstance( $cacheType );
        }
        $cache = self::$memcachedInstances[ $cacheType ];
        if ( $identifier ) {
            $cache->delete( md5( $identifier ) );
        }
        else {
            if ( isset( self::$memcachedInstances[ $cacheType ] ) ) {
                self::$memcachedInstances[ $cacheType ]->flush();
            }
        }
        CacheAccess::$stats[ 'destroyed' ]++;

        return true;
    }

    /**
     * To Array
     * @return array
     */
    final public function toArray() {

        $rVal = [ ];
        if ( !self::$memcachedInstances ) {
            return [ ];
        }

        foreach ( self::$memcachedInstances AS $k => $cache ) {
            $keys = $cache->getAllKeys();
            if ( !$keys ) {
                continue;
            }
        }

        $rVal[ 'stats' ] = CacheAccess::$stats;

        return $rVal;
    }
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
