<?php
/**
 * NoCloneTrait.php
 * @package    ecsco\ormbase
 * @subpackage TRAITS
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150119 16:33
 */

namespace ecsco\ormbase\traits;

/**
 * NoCloneTrait
 * @package    ecsco\ormbase
 * @subpackage TRAITS
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150119 16:33
 */
trait NoCloneTrait {

    /**
     * Dont allow cloning
     * @throws \Exception
     */
    final public function __clone() {

        throw new \Exception( 'Cloning not allowed in ' . get_called_class() );
    }
}
