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
     * @var int
     */
    public static $cacheTime = 30;
    /**
     * @var iCache
     */
    private $cacheClass = '\wplibs\cache\local\Cache';

    private static $instance = null;

    /**
     * Get an instance
     * @return mixed
     * @author Christian Senkowski <cs@e-cs.co>
     * @since  20120823 15:42
     */
    final public static function getInstance() {

        if ( self::$instance === null ) {

            $config = Config::getInstance();
            $cacheClass = ( $config->getItem( 'cache', 'cacheclass' ) ? $config->getItem( 'cache', 'cacheclass' ) : '\wplibs\cache\local\Cache' );
            if ( !class_exists( $cacheClass ) ) {
                throw new CacheException( "Could not find caching class " . var_export( $cacheClass, true ) );
            }

            $cache = $cacheClass::getInstance();
            if ( !( $cache instanceof iCache ) ) {
                throw new CacheException( "Cacheclass $cacheClass must implement iCache" );
            }

            self::$instance = $cache;
        }

        return self::$instance;
    }

    /**
     * Constructor
     * @throws \Exception
     * @throws \wplibs\exception\CacheException
     */
    protected function __construct() {

    }
}
