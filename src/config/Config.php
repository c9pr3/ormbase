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
class Config extends \Packaged\Config\Provider\AbstractConfigProvider {

    /**
     * @var \wplibs\config\Config
     */
    private static $instance = null;

    /**
     * getDatabase
     *
     * @param string $forceBackend
     *
     * @return \wplibs\database\iDatabase
     */
    public function getDatabase( $forceBackend = 'mysql' ) {

        return DatabaseAccess::getDatabaseInstance( $this, $forceBackend );
    }

    /**
     * Get an instance
     *
     * @param string
     * @param string
     *
     * @return Config
     */
    public static function getInstance() {

        if ( self::$instance === null ) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
