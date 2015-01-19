<?php
/**
 * iSelectStrategy.php
 * @package    WPLIBS
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:07
 */

namespace wplibs\database;

/**
 * iSelectStrategy
 * @package    WPLIBS
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:07
 */
interface iSelectStrategy {

    /**
     * @param ...$params
     */
    public function __construct( ...$params );

    /**
     * @return array
     */
    public function get();
}
