<?php
/**
 * iSelectStrategy.php
 *
 * @package WPLIBS
 * @subpackage DATABASE
 * @author Christian Senkowski <cs@kon.de>
 * @since 20150106 14:07
 */

namespace wplibs\database;

/**
 * iSelectStrategy
 *
 * @package WPLIBS
 * @subpackage DATABASE
 * @author Christian Senkowski <cs@kon.de>
 * @since 20150106 14:07
 */
interface iSelectStrategy {

	public function __construct( ...$params );

	public function get();
}
