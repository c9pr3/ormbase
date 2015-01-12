<?php
/**
 * class.Cache.php
 * @package    wplibs
 * @subpackage CACHE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:04
 */

namespace wplibs\cache\local;

use wplibs\cacheinterface\CacheAccess;
use wplibs\cacheinterface\iCache;
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

        return ( isset( $this->cache[ $cacheType ] ) && isset( $this->cache[ $cacheType ][ $identifier ] ) );
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
    public function add( $cacheType, $identifier, $objects ) {

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
     * @param      string
     * @param bool $identifier
     *
     * @return mixed
     */
    public function get( $cacheType, $identifier = false ) {

        if ( !isset( $this->cache[ $cacheType ] ) ) {
            return [ ];
        }

        $r = $this->cache[ $cacheType ];
        if ( $identifier ) {
            $r = $this->cache[ $cacheType ][ $identifier ];
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
    public function destroy( $cacheType, $identifier = false ) {

        if ( $identifier ) {
            unset( $this->cache[ $cacheType ][ $identifier ] );
        }
        else {
            unset( $this->cache[ $cacheType ] );
        }

        CacheAccess::$stats[ 'destroyed' ]++;

        return true;
    }

    /**
     * To Array
     * @return string[]
     */
    public function toArray() {

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
