<?php
/**
 * tGet.php
 * @package    WPLIBS
 * @subpackage TRAITS
 * @author     Christian Senkowski <cs@e-cs.co>
 * @since      20140926 11:07
 */

namespace wplibs\traits;

trait tGet {

    /**
     * What to do if unknown var?
     *
     * @param string
     *
     * @throws \Exception
     * @return string
     */
    public function __get( $var ) {

        /** @noinspection PhpUndefinedMethodInspection */
        throw new \Exception( get_called_class() .
                              ': Could not find attribute "' .
                              $var .
                              ' in ' .
                              get_called_class() );
    }
}
