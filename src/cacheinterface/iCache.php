<?php

namespace wplibs\cacheinterface;

interface iCache {

    /**
     * Get an instance
     * @return \wplibs\cacheinterface\iCache
     */
    public static function getInstance();

    /**
     * Has cached ?
     *
     * @param string
     * @param string
     *
     * @return boolean
     */
    public function has( $cacheType, $identifier );

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
    public function add( $cacheType, $identifier, $objects );

    /**
     * Get cached content
     *
     * @param      string
     * @param bool $identifier
     *
     * @return mixed
     */
    public function get( $cacheType, $identifier = false );

    /**
     * Destroy cached content
     *
     * @param                string
     * @param string|boolean default : false
     *
     * @return boolean
     */
    public function destroy( $cacheType, $identifier = false );

    /**
     * To Array
     * @return string[]
     */
    public function toArray();
}