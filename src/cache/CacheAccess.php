<?php

namespace wplibs\cache;

use wplibs\config\Config;
use wplibs\exception\CacheException;

class CacheAccess {

    private static $cacheClass = '';
    public static  $stats     = [ 'added' => 0, 'destroyed' => 0, 'provided' => 0 ];
    public static $cacheTime = 30;

    /**
     * getCacheInstance
     *
     * @param Config $config
     * @return \wplibs\cache\aCache
     */
    public static function getCacheInstance( Config $config ) {
        $cacheClass = ( $config->getItem('cache', 'cacheclass') ? $config->getItem('cache', 'cacheclass') : '\wplibs\cache\CacheLocal' );
        if ( !class_exists( $cacheClass ) ) {
            throw new CacheException("Could not find caching class $cacheClass");
        }
        self::$cacheClass = $cacheClass;
    }

    /**
     * __callStatic
     *
     * @param mixed $func
     * @param mixed $args
     * @return mixed
     */
    public static function __callStatic($func, $args) {
        if ( !self::$cacheClass ) {
            self::getCacheInstance( Config::getInstance() );
        }

        $class = self::$cacheClass;
        return $class::$func( ...$args );
    }
}
