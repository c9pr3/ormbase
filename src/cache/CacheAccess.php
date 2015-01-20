<?php

namespace wplibs\cache;

use wplibs\cacheinterface\CacheInterface;
use wplibs\config\Config;
use wplibs\exception\CacheException;
use wplibs\traits\CallTrait;
use wplibs\traits\GetTrait;
use wplibs\traits\NoCloneTrait;

class CacheAccess {

    use GetTrait;
    use CallTrait;
    use NoCloneTrait;

    /**
     * @var array
     */
    public static $stats = [ 'added' => 0, 'destroyed' => 0, 'provided' => 0 ];

    /**
     * @var CacheInterface
     */
    private static $instance = null;

    /**
     * Constructor
     */
    protected function __construct() {

    }

    /**
     * Get an instance
     * @return CacheInterface
     * @throws \wplibs\exception\CacheException
     */
    final public static function getCacheInstance() {

        if ( self::$instance === null ) {

            $config = Config::getInstance();
            $cacheClass =
                ( $config->getItem( 'cache', 'cacheclass' ) ? $config->getItem( 'cache', 'cacheclass' ) : '\wplibs\cache\local\Cache' );
            if ( !class_exists( $cacheClass ) ) {
                throw new CacheException( "Could not find caching class " . var_export( $cacheClass, true ) );
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $cache = $cacheClass::getInstance();
            if ( !( $cache instanceof CacheInterface ) ) {
                throw new CacheException( "Cacheclass $cacheClass must implement iCache" );
            }

            self::$instance = $cache;
        }

        return self::$instance;
    }
}
