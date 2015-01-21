<?php
/**
 * CachableInterface.php
 * @package    ecsco\ormbase
 * @subpackage CACHE
 */

namespace ecsco\ormbase\dbinterface;

/**
 * CachableInterface
 * @package    ecsco\ormbase
 * @subpackage CACHE
 */
interface CachableInterface {

    /**
     * Get cache identifier
     * @return string
     */
    public static function getCacheIdentifier();
}
