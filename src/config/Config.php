<?php
/**
 * Config.php
 * @package    ecsco\ormbase
 * @subpackage CONFIG
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:05
 */

namespace ecsco\ormbase\config;

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
class Config {

    use SingletonTrait;
    use CallTrait;
    use GetTrait;
    use NoCloneTrait;

    protected $config = [ ];

    /**
     * @param ...$params
     */
    public function addItem( ...$params ) {

        $this->parseParams( $this->config, $params );
    }

    /**
     * @param $section
     * @param $params
     *
     * @return array
     * @throws \ecsco\ormbase\exception\ConfigException
     */
    private function parseParams( &$section, $params ) {

        $sectionName = $params[ 0 ];

        if ( isset( $section[ $sectionName ] ) && !$section[ $sectionName ] instanceof ConfigSection ) {
            throw new ConfigException( "Cannot overwrite " . $sectionName . "." );
        }

        if ( !isset( $section[ $sectionName ] ) ) {
            $section[ $sectionName ] = new ConfigSection();
        }
        array_shift( $params );

        if ( count( $params ) > 1 ) {
            $this->parseParams( $section[ $sectionName ], $params );
        }
        else {
            $section[ $sectionName ] = new ConfigSection( $sectionName, $params );
        }

        return $section[ $sectionName ];
    }

    /**
     * @param ...$params
     *
     * @return mixed
     */
    public function getItem( ...$params ) {

        print_r( $this->config[ $params[ 0 ] ][ $params[ 1 ] ][ $params[ 2 ] ] );
    }
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
