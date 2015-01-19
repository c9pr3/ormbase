<?php
/**
 * tCall.php
 * @package    WPLIBS
 * @subpackage TRAITS
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:11
 */

namespace wplibs\traits;

trait tCall {

    /**
     * What to do if undefined method was called
     *
     * @param $func
     * @param $var
     *
     * @throws \Exception
     */
    final public function __call( $func, $var ) {

        throw new \Exception( 'Could not find ' . $func . ' in ' . get_called_class() );
    }
}
