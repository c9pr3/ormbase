<?php

namespace wplibs\cache;

use wplibs\cacheinterface\iCache;
use wplibs\config\Config;
use wplibs\exception\CacheException;

class CacheAccess {

    /**
     * @var array
     */
    public static $stats     = [ 'added' => 0, 'destroyed' => 0, 'provided' => 0 ];

    /**
     * @var iCache
     */
    private static $instance = null;

    /**
     * Constructor
     * @throws \Exception
     * @throws \wplibs\exception\CacheException
     */
    protected function __construct() {

    }

    /**
     * Get an instance
     * @return mixed
     * @throws \wplibs\exception\CacheException
     */
    final public static function getCacheInstance() {

        if ( self::$instance === null ) {

            $config = Config::getInstance();
            $cacheClass = ( $config->getItem( 'cache', 'cacheclass' ) ? $config->getItem( 'cache', 'cacheclass' ) : '\wplibs\cache\local\Cache' );
            if ( !class_exists( $cacheClass ) ) {
                throw new CacheException( "Could not find caching class " . var_export( $cacheClass, true ) );
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $cache = $cacheClass::getInstance();
            if ( !( $cache instanceof iCache ) ) {
                throw new CacheException( "Cacheclass $cacheClass must implement iCache" );
            }

            self::$instance = $cache;
        }

        return self::$instance;
    }
}
