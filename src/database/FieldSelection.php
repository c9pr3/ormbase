<?php
/**
 * FieldSelection.php
 * @package    WPLIBS
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:06
 */

namespace wplibs\database;
use wplibs\traits\tCall;
use wplibs\traits\tGet;

/**
 * FieldSelection
 * @package    WPLIBS
 * @subpackage DATABASE
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20150106 14:06
 */
class FieldSelection implements iSelectStrategy {

    use tCall;
    use tGet;

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
