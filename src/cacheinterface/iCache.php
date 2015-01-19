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
     * @param $cacheType
     * @param $identifier
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
     * @param      $cacheType
     * @param bool $identifier
     *
     * @return mixed
     */
    public function get( $cacheType, $identifier = false );

    /**
     * Destroy cached content
     *
     * @param      $cacheType
     * @param bool $identifier
     *
     * @return bool
     */
    public function destroy( $cacheType, $identifier = false );

    /**
     * To Array
     * @return array
     */
    public function toArray();
}