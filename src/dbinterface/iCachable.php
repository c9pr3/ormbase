<?php
/**
 * iCachable.php
 * @package    WPLIBS
 * @subpackage CACHE
 */

namespace wplibs\dbinterface;

/**
 * iCachable
 * @package    WPLIBS
 * @subpackage CACHE
 */
interface iCachable {

    /**
     * Get cache identifier
     * @return string
     */
    public static function getCacheIdentifier();
}
