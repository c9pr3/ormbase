<?php
/**
 * class.Config.php
 * @package    wplibs
 * @subpackage CONFIG
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:05
 */

namespace wplibs\config;

use wplibs\database\DatabaseAccess;
use wplibs\exception\ConfigException;

/**
 * class Config
 * @package    wplibs
 * @subpackage CONFIG
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:05
 */
class Config {

    /**
     * @var \wplibs\database\iDatabase
     */
    public static  $cache      = true;
    private static $instances  = [ ];
    private static $config     = [ ];
    private        $configName = '';

    /**
     * Create new Config
     *
     * @param        $configName
     * @param string $section
     *
     * @throws ConfigException
     * @internal param $string
     * @internal param $string
     * @return Config
     */
    private function __construct( $configName, $section = '' ) {

        if ( !$configName ) {
            throw new ConfigException( "Missing configName" );
        }

        $this->configName = $configName . $section;

        $configFile = __DIR__ . '/../configfiles/' . $configName . '.config.php';
        if ( !isset( self::$config[ $this->configName ] ) ) {
            if ( !file_exists( $configFile ) ) {
                throw new ConfigException( "Could not find configFile '$configFile'" );
            }

            self::$config[ $configName ] = parse_ini_file( $configFile, true );
        }
        if ( $section ) {
            $section = strtoupper( $section );
            if ( !isset( self::$config[ $configName ][ $section ] ) ) {
                throw new ConfigException( "Could not find section '$section' in config '$configFile' -> " .
                                           var_export( self::$config[ $this->configName ],
                                                       true
                                           )
                );
            }

            self::$config[ $this->configName ] = self::$config[ $configName ][ $section ];
        }
    }

    /**
     * getDatabase
     *
     * @param string $forceBackend
     *
     * @return \wplibs\database\iDatabase
     */
    public function getDatabase( $forceBackend = 'mysql' ) {

        return DatabaseAccess::getDatabaseInstance( $this->getSection( 'database' ), $forceBackend );
    }

    /**
     * Get a specific section
     *
     * @param string
     *
     * @return Config
     */
    final public function getSection( $sectionName ) {

        $sectionName = strtoupper( $sectionName );
        if ( isset( self::$config[ $this->configName ][ $sectionName ] ) ) {
            return self::getNamedInstance( $this->getConfigName(), $sectionName );
        }

        return $this;
    }

    /**
     * Get an instance
     *
     * @param string
     * @param string
     *
     * @return Config
     */
    public static function getNamedInstance( $configName, $section = '' ) {

        if ( $section ) {
            $section = strtoupper( $section );
        }
        if ( !isset( self::$instances[ $configName . $section ] ) ) {
            self::$instances[ $configName . $section ] = new self( $configName, $section );
        }

        return self::$instances[ $configName . $section ];
    }

    /**
     * Get config name
     * @return string
     */
    public function getConfigName() {

        return $this->configName;
    }

    /**
     * Get a value
     *
     * @param string
     *
     * @throws \wplibs\exception\ConfigException
     * @return string
     */
    public function getValue( $key ) {

        if ( !$key ) {
            return null;
        }

        $key = strtolower( $key );

        if ( !isset( self::$config[ $this->configName ][ $key ] ) ) {
            throw new ConfigException( "Could not find key '$key' in config '" .
                                       $this->configName .
                                       "'" .
                                       var_export( self::$config[ $this->configName ],
                                                   true
                                       )
            );
        }

        return self::$config[ $this->configName ][ $key ];
    }
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
