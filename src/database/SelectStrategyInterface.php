<?php
/**
 * SelectStrategyInterface.php
 * @package    ecsco\ormbase
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:07
 */

declare(strict_types=1);

namespace ecsco\ormbase\database;

/**
 * SelectStrategyInterface
 * @package    ecsco\ormbase
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
