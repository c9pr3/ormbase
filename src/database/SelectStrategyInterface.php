<?php
/**
 * SelectStrategyInterface.php
 * @package    WPLIBS
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:07
 */

namespace wplibs\database;

/**
 * SelectStrategyInterface
 * @package    WPLIBS
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:07
 */
interface SelectStrategyInterface {

    /**
     * @param ...$params
     */
    public function __construct( ...$params );

    /**
     * @return array
     */
    public function get();
}
