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
     * Construct
     *
     * @param array $config
     */
    public function __construct( $config = [ ] ) {

        $this->config = $config;
    }

    /**
     * Add an item
     *
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

        /*
        if ( isset( $section[ $sectionName ] ) && !is_array( $section[ $sectionName ] ) ) {
            throw new ConfigException( "Cannot overwrite " . $sectionName . "." );
        }
        */

        if ( !isset( $section[ $sectionName ] ) ) {
            $section[ $sectionName ] = [];
        }
        array_shift( $params );

        if ( count( $params ) > 1 ) {
            $this->parseParams( $section[ $sectionName ], $params );
        }
        else {
            $section[ $sectionName ] = $params[ 0 ];
        }

        return $section[ $sectionName ];
    }

    /**
     * hasItem
     *
     * @param ... $params
     * @return boolean
     */
    public function hasItem( ...$params ) {

        return ( $this->arrayPathValue( $this->config, implode( '/', $params ) ) !== null );
    }

    /**
     * Get value of an array by using "root/branch/leaf" notation
     * shamelessly borrowed from http://codeaid.net/php/get-values-of-multi-dimensional-arrays-using-xpath-notation

     *
*@param array  $array Array to traverse
     * @param string $path  Path to a specific option to extract
     *
     * @return mixed
     * @throws \ecsco\ormbase\exception\ConfigException
     */
    private function arrayPathValue(array $array, $path) {
        // specify the delimiter
        $delimiter = '/';

        if (empty($path)) {
            throw new ConfigException('Path cannot be empty');
        }

        $path = trim($path, $delimiter);
        $value = $array;
        $parts = explode($delimiter, $path);

        foreach ($parts as $part) {
            if (isset($value[$part])) {
                $value = $value[$part];
            } else {
                return null;
            }
        }

        return $value;
    }

    /**
     * Get Item
     *
     * @param ...$params
     *
     * @return mixed
     * @throws \ecsco\ormbase\exception\ConfigException
     */
    public function getItem( ...$params ) {

        $config = $this->config;

        $sectionOrValue = $this->arrayPathValue( $config, implode( '/', $params ) );

        if ( $sectionOrValue === null ) {
            throw new ConfigException( "Could not find " . implode( '/', $params ) . " in actual config");
        }

        if ( is_array( $sectionOrValue ) ) {
            return new self( $sectionOrValue );
        }

        return $sectionOrValue;
    }
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
