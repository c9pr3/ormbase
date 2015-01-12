<?php

namespace wplibs\cacheinterface;

use wplibs\config\Config;
use wplibs\exception\CacheException;

class CacheAccess {

    public static $stats     = [ 'added' => 0, 'destroyed' => 0, 'provided' => 0 ];
    public static $cacheTime = 30;
    /**
     * @return string
     */
    private static $cacheClass = '';

    /**
     * getCache class name
     *
     * @param Config $config
     *
     * @return \wplibs\cacheinterface\iCache
     * @throws \wplibs\exception\CacheException
     */
    public static function getCache() {
        if ( !self::$cacheClass ) {
            $config = Config::getInstance();
            $cacheClass = ( $config->getItem('cache', 'cacheclass') ? $config->getItem('cache', 'cacheclass') : '\wplibs\cache\CacheLocal' );
            if ( !class_exists( $cacheClass ) ) {
                throw new CacheException("Could not find caching class $cacheClass");
            }
            self::$cacheClass = $cacheClass;
        }
        
        return self::$cacheClass;
    }
}
