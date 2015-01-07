<?php
/**
 * class.Cache.php
 *
 * @package wplibs
 * @subpackage CACHE
 * @author Christian Senkowski <cs@e-cs.co>
 * @since 20150106 14:04
 */

namespace wplibs\cache;

/**
 * class Cache
 *
 * @package wplibs
 * @subpackage CACHE
 * @author Christian Senkowski <cs@e-cs.co>
 * @since 20150106 14:04
 */
class Cache {

	public static $stats = [ 'added' => 0, 'destroyed' => 0, 'provided' => 0 ];
	private static $cacheTime = 30;
	private static $validKeys = [ 'contact', 'media', 'conversation_listener', 'conversation', 'geo_location' ];
	private static $instances = [ ];

	/**
	 * Has cached ?
	 *
	 * @param string
	 * @param string
	 * @return boolean
	 */
	final public static function has( $cacheType, $identifier ) {
		if ( !isset( self::$instances[ $cacheType ] ) ) {
			self::init();
		}
		$cache = self::$instances[ $cacheType ];
		$cache->get( md5( $identifier ) );
		if ( $cache->getResultCode() != \Memcached::RES_NOTFOUND ) {
			return true;
		}

		return false;
	}

	/**
	 * init
	 *
	 * @return void
	 */
	final public static function init() {
		foreach ( self::$validKeys AS $k ) {
			if ( isset( self::$instances[ $k ] ) ) {
				continue;
			}

			self::addInstance( $k );
		}
	}

	/**
	 * addInstance
	 *
	 * @param mixed $instanceName
	 * @return void
	 */
	private static function addInstance( $instanceName ) {
		self::$instances[ $instanceName ] = new \Memcached( $instanceName );
		self::$instances[ $instanceName ]->setOption( \Memcached::OPT_PREFIX_KEY, $instanceName );
		self::$instances[ $instanceName ]->setOption( \Memcached::OPT_BINARY_PROTOCOL, true );
		if ( !count( self::$instances[ $instanceName ]->getServerList() ) ) {
			self::$instances[ $instanceName ]->addServer( '127.0.0.1', 11211 );
		}
	}

	/**
	 * Add content to cache
	 *
	 * @param $cacheType
	 * @param $identifier
	 * @param $objects
	 * @throws \CacheException
	 * @return boolean
	 */
	public static function add( $cacheType, $identifier, $objects ) {
		$cache = self::$instances[ $cacheType ];

		$res = $cache->set( md5( $identifier ), $objects, self::$cacheTime );
		if ( $cache->getResultCode() == \Memcached::RES_NOTSTORED || !$res ) {
			throw new \wplibs\exception\CacheException( "Could not add memcached objects $identifier" );
		}

		self::$stats[ 'added' ]++;

		return true;
	}

	/**
	 * Get cached content
	 *
	 * @param string
	 * @param string
	 * @return mixed
	 */
	public static function get( $cacheType, $identifier ) {
		$cache = self::$instances[ $cacheType ];
		$r = $cache->get( md5( $identifier ) );
		if ( $cache->getResultCode() == \Memcached::RES_NOTFOUND ) {
			return [ ];
		}

		self::$stats[ 'provided' ]++;

		return $r;
	}

	/**
	 * Destroy cached content
	 *
	 * @param                string
	 * @param string|boolean default : false
	 * @return boolean
	 */
	public static function destroy( $cacheType, $identifier = false ) {
		$cache = self::$instances[ $cacheType ];
		if ( $identifier ) {
			$cache->delete( md5( $identifier ) );
		} else {
			if ( isset( self::$instances[ $cacheType ] ) ) {
				self::$instances[ $cacheType ]->flush();
			}
		}
		self::$stats[ 'destroyed' ]++;

		return true;
	}

	/**
	 * To Array
	 *
	 * @return string[]
	 */
	public static function toArray() {
		$rVal = [ ];
		if ( !self::$instances ) {
			return [ ];
		}

		foreach ( self::$validKeys AS $k ) {
			if ( !isset( self::$instances[ $k ] ) ) {
				continue;
			}
			$cache = self::$instances[ $k ];
			$keys = $cache->getAllKeys();
			if ( !$keys ) {
				continue;
			}
		}

		$rVal[ 'stats' ] = self::$stats;

		return $rVal;
	}
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
