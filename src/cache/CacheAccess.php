<?php

namespace wplibs\cache;

use wplibs\cacheinterface\iCache;
use wplibs\config\Config;
use wplibs\exception\CacheException;
use wplibs\traits\tGetInstance;

class CacheAccess {

    /**
     * @var array
     */
    public static $stats     = [ 'added' => 0, 'destroyed' => 0, 'provided' => 0 ];
    /**
     * @var int
     */
    public static $cacheTime = 30;
    /**
     * @var iCache
     */
    private $cacheClass = '\wplibs\cache\local\Cache';

    use tGetInstance;

    /**
     * Constructor
     * @throws \Exception
     * @throws \wplibs\exception\CacheException
     */
    protected function __construct() {

        if ( !$this->cacheClass ) {
            $config = Config::getInstance();
            $this->cacheClass =
                ( $config->getItem( 'cache', 'cacheclass' ) ? $config->getItem( 'cache', 'cacheclass' ) : $this->cacheClass );
            if ( !class_exists( $this->cacheClass ) ) {
                throw new CacheException( "Could not find caching class " . var_export( $this->cacheClass, true ) );
            }
        }

        $cacheClass = $this->cacheClass;
        $cache = $cacheClass::getInstance( $this );
        if ( !( $cache instanceof iCache ) ) {
            throw new CacheException( "Cacheclass $cacheClass must implement iCache" );
        }

        return $cache;
    }
}