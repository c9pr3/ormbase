<?php

namespace ecsco\ormbase\cache;

use ecsco\ormbase\cacheinterface\CacheInterface;
use ecsco\ormbase\config\Config;
use ecsco\ormbase\exception\CacheException;
use ecsco\ormbase\traits\CallTrait;
use ecsco\ormbase\traits\GetTrait;
use ecsco\ormbase\traits\NoCloneTrait;

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
     * @throws \ecsco\ormbase\exception\CacheException
     */
    final public static function getCacheInstance() {

        if ( self::$instance === null ) {

            $config = Config::getInstance();
            $cacheClass =
                ( $config->getItem( 'cache', 'cacheclass' ) ? $config->getItem( 'cache', 'cacheclass' ) : '\ecsco\ormbase\cache\local\Cache' );
            if ( !class_exists( $cacheClass ) ) {
                throw new CacheException( "Could not find caching class " . var_export( $cacheClass, true ) );
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $cache = $cacheClass::getInstance();
            if ( !( $cache instanceof CacheInterface ) ) {
                throw new CacheException( "Cacheclass $cacheClass must implement CacheInterface" );
            }

            self::$instance = $cache;
        }

        return self::$instance;
    }
}
