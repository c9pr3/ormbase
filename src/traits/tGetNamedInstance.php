<?php

namespace wplibs\traits;

trait tGetNamedInstance {

	/**
	 * Get an instance
	 *
	 * @param string
	 * @return \wplibs\dbinterface\aContainer
	 * @author Christian Senkowski <cs@e-cs.co>
	 * @since 20140814 14:37
	 */
	final public static function getNamedInstance( $name ) {
		/** @noinspection PhpUndefinedFieldInspection */
		if ( self::$instances === null ) {
			/** @noinspection PhpUndefinedFieldInspection */
			self::$instances = [ ];
		}
		/** @noinspection PhpUndefinedFieldInspection */
		if ( isset( self::$instances[ $name ] ) ) {
			/** @noinspection PhpUndefinedFieldInspection */
			return self::$instances[ $name ];
		}

		return new self( $name );
	}
}
