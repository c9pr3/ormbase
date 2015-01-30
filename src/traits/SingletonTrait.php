<?php
/**
 * SingletonTrait.php
 * @package    ecsco\ormbase
 * @subpackage TRAITS
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20120823 15:42
 */

namespace ecsco\ormbase\traits;

use ecsco\ormbase\config\Config;

/**
 * SingletonTrait
 * @package    ecsco\ormbase
 * @subpackage TRAITS
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20120823 15:42
 */
trait SingletonTrait {

    /**
     * @var array
     */
    private static $instances = [ ];

    /**
     * Get an instance
     * @return mixed
     */
    final public static function getInstance() {

        if ( empty( self::$instances ) ) {
            self::$instances[ 'noname' ] = new self();
        }

        return self::$instances[ 'noname' ];
    }

    /**
     * Get named instance
     *
     * @param Config $dbConfig
     *
     * @return mixed
     */
    final public static function getNamedInstance( Config $dbConfig ) {

        $configName = md5( serialize( $dbConfig ) );
        if ( !isset( self::$instances[ $configName ] ) ) {
            self::$instances[ $configName ] = new self( $dbConfig );
        }

        return self::$instances[ $configName ];
    }
}
