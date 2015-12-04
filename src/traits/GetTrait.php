<?php
/**
 * GetTrait.php
 * @package    ecsco\ormbase
 * @subpackage TRAITS
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20140926 11:07
 */

declare(strict_types=1);

namespace ecsco\ormbase\traits;

/**
 * GetTrait
 * @package    ecsco\ormbase
 * @subpackage TRAITS
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20140926 11:07
 */
trait GetTrait {

    /**
     * What to do if unknown var?
     *
     * @param string
     *
     * @throws \Exception
     * @return string
     */
    final public function __get( $var ) {

        throw new \Exception( get_called_class() .
                              ': Could not find attribute "' .
                              var_export( $var, true ) .
                              ' in ' .
                              get_called_class() );
    }
}
