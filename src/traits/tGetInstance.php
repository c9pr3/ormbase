<?php

namespace wplibs\traits;

trait tGetInstance {

	private static $instance = null;

	/**
	 * Get an instance
	 *
	 * @return mixed
	 * @author Christian Senkowski <cs@e-cs.co>
	 * @since 20120823 15:42
	 */
	final public static function getInstance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
