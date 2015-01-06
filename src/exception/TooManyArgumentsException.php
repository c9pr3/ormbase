<?php
/**
 * exception.TooManyArgumnts.php
 *
 * @package wplibs
 * @subpackage EXCEPTIONS
 * @author Christian Senkowski <c.senkowski@kon.de>
 * @since 20150106 14:10
 */

namespace wplibs\exception;

/**
 * class TooManyArgumentsException
 *
 * @package wplibs
 * @subpackage EXCEPTIONS
 * @author Christian Senkowski <c.senkowski@kon.de>
 * @since 20150106 14:10
 */
class TooManyArgumentsException extends \Exception {

	/**
	 * Create new TooManyArgumentsException
	 *
	 * @param string           $methodName
	 * @param int              $unwantedProperty
	 * @param array|\Exception $allArguments
	 * @return TooManyArgumentsException
	 */
	public function __construct( $methodName, $unwantedProperty, $allArguments = [ ] ) {
		$methodName = preg_replace( '/^.*\\\/', '', $methodName );
		parent::__construct( "TooManyArguments ('$unwantedProperty') for '$methodName'" );
		#, given " . var_export( $allArguments, true ) );
	}
}

/**
 *  vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4 foldmethod=marker:
 */
