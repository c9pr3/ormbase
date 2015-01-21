<?php
/**
 * Config.php
 * @package    ecsco\ormbase
 * @subpackage CONFIG
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:05
 */

namespace ecsco\ormbase\config;

use Packaged\Config\Provider\ConfigProvider;
use ecsco\ormbase\exception\ConfigException;
use ecsco\ormbase\traits\CallTrait;
use ecsco\ormbase\traits\GetTrait;
use ecsco\ormbase\traits\NoCloneTrait;
use ecsco\ormbase\traits\SingletonTrait;

/**
 * class Config
 * @package    ecsco\ormbase
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
