<?php
/**
 * CallTrait.php
 * @package    WPLIBS
 * @subpackage TRAITS
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:11
 */

namespace wplibs\traits;

/**
 * CallTrait
 * @package    WPLIBS
 * @subpackage DBINTERFACE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:11
 */
trait CallTrait {

    /**
     * What to do if undefined method was called
     *
     * @param $functionName
     * @param $variableName
     *
     * @throws \Exception
     */
    final public function __call( $functionName, $variableName ) {

        throw new \Exception( 'Could not find ' .
                              $functionName .
                              ", called with $variableName in " .
                              get_called_class() );
    }
}
