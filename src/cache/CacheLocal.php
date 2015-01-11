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

/**
 * class Cache
 * @package    wplibs
 * @subpackage CACHE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:04
 */
class CacheLocal implements iCache {

    /**
     * @var array
     */
    private static $cache = [];

    /**
     * Has cached ?
     *
     * @param string
     * @param string
     *
     * @return boolean
     */
    final public static function has( $cacheType, $identifier ) {
        return ( isset(self::$cache[$cacheType]) && isset(self::$cache[$cacheType][$identifier]) );
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

        if ( !isset(self::$cache[$cacheType]) ) {
            self::$cache[$cacheType] = [];
        }
        self::$cache[$cacheType][md5($identifier)] = $objects;

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

        if ( !isset(self::$cache[$cacheType]) ) {
            return [];
        }

        $r = self::$cache[$cacheType];
        if ( $identifier ) {
            $r = self::$cache[$cacheType][$identifier];
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

        if ( $identifier ) {
            unset(self::$cache[$cacheType][$identifier]);
        } else {
            unset(self::$cache[$cacheType]);
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
        foreach ( self::$cache AS $key => $kv ) {
            foreach ( $kv AS $k => $v ) {
                $rVal[ $key ][ $k ] = count( $v );
            }
        }

        $rVal[ 'stats' ] = CacheAccess::$stats;

        return $rVal;
    }
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
