<?php
/**
 * SingletonTrait.php
 * @package    ecsco\ormbase
 * @subpackage TRAITS
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20120823 15:42
 */

namespace ecsco\ormbase\traits;

/**
 * SingletonTrait
 * @package    ecsco\ormbase
 * @subpackage TRAITS
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20120823 15:42
 */
trait SingletonTrait {

    /**
     * @var mixed
     */
    private static $instance = null;

    /**
     * Get an instance
     * @return mixed
     */
    final public static function getInstance() {

        if ( self::$instance === null ) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
