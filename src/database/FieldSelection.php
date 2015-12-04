<?php
/**
 * FieldSelection.php
 * @package    ecsco\ormbase
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:06
 */

declare(strict_types=1);

namespace ecsco\ormbase\database;

use ecsco\ormbase\traits\CallTrait;
use ecsco\ormbase\traits\GetTrait;

/**
 * FieldSelection
 * @package    ecsco\ormbase
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:06
 */
class FieldSelection implements SelectStrategyInterface {

    use CallTrait;
    use GetTrait;

    /**
     * @var array
     */
    private $fields = [ ];

    /**
     * Construct
     *
     * @param ...$params
     *
     * @return FieldSelection
     */
    public function __construct( ...$params ) {

        $this->fields = $params;
    }

    /**
     * getFields
     * @return array
     */
    public function get() {

        return ( !empty( $this->fields ) ? $this->fields : [ '*' ] );
    }
}
