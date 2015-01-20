<?php
/**
 * CachableInterface.php
 * @package    WPLIBS
 * @subpackage CACHE
 */

namespace wplibs\dbinterface;

/**
 * CachableInterface
 * @package    WPLIBS
 * @subpackage CACHE
 */
interface CachableInterface {

    /**
     * Get cache identifier
     * @return string
     */
    public static function getCacheIdentifier();
}
