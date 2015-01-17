<?php
/**
 * class.Cache.php
 * @package    wplibs
 * @subpackage CACHE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:04
 */

namespace wplibs\cache\local;

use wplibs\cache\CacheAccess;
use wplibs\cacheinterface\iCache;
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
     * @var array
     */
    private $cache = [ ];

    use tGetInstance;

    /**
     * Has cached ?
     *
     * @param string
     * @param string
     *
     * @return boolean
     */
    final public function has( $cacheType, $identifier ) {

        return ( isset( $this->cache[ $cacheType ] ) && isset( $this->cache[ $cacheType ][ md5( $identifier ) ] ) );
    }

    /**
     * Add content to cache
     *
     * @param $cacheType
     * @param $identifier
     * @param $objects
     *
     * @return bool
     */
    final public function add( $cacheType, $identifier, $objects ) {

        if ( !isset( $this->cache[ $cacheType ] ) ) {
            $this->cache[ $cacheType ] = [ ];
        }
        $this->cache[ $cacheType ][ md5( $identifier ) ] = $objects;
        CacheAccess::$stats[ 'added' ]++;

        return true;
    }

    /**
     * Get cached content

     *
*@param      $cacheType
     * @param bool $identifier
     *
     * @return array|mixed
     * @throws \wplibs\exception\CacheException
     */
    final public function get( $cacheType, $identifier = false ) {

        if ( !isset( $this->cache[ $cacheType ] ) ) {
            return [ ];
        }

        $r = $this->cache[ $cacheType ];
        if ( $identifier ) {
            if ( !isset( $this->cache[ $cacheType ][ md5( $identifier ) ] ) ) {
                throw new CacheException( "Could not find $cacheType/$identifier" );
            }
            $r = $this->cache[ $cacheType ][ md5( $identifier ) ];
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
     * @return bool
     */
    final public function destroy( $cacheType, $identifier = false ) {

        if ( $identifier ) {
            unset( $this->cache[ $cacheType ][ md5( $identifier ) ] );
        }
        else {
            unset( $this->cache[ $cacheType ] );
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
        foreach ( $this->cache AS $key => $kv ) {
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
