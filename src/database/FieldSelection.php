<?php
/**
 * FieldSelection.php
 * @package    WPLIBS
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:06
 */

namespace wplibs\database;

use wplibs\traits\CallTrait;
use wplibs\traits\GetTrait;

/**
 * FieldSelection
 * @package    WPLIBS
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
