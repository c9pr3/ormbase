<?php
/**
 * MissingArguments.php
 * @package    wplibs
 * @subpackage EXCEPTIONS
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:10
 */

namespace wplibs\exception;

/**
 * MissingArgumentsException
 * @package    wplibs
 * @subpackage EXCEPTIONS
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:10
 */
class MissingArgumentsException extends \Exception {

    /**
     * Create new MissingArgumentsException
     *
     * @param string $methodName
     * @param int    $wantedProperty
     * @param array  $allArguments
     *
     * @return MissingArgumentsException
     */
    public function __construct( $methodName, $wantedProperty, $allArguments = [ ] ) {

        $methodName = preg_replace( '/^.*\\\/', '', $methodName );
        parent::__construct( "MissingArguments ('$wantedProperty') for '$methodName'" );
        #, given " . var_export( $allArguments, true ) );
    }
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
