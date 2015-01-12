<?php

namespace wplibs\traits;

trait tGet {

    /**
     * Unknown API Get
     *
     * @param string
     *
     * @throws \Exception
     * @return string
     * @author Christian Senkowski <cs@e-cs.co>
     * @since  20140926 11:07
     */
    public function __get( $var ) {

        /** @noinspection PhpUndefinedMethodInspection */
        throw new \Exception( get_class( $this ) . ': Could not find attribute "' . $var );
    }
}
