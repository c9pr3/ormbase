<?php
/**
 * FieldSelection.php
 *
 * @package WPLIBS
 * @subpackage DATABASE
 * @author Christian Senkowski <cs@e-cs.co>
 * @since 20150106 14:06
 */

namespace wplibs\database;

/**
 * FieldSelection
 *
 * @package WPLIBS
 * @subpackage DATABASE
 * @author Christian Senkowski <cs@e-cs.co>
 * @since 20150106 14:06
 */
class FieldSelection implements iSelectStrategy {

	private $fields = [ ];

	/**
	 * Construct
	 *
	 * @param string []
	 * @return FieldSelection
	 */
	public function __construct( ...$params ) {
		$this->fields = $params;
	}

	/**
	 * getFields
	 *
	 * @return string[]
	 */
	public function get() {
		return ( !empty( $this->fields ) ? $this->fields : [ '*' ] );
	}
}
