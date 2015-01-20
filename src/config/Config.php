<?php
/**
 * Config.php
 * @package    WPLIBS
 * @subpackage CONFIG
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:05
 */

namespace wplibs\config;

use Packaged\Config\Provider\ConfigProvider;
use wplibs\exception\ConfigException;
use wplibs\traits\CallTrait;
use wplibs\traits\GetTrait;
use wplibs\traits\NoCloneTrait;
use wplibs\traits\SingletonTrait;

/**
 * class Config
 * @package    WPLIBS
 * @subpackage CONFIG
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:05
 */
class Config extends ConfigProvider {

    use SingletonTrait;
    use CallTrait;
    use GetTrait;
    use NoCloneTrait;

    /**
     * @param string $name Name/Key of the configuration section
     *
     * @return ConfigSection
     * @throws \Exception
     */
    public function getSection( $name ) {

        if ( isset( $this->_sections[ $name ] ) ) {
            return new ConfigSection( $name, $this->_sections[ $name ]->getItems() );
        }
        throw new ConfigException( "Configuration section $name could not be found" );
    }
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
