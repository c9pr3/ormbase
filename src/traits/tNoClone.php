<?php
/**
 * tNoClone.php
 * @package    WPLIBS
 * @subpackage TRAITS
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150119 16:33
 */

namespace wplibs\traits;

trait tNoClone {

    /**
     * Dont allow cloning
     * @throws \Exception
     */
    final public function __clone() {

        throw new \Exception( 'Cloning not allowed in ' . get_called_class() );
    }
}
