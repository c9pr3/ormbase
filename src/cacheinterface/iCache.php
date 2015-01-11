<?php

namespace wplibs\cacheinterface;

interface iCache {

    /**
     * Has cached ?
     *
     * @param string
     * @param string
     *
     * @return boolean
     */
    public static function has( $cacheType, $identifier );

    /**
     * Add content to cache
     *
     * @param $cacheType
     * @param $identifier
     * @param $objects
     *
     * @return bool
     * @throws \wplibs\exception\CacheException
     */
    public static function add( $cacheType, $identifier, $objects );

    /**
     * Get cached content
     *
     * @param      string
     * @param bool $identifier
     *
     * @return mixed
     */
    public static function get( $cacheType, $identifier = false );

    /**
     * Destroy cached content
     *
     * @param                string
     * @param string|boolean default : false
     *
     * @return boolean
     */
    public static function destroy( $cacheType, $identifier = false );

    /**
     * To Array
     * @return string[]
     */
    public static function toArray();
}