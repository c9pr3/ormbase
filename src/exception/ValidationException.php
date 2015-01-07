<?php
/**
 * exception.ValidationException.php
 * @package    wplibs
 * @subpackage EXCEPTIONS
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:10
 */

namespace wplibs\exception;

/**
 * ValidationException
 * @package    wplibs
 * @subpackage EXCEPTIONS
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:10
 */
class ValidationException extends \Exception {

    /**
     * __construct
     *
     * @param mixed $methodName
     * @param mixed $unwantedProperty
     * @param mixed $allArguments
     *
     * @return ValidationException
     */
    public function __construct( $methodName, $unwantedProperty, $allArguments = [ ] ) {

        $methodName = preg_replace( '/^.*\\\/', '', $methodName );
        parent::__construct( "Validation failed. $unwantedProperty/$methodName" );
        #, given " . var_export( $allArguments, true ) );
    }
}
