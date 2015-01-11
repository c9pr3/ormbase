<?php
/**
 * class.Cache.php
 * @package    wplibs
 * @subpackage CACHE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:04
 */

namespace wplibs\cache;

use wplibs\cacheinterface\CacheAccess;
use wplibs\cacheinterface\iCache;
use wplibs\config\Config;
use wplibs\exception\CacheException;

/**
 * class Cache
 * @package    wplibs
 * @subpackage CACHE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:04
 */
class CacheMemcached implements iCache {

    /**
     * @var \Memcached[]
     */
    private static $instances = [ ];

    /**
     * Has cached ?
     *
     * @param string
     * @param string
     *
     * @return boolean
     */
    final public static function has( $cacheType, $identifier ) {

        if ( !isset( self::$instances[ $cacheType ] ) ) {
            self::addInstance( $cacheType );
        }
        $cache = self::$instances[ $cacheType ];
        $cache->get( md5( $identifier ) );
        if ( $cache->getResultCode() != \Memcached::RES_NOTFOUND ) {
            return true;
        }

        return false;
    }

    /**
     * addInstance
     *
     * @param mixed $instanceName
     *
     * @return void
     */
    protected static function addInstance( $instanceName ) {

        self::$instances[ $instanceName ] = new \Memcached( $instanceName );
        self::$instances[ $instanceName ]->setOption( \Memcached::OPT_PREFIX_KEY, $instanceName );
        self::$instances[ $instanceName ]->setOption( \Memcached::OPT_BINARY_PROTOCOL, true );
        if ( !count( self::$instances[ $instanceName ]->getServerList() ) ) {
            self::$instances[ $instanceName ]->addServer( Config::getInstance()->getItem( 'cache', 'server' ), Config::getInstance()->getItem( 'cache', 'port' ) );
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
    public static function add( $cacheType, $identifier, $objects ) {

        if ( !isset( self::$instances[ $cacheType ] ) ) {
            self::addInstance( $cacheType );
        }
        $cache = self::$instances[ $cacheType ];

        $res = $cache->set( md5( $identifier ), $objects, CacheAccess::$cacheTime );
        if ( $cache->getResultCode() == \Memcached::RES_NOTSTORED || !$res ) {
            throw new CacheException( "Could not add memcached objects $identifier" );
        }

        CacheAccess::$stats[ 'added' ]++;

        return true;
    }

    /**
     * Get cached content
     *
     * @param      string
     * @param bool $identifier
     *
     * @return mixed
     */
    public static function get( $cacheType, $identifier = false ) {

        if ( !isset( self::$instances[ $cacheType ] ) ) {
            self::addInstance( $cacheType );
        }
        $cache = self::$instances[ $cacheType ];
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
     * @param                string
     * @param string|boolean default : false
     *
     * @return boolean
     */
    public static function destroy( $cacheType, $identifier = false ) {

        if ( !isset( self::$instances[ $cacheType ] ) ) {
            self::addInstance( $cacheType );
        }
        $cache = self::$instances[ $cacheType ];
        if ( $identifier ) {
            $cache->delete( md5( $identifier ) );
        }
        else {
            if ( isset( self::$instances[ $cacheType ] ) ) {
                self::$instances[ $cacheType ]->flush();
            }
        }
        CacheAccess::$stats[ 'destroyed' ]++;

        return true;
    }

    /**
     * To Array
     * @return string[]
     */
    public static function toArray() {

        $rVal = [ ];
        if ( !self::$instances ) {
            return [ ];
        }

        foreach ( self::$instances AS $k => $cache ) {
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
