<?php
/**
 * class.Cache.php
 * @package    ecsco\ormbase
 * @subpackage CACHE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:04
 */

declare(strict_types=1);

namespace ecsco\ormbase\cache\local;

use ecsco\ormbase\cache\CacheAccess;
use ecsco\ormbase\cache\CacheInterface;
use ecsco\ormbase\exception\CacheException;
use ecsco\ormbase\traits\CallTrait;
use ecsco\ormbase\traits\GetTrait;
use ecsco\ormbase\traits\NoCloneTrait;
use ecsco\ormbase\traits\SingletonTrait;

/**
 * class Cache
 * @package    ecsco\ormbase
 * @subpackage CACHE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:04
 */
class Cache implements CacheInterface {

    use SingletonTrait;
    use GetTrait;
    use CallTrait;
    use NoCloneTrait;

    /**
     * @var array
     */
    protected $cache = [ ];

    /**
     * Has cached ?
     *
     * @param string
     * @param string
     *
     * @return boolean
     */
    final public function has( $cacheType, $identifier ): bool {

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
    final public function add( $cacheType, $identifier, $objects ): bool {

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
     * @param      $cacheType
     * @param bool $identifier
     *
     * @return array|mixed
     * @throws \ecsco\ormbase\exception\CacheException
     */
    final public function get( $cacheType, bool $identifier = false ) {

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
    final public function destroy( $cacheType, $identifier = false ): bool {

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
